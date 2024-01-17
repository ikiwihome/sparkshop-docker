<?php
// +----------------------------------------------------------------------
// | SparkShop 坚持做优秀的商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2099 http://sparkshop.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai  <1902822973@qq.com>
// +----------------------------------------------------------------------

namespace app\index\service;

use app\model\goods\Goods;
use app\model\goods\GoodsCate;
use app\model\system\Article;
use app\model\system\WebsiteSlider;
use app\model\user\UserAgreement;

class HomeService
{
    /**
     * 首页数据
     * @return array
     */
    public function getHomeData(): array
    {
       // 幻灯
        $websiteModel = new WebsiteSlider();
        $flash = $websiteModel->getAllList(['position' => 2], '*', 'sort desc')['data'];
        foreach ($flash as $key => $vo) {
            $flash[$key]['pic_url'] = str_replace('\\', '/', $vo['pic_url']);
        }

        // 分类
        $cateModel = new GoodsCate();
        $cateList = $cateModel->getAllList([
            ['status', '=', 1]
        ], 'id,pid,name,icon', 'sort desc')['data'];

        // 精品推荐
        $goodsModel = new Goods();
        $recommendList = $goodsModel->getLimitList([
            ['is_recommend', '=', 1],
            ['is_del', '=', 2],
            ['is_show', '=', 1]
        ], 4, 'id,name,slider_image,sales,price,original_price')['data'];
        foreach ($recommendList as $key => $vo) {
            $recommendList[$key]['pic'] = json_decode($vo['slider_image'], true)['0'];
        }

        // 数码办公
        $pcIds = [2, 3, 4, 5];
        $pcList = $goodsModel->getLimitList([
            ['cate_id', 'in', $pcIds],
            ['is_del', '=', 2],
            ['is_show', '=', 1]
        ], 8, 'id,name,slider_image,collects,price,original_price')['data'];
        foreach ($pcList as $key => $vo) {
            $pcList[$key]['pic'] = json_decode($vo['slider_image'], true)['0'];
        }

        $seckillList = [];
        $seckillHour = 0;
        $endHour = 0;
        $seckillInstalled = hasInstalled('seckill');
        // 首页秒杀信息 TODO V1.1.3改造事件机制
        $res = $seckillInstalled ? event('SeckillHomeData') : [];
        if (!empty($res)) {
            $seckillList = $res[0]['data']['list'];
            $seckillHour = $res[0]['data']['seckillHour'];
            $endHour = $res[0]['data']['seckillHour'] + $res[0]['data']['continueHour'];
        }

        // 文章
        $article = new Article();
        $updateNews = $article->getLimitList(['cate_id' => 5], 6)['data'];
        $companyNews = $article->getLimitList(['cate_id' => 2], 6)['data'];
        $news = $article->getLimitList(['cate_id' => 1], 6)['data'];

        return dataReturn(0, 'success', [
            'flash' => $flash,
            'cate' => makeTree($cateList->toArray()),
            'recommend' => $recommendList,
            'pcList' => $pcList,
            'seckillList' => $seckillList,
            'seckillHour' => $seckillHour,
            'endHour' => date('Y-m-d ') . $endHour . ':00:00',
            'updateNews' => $updateNews,
            'companyNews' => $companyNews,
            'news' => $news
        ]);
    }

    /**
     * 获取用户协议
     * @param $type
     * @return array
     */
    public function getAgreement($type): array
    {
        $userAgreementModel = new UserAgreement();
        $info = $userAgreementModel->findOne([
            'type' => $type
        ], 'content')['data'];

        return dataReturn(0, 'success', $info);
    }
}