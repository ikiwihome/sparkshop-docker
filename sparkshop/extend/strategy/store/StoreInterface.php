<?php

namespace strategy\store;

interface StoreInterface
{
    /**
     * 上传
     * @param $path
     * @param $file
     * @return mixed
     */
    public function upload($path, $file);

    /**
     * 删除
     * @param $path
     * @return mixed
     */
    public function del($path);
}