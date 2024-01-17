getBalanceList(0)
getTotalInfo()

function getBalanceList(type) {

    layui.use('table', function() {
        var table = layui.table;

        table.render({
            url: '/index/balanceLog/index',
            where: {
                type: type
            },
            parseData: function(res) {
                return {
                    "code": res.code,
                    "msg": res.msg,
                    "count": res.data.total,
                    "data": res.data.data
                };
            },
            elem: '#tableData',
            cols: [[
                {field: 'remark', title: '事项'},
                {field: 'create_time', title: '时间'},
                {field: 'balance', title: '变动金额'},
            ]],
            page: {
                limits: [10, 20, 30],
                limit: 10,
                count: 90
            },
            pagebar: '#table-page-bar'
        });
    });
}

function search() {
    getBalanceList($('#type').val())
}

function getTotalInfo() {
    $.getJSON('/index/balanceLog/getTotalInfo', function (res) {
        $('#money').text('￥ ' + res.data.balance)
    })
}

// 充值
let layerIndex;
function recharge() {
    layui.use('layer', function() {
        let layer = layui.layer;

        layerIndex = layer.open({
            type: 1,
            title: '余额充值',
            area: ['500px', '300px'],
            shade: 0.6,
            content: $('#recharge')
        });
    })
}

let index2;
let timer;
// 我已支付
$('.payed').click(function () {
    getBalanceList(0)
    clearInterval(timer)
    layer.close(index2)
});

layui.use(['form', 'layer'], function() {
    let form = layui.form;
    let layer = layui.layer;

    form.on('submit(order_submit)', function(data) {
        layer.close(layerIndex)
        let field = data.field;
        var index = layer.load(0, {shade: false});
        $.post('/index/balance/recharge', field, function (res) {
            if (res.code == 0) {
                layer.close(index)
                let title = '';
                if (field.pay_way == 'wechat_pay') {
                    title = '微信';
                } else {
                    title = '支付宝';
                }

                $('.goods-title').text('订单号：' + res.data.out_trade_no);
                $('.pay-box .header span').text(title);
                $('.order-price').text('￥' + field.amount);

                // 生成二维码
                $('#qrcode').html('')
                new QRCode(document.getElementById("qrcode"), {
                    text: res.data.qr_code,
                    width: 250,
                    height: 250,
                    correctLevel : QRCode.CorrectLevel.H
                });

                index2 = layer.open({
                    title: '',
                    closeBtn: 1,
                    id: 1,
                    type: 1,
                    area: ['550px', '550px'],
                    content: $('#pay-box'),
                    end : function() {
                        clearInterval(timer)
                    }
                });

                let nowStep = 0;
                let totalTimes = 30;
                timer = setInterval(function () {

                    nowStep += 1;
                    if (nowStep > totalTimes) {
                        clearInterval(timer);
                    }

                    $.getJSON('/index/balance/checkOrderStatus', {order: res.data.out_trade_no}, function (res2) {
                        if (res2.data.status != 1) {
                            layer.msg('支付完成');
                            getBalanceList(0)
                            clearInterval(timer)
                            layer.close(index2)
                        }
                    })
                }, 3000);

            } else {
                layer.msg(res.msg)
            }
        }, 'json');

        return false;
    });
});