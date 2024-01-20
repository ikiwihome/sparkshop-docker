<?php


namespace addons\agent\service;

use addons\agent\model\AgentUser;
use addons\agent\validate\AgentSettingValidate;
use app\model\system\SysSetting;
use app\model\user\User;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Http\StreamResponse;
use think\db\exception\DbException;
use think\exception\ValidateException;
use think\facade\App;
use utils\SparkTools;

class AgentService
{
    /**
     * 保存设置
     * @param array $param
     * @return array
     * @throws DbException
     */
    public function saveSetting(array $param) : array
    {
        $type = $param['type'];
        unset($param['type']);

        // 分销模式校验
        if ($type == 1) {

            if (($param['agent_type'] == 3) && !is_numeric($param['agent_low_amount'])) {
                return dataReturn(-1, '满额分销最低金额必须是大于0的数字');
            }

            if (($param['agent_type'] == 4)) {

                if (empty($param['buy_designated_goods'])) {
                    return dataReturn(-2, '指定商品不能为空');
                }

                $param['buy_designated_goods'] = json_encode($param['buy_designated_goods']);
            }
        } else {

            try {

                validate(AgentSettingValidate::class)->scene('form' . $type)->check($param);
            } catch (ValidateException $e) {
                return dataReturn(-5, $e->getError());
            }

            if ($type == 3) {
                $param['apply_way'] = json_encode($param['apply_way']);
            }

            if ($type == 2) {
                // 开启自否返佣，则必须设置自购返佣比例
                if ($param['agent_self_buy'] == 1 && empty($param['self_buy_percent'])) {
                    return dataReturn(-10, '请设置自购返佣比例');
                }
            }
        }

        $sysSettingModel = new SysSetting();
        foreach ($param as $key => $vo) {
            $sysSettingModel->where('key', $key)->update([
                'value' => $vo
            ]);
        }

        return dataReturn(0, '保存成功');
    }

    /**
     * 生成海报
     * @param array $param
     * @return array
     */
    public function getQrcode(array $param): array
    {
        $appConfig = getConfByType('miniapp');
        $config = [
            'app_id' => $appConfig['miniapp_app_id'],
            'secret' => $appConfig['miniapp_app_secret']
        ];

        $app = Factory::miniProgram($config);

        $scene = 'id=' . $param['goods_id'] . '&spId=' . $param['spId'];
        $response = $app->app_code->getUnlimit($scene, [
            'width' => 220,
            'page'  => 'pages/product/product'
        ]);

        // 保存小程序码到文件
        if ($response instanceof StreamResponse) {
            $qrcodeSrc = App::getRootPath() . 'public' . DS . 'qrcode';
            $fileName = md5($scene) . '.png';
            $response->save($qrcodeSrc, $fileName);
            return dataReturn(0, 'success', request()->domain() . '/qrcode/' . $fileName);
        }

        if (isset($response['errcode'])) {
            return dataReturn(-5, '生成海报错误，请确认小程序是否发布');
        }

        return dataReturn(0);
    }

    /**
     * 获取代理员信息
     * @param int $type
     * @return array
     */
    public function getAgentInfo(int $type = 1) : array
    {
        if ($type == 1) {
            $userInfo = getUserInfo();
        } else {
            $userInfo = getFrontUserInfo();
        }

        $agentUserModel = new AgentUser();

        $info = $agentUserModel->field('id,user_id,residue_amount,draw_amount,level_id')
            ->with(['user', 'level'])->where('user_id', $userInfo['id'])->find();

        // 提现渠道
        $config = getConfByType('agent');
        $info['way'] = json_decode($config['apply_way'], true);

        return dataReturn(0, 'success', $info);
    }

    /**
     * 获取我推广的信息
     * @param array $param
     * @param int $type
     * @return array
     */
    public function getAgentUsers(array $param, int $type = 1) : array
    {
        if ($type == 1) {
            $userInfo = getUserInfo();
        } else {
            $userInfo = getFrontUserInfo();
        }

        $userModel = new User();
        $list = $userModel->field('avatar,id,nickname,phone,agent_bind_time')->where('parent_spread_id', $userInfo['id'])
            ->paginate($param['limit'])->each(function ($item) use($userModel) {
            return $item->agent_user_num = $userModel->where('parent_spread_id', $item->id)->count('id');
        });

        return dataReturn(0, 'success', $list);
    }
}