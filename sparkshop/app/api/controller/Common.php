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
namespace app\api\controller;

use app\BaseController;
use think\facade\Filesystem;
use utils\SparkTools;

class Common extends BaseController
{
    public function initialize()
    {
        crossDomain();
    }

    /**
     * 发送登录短信
     */
    public function sendMsg()
    {
        $phone = input('post.phone');
        $type = input('post.type', SparkTools::SMS_REGISTER_KEY);

        if (false === in_array($type, SparkTools::ALLOWED_SMS_TYPES)) {
            return json(['code' => -1, 'data' => [], 'msg' => '短信类型错误']);
        }

        $res = SparkTools::sendSms(['phone' => $phone, 'type' => $type]);

        if ($res['code'] != 0) {
            return json($res);
        }

        $res['msg'] = '短信发送成功';
        return json($res);
    }

    /**
     * 上传
     */
    public function uploadFile()
    {
        $file = request()->file('file');
        
        // 上传到本地服务器
        try {

            // 存到本地
            $saveName = Filesystem::disk('public')->putFile('api', $file);
            $url = request()->domain() . '/storage/' . $saveName;
            $url = str_replace('\\', '/', $url);
            $storeWay = getConfByType('store')['store_way'];

            if ($storeWay != 'local') {
                $url = SparkTools::storeOSS($storeWay, $file, $saveName);
            }

        } catch (\Exception $e) {
            return json(['code' => -1, 'data' => [], 'msg' => $e->getMessage()]);
        }

        return json(['code' => 0, 'data' => ['url' => $url], 'msg' => 'upload success']);
    }

    /**
     * 图片转base64
     */
    public function changeImg()
    {
        $src = input('param.path');

        return json(SparkTools::image2Base64($src));
    }
}