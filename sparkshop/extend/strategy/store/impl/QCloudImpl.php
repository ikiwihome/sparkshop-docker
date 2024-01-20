<?php

namespace strategy\store\impl;

use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Cos\CosAdapter;
use strategy\store\StoreInterface;

class QCloudImpl implements StoreInterface
{
    protected $qCloudObj = null;

    public function __construct($config)
    {
        $adapter = new CosAdapter([
            'region'      => $config['tencent_endpoint'],
            'credentials' => [
                'appId'      => $config['tencent_appid'], // 域名中数字部分
                'secretId'   => $config['secret_id'],
                'secretKey'  => $config['secret_key'],
            ],
            'bucket'          => $config['tencent_bucket'],
        ]);

        $this->qCloudObj = new Filesystem($adapter);
    }

    /**
     * 上传
     * @param $path
     * @param $file
     * @return array|mixed
     */
    public function upload($path, $file)
    {
        try {

            $config = [
                "Content-Type" => $file['type']
            ];
            $this->qCloudObj->write($path, file_get_contents($file['content']), $config);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '上传成功');
    }

    /**
     * 删除
     * @param $path
     * @return array|mixed
     */
    public function del($path)
    {
        try {

            $this->qCloudObj->delete($path);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '删除成功');
    }
}