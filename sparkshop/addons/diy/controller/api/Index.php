<?php


namespace addons\diy\controller\api;

use app\BaseController;
use app\model\goods\Goods;
use app\model\goods\GoodsCate;
use app\model\system\Article;
use app\model\system\ArticleCate;
use app\model\system\WebsiteSlider;

class Index extends BaseController
{
    /**
     * diy时商品分类
     */
    public function getGoodsCate()
    {
        $goodsCateModel = new GoodsCate();
        $cate = $goodsCateModel->getAllList([
            'status' => 1
        ], 'id,pid,name', 'id asc')['data']->toArray();

        return jsonReturn(0, 'success', makeTree($cate));
    }

    /**
     * 商品列表
     */
    public function getGoodsList()
    {
        $param = input('param.');

        $where[] = ['is_del', '=', 2];
        $where[] = ['is_show', '=', 1];
        if (!empty($param['cate_id'])) {
            $where[] = ['cate_id', '=', $param['cate_id']];
        }

        $sort = 'id desc';
        if ($param['sortType'] == 'price') {
            $sort = 'price desc';
        } else if ($param['sortType'] == 'sales') {
            $sort = 'sales desc';
        }

        $list = (new Goods())->getLimitList($where, $param['num'], '*', $sort);
        foreach ($list['data'] as $key => $vo) {
            $list['data'][$key]['slider_image'] = json_decode($vo['slider_image'], true)[0];
        }

        return json($list);
    }

    /**
     * 获取幻灯片
     */
    public function getSlider()
    {
        $res = (new WebsiteSlider())->getAllList(['position' => input('param.position')]);
        return json($res);
    }

    /**
     * 获取初始化导航菜单
     */
    public function getMenu()
    {
        return json(dataReturn(0, 'success', [
            ['icon' => 'iconfont icon-fenlei', 'link' => '/pages/category/category', 'title' => '分类'],
            ['icon' => 'iconfont icon-shoucang', 'link' => '/pages/user/collection', 'title' => '收藏'],
            ['icon' => 'iconfont icon-dingdan', 'link' => '/pages/order/order?status=-1', 'title' => '订单']
        ]));
    }

    /**
     * 获取文章分类
     */
    public function getArticleCate()
    {
        $res = (new ArticleCate())->getAllList();
        return json($res);
    }

    /**
     * 获取文章
     */
    public function getArticle()
    {
        $param = input('param.');
        $where = [];
        if (!empty($param['cate_id'])) {
            $where[] = ['cate_id', '=', $param['cate_id']];
        }

        $res = (new Article())->getLimitList($where, $param['limit']);
        return json($res);
    }

    /**
     * 获取秒杀列表
     */
    public function getSeckillList()
    {
        $seckillList = [];
        $seckillHour = 0;
        $seckillInstalled = hasInstalled('seckill');
        // 首页秒杀信息
        $res = $seckillInstalled ? event('SeckillHomeData') : [];
        if (!empty($res)) {
            $seckillList = $res[0]['data']['list'];
            $seckillHour = $res[0]['data']['seckillHour'];
        }

        return jsonReturn(0, 'success', compact('seckillHour', 'seckillList'));
    }
}