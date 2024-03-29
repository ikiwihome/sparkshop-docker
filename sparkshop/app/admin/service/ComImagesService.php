<?php

namespace app\admin\service;

use app\model\system\ComImages;
use strategy\store\StoreProvider;
use think\db\exception\DbException;

class ComImagesService
{
    /**
     * 获取图片列表
     * @param array $param
     * @return array
     * @throws DbException
     */
    public function getList(array $param): array
    {
        $limit = $param['limit'];
        $where = [];
        if (!empty($param['cate_id'])) {
            $where[] = ['cate_id', '=', $param['cate_id']];
        }

        $comImagesModel = new ComImages();
        $base = getConfByType('base');
        $list = $comImagesModel->where($where)->order('id desc')->paginate($limit)->each(function ($item) use ($base) {
            if ($base['website_url']) {
                $item->url = ((strstr($item->url, 'http://') != false) ||
                    (strstr($item->url, 'https://') != false)) ? $item->url : $base['website_url'] . $item->url;
            }

        });

        return dataReturn(0, 'success', $list);
    }

    /**
     * 删除图片
     * @param array $ids
     * @return array
     */
    public function delComImages(array $ids): array
    {
        $comImagesModel = new ComImages();
        // 去删除物理文件
        $files = $comImagesModel->getAllList([
            ['id', 'in', $ids]
        ])['data'];

        $otherStore = [];
        foreach ($files as $vo) {
            if ($vo['type'] == 'local') {
                @unlink($vo['path']);
            } else {
                $otherStore[$vo['type']][] = $vo;
            }
        }

        // 删除三方存储的内容
        if (!empty($otherStore)) {

            $storeConfigMap = config('shop.store_config');
            foreach ($otherStore as $key => $vo) {

                $config = getConfByType($storeConfigMap[$key]);
                $provider = new StoreProvider($key, $config);

                foreach ($vo as $v) {
                    $provider->getStrategy()->del($v['folder'] . '/' . pathinfo($v['url'])['basename']);
                }
            }
        }

        return $comImagesModel->delByIds($ids);
    }
}