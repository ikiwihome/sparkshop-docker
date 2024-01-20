<?php

namespace app\admin\service;


use app\model\order\OrderComment;
use think\db\exception\DbException;

class AppraiseService
{
    /**
     * 获取评价列表
     * @param array $param
     * @return array
     * @throws DbException
     */
    public function getList(array $param): array
    {
        $limit = $param['limit'];
        $type = $param['type'];
        $appraiseTime = $param['create_time'] ?? '';

        $where = [];
        if (!empty($type)) {
            $where[] = ['type', '=', $type];
        }

        if (!empty($appraiseTime)) {
            $where[] = ['create_time', 'between', [$appraiseTime[0] . ' 00:00:00', $appraiseTime[1] . ' 23:59:59']];
        }

        $appraise = config('shop.appraise');
        $commentModel = new OrderComment();
        $list = $commentModel->where($where)->order('id desc')->paginate($limit)->each(function ($item) use ($appraise) {
            $item->type = $appraise[$item->type];
        });

        return dataReturn(0, 'success', $list);
    }
}