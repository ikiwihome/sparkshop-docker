<?php

require dirname(__FILE__, 2) . '/vendor/autoload.php';

$secretId = "SECRETID"; //替换为用户的 secretId，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
$secretKey = "SECRETKEY"; //替换为用户的 secretKey，请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi
$region = "ap-beijing"; //替换为用户的 region，已创建桶归属的region可以在控制台查看，https://console.cloud.tencent.com/cos5/bucket
$cosClient = new Qcloud\Cos\Client(
    array(
        'region' => $region,
        'scheme' => 'https', //协议头部，默认为http
        'credentials'=> array(
            'secretId'  => $secretId,
            'secretKey' => $secretKey)));
try {
    // 提交视频目标检测任务
    // start --------------- 使用模版 ----------------- //
    $result = $cosClient->createMediaTargetRecJobs(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Tag' => 'VideoTargetRec',
        'Input' => array(
            'Object' => 'test.mp4',
        ),
        'Operation' => array(
            'TemplateId' => '',
//            'UserData' => 'xxx',
//            'JobLevel' => '0',
        ),
//        'CallBack' => '',
//        'CallBackFormat' => '',
//        'CallBackType' => '',
//        'CallBackMqConfig' => array(
//            'MqRegion' => '',
//            'MqMode' => '',
//            'MqName' => '',
//        ),
    ));
    // 请求成功
    print_r($result);
    // end --------------- 使用模版 ----------------- //


    // start --------------- 自定义参数 ----------------- //
    $result = $cosClient->createMediaTargetRecJobs(array(
        'Bucket' => 'examplebucket-125000000', //存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
        'Tag' => 'VideoTargetRec',
        'Input' => array(
            'Object' => 'test.mp4',
        ),
        'Operation' => array(
//            'UserData' => 'xxx',
//            'JobLevel' => '0',
            'VideoTargetRec' => array(
                'Body' => 'true',
                'Pet' => 'true',
                'Car' => 'false',
            ),
        ),
//        'CallBack' => '',
//        'CallBackFormat' => '',
//        'CallBackType' => '',
//        'CallBackMqConfig' => array(
//            'MqRegion' => '',
//            'MqMode' => '',
//            'MqName' => '',
//        ),
    ));
    // 请求成功
    print_r($result);
    // end --------------- 自定义参数 ----------------- //
} catch (\Exception $e) {
    // 请求失败
    echo($e);
}
