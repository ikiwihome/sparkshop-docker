<?php


namespace app\model\system;

use app\model\BaseModel;

class ComImages extends BaseModel
{
    /**
     * 检测文件是否存在
     * @param $sha1
     * @return array
     */
    public function checkImageExist($sha1)
    {
        try {

            $has = $this->where('sha1', $sha1)->find();
            if (!empty($has)) {
                return ['code' => 203, 'data' => $has, 'msg' => '该图片已经存在了'];
            }
        } catch (\Exception $e) {
            return ['code' => -1, 'data' => 0, 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'data' => [], 'msg' => 'success'];
    }
}

