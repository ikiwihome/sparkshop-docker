<?php


namespace addons\diy\controller\admin;

use app\model\goods\GoodsCate;
use app\model\system\Article;
use app\model\system\ArticleCate;
use app\model\system\WebsiteSlider;
use app\PluginBaseController;

class Api extends PluginBaseController
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

        $where = [];
        if (!empty($param['cate_id'])) {
            $where[] = ['cate_id', '=', $param['cate_id']];
        }

        $sort = 'id desc';
        if ($param['sortType'] == 'price') {
            $sort = 'price desc';
        } else if ($param['sortType'] == 'sales') {
            $sort = 'sales desc';
        }

        $list = (new \app\model\goods\Goods())->getLimitList($where, $param['num'], '*', $sort);
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
        $index = input('param.index');
        $diyInfo = getConfByType('diy')['miniapp_home_diy'];
        $diyInfo = json_decode($diyInfo, true);

        if (isset($diyInfo[$index])) {
            return json(dataReturn(0, 'success', $diyInfo[$index]['content']));
        }

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
}
