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
namespace utils;

use app\model\system\AdminNode;
use app\model\system\SysSetting;
use app\model\user\User;
use strategy\sms\SmsProvider;
use strategy\store\StoreProvider;

class SparkTools
{
    // 注册的key
    public const SMS_REGISTER_KEY = 'reg_sms_code';
    // 登陆的key
    public const SMS_LOGIN_KEY = 'login_sms_code';
    // 找回密码的key
    public const SMS_FORGET_KEY = 'forget_sms_code';
    // 手机号绑定key
    public const SMS_MOBILE_BIND_KEY = 'bind_sms_code';
    // 通用短信模板类型
    public const SMS_COMMON_KEY = 'common_sms_code';

    public const ALLOWED_SMS_TYPES = [
        self::SMS_REGISTER_KEY,
        self::SMS_LOGIN_KEY,
        self::SMS_FORGET_KEY,
        self::SMS_MOBILE_BIND_KEY,
        self::SMS_COMMON_KEY
    ];

    public static function sendSms($param)
    {
        if (false === in_array($param['type'], self::ALLOWED_SMS_TYPES)) {
            return dataReturn(-1, '短信验证码类型错误');
        }

        if (empty($param['phone']) || empty($param['type'])) {
            return dataReturn(-2, '参数错误');
        }

        // 如果是登陆，校验号码是否存在
        if ($param['type'] == SparkTools::SMS_LOGIN_KEY) {
            $userInfo = (new User())->findOne(['phone' => $param['phone']])['data'];
            if (empty($userInfo)) {
                return dataReturn(-11, '您尚未注册，请先注册');
            }
        }

        $info = getConfByType('sms');

        $sms = [
            'access_key_id' => $info['access_key_id'],
            'access_key_secret' => $info['access_key_secret'],
            'sign_name' => $info['sign_name'],
            'templateCode' => $info[$param['type']],
            'phone' => $param['phone']
        ];

        $smsProvider = new SmsProvider('ali');
        $sendParam = formatSmsData($sms);
        $res = $smsProvider->getStrategy()->send($sendParam);

        if ($res['code'] == 0) {
            // 记录5分钟
            cache($param['phone'] . '_' . $param['type'], json_decode($sendParam['code'], true)['code'], 300);
        }

        return $res;
    }

    public static function getPayWay()
    {
        // 支付方式开启情况
        $sysSettingModel = new SysSetting();
        $payWayMap = $sysSettingModel->getOpenWay()['data'];
        $payWay = '';
        if (isset($payWayMap['wechat_pay']) && $payWayMap['wechat_pay'] == 1) {
            $payWay = 'wechat_pay';
        } else if (isset($payWayMap['alipay']) && $payWayMap['alipay'] == 1) {
            $payWay = 'alipay';
        } else if (isset($payWayMap['balance_pay']) && $payWayMap['balance_pay'] == 1) {
            $payWay = 'balance';
        }

        return compact('payWayMap', 'payWay');
    }

    /**
     * 第三方存储
     * @param $storeWay
     * @param $file
     * @param $saveName
     * @return string
     */
    public static function storeOSS($storeWay, $file, $saveName)
    {
        $storeConfigMap = config('shop.store_config');
        $config = getConfByType($storeConfigMap[$storeWay]);
        $provider = new StoreProvider($storeWay, $config);
        $fileArr = [
            'content' => app()->getRootPath() . 'public/storage/' . $saveName,
            'type' => $file->getMime()
        ];

        $provider->getStrategy()->upload($saveName, $fileArr);
        unlink(app()->getRootPath() . 'public/storage/' . $saveName);
        removeEmptyDir(dirname(app()->getRootPath() . 'public/storage/' . $saveName));
        $ossDomain = $config[config('shop.store_domain')[$storeWay]];
        if (strstr($ossDomain, 'http://') == false && strstr($ossDomain, 'https://') == false) {
            $ossDomain = 'https://' . $ossDomain;
        }

        return str_replace('\\', '/', $ossDomain . '/' . $saveName);
    }

     /**
     * 保持目录结构的压缩方法
     * @param string $zipFile
     * @param $folderPaths
     * @return void
     */
    public static function dirZip(string $zipFile, $folderPaths)
    {
        // 创建ZIP文件
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE) === true) {
            // 遍历目录中的文件和子目录
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folderPaths),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                // 排除目录本身
                if (!$file->isDir()) {
                    // 转换目录分隔符为正斜杠（/）
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($folderPaths) + 1);
                    $relativePath = str_replace('\\', '/', $relativePath);
                    // 添加文件到ZIP文件
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }
    }

    /**
     * 获得锁,如果锁被占用,阻塞,直到获得锁或者超时。
     * -- 1、如果 $timeout 参数为 0,则立即返回锁。
     * -- 2、建议 timeout 设置为 0,避免 redis 因为阻塞导致性能下降。请根据实际需求进行设置。
     *
     * @param string $key 缓存KEY。
     * @param int $timeout 取锁超时时间。单位(秒)。等于0,如果当前锁被占用,则立即返回失败。如果大于0,则反复尝试获取锁直到达到该超时时间。
     * @param int $lockSecond 锁定时间。单位(秒)。
     * @param int $sleep 取锁间隔时间。单位(微秒)。当锁为占用状态时。每隔多久尝试去取锁。默认 0.1 秒一次取锁。
     * @return bool 成功:true、失败:false
     * @throws \Exception
     */
    public static function lock(string $key, int $timeout = 0, int $lockSecond = 20, int $sleep = 100000): bool
    {
        $redis = getRedisHandler();
        if (strlen($key) === 0) {
            // 项目抛异常方法
            throw new \Exception(500, '缓存KEY没有设置');
        }
        $start = floatval(self::getMicroTime());
        do {
            // [1] 锁的 KEY 不存在时设置其值并把过期时间设置为指定的时间。锁的值并不重要。重要的是利用 Redis 的特性。
            $acquired = $redis->set("Lock:{$key}", 1, ['NX', 'EX' => $lockSecond]);
            if ($acquired) {
                break;
            }
            if ($timeout === 0) {
                break;
            }
            usleep($sleep);
        } while (!is_numeric($timeout) || (self::getMicroTime()) < ($start + ($timeout * 1000000)));

        return (bool)$acquired;
    }

    /**
     * 释放锁
     *
     * @param mixed $key 被加锁的KEY。
     * @return void
     * @throws \Exception
     */
    public static function release($key)
    {
        $redis = getRedisHandler();
        if (strlen($key) === 0) {
            // 项目抛异常方法
            throw new \Exception(500, '缓存KEY没有设置');
        }
        $redis->del("Lock:{$key}");
    }

    /**
     * 获取当前微秒。
     */
    public static function getMicroTime(): string
    {
        return bcmul(microtime(true), 1000000);
    }

    /**
     * lua减少库存
     */
    public static function luaDecStock($redis, $key, $val)
    {
        $script = <<<LUA
local stock = redis.call('get', KEYS[1])
stock = tonumber(stock)
local userBuyNum = tonumber(ARGV[1])
if (stock == 0) then
    return -1
elseif (stock < userBuyNum) then
    return -2
else
    redis.call('decrby', KEYS[1], ARGV[1])
    return 0
end
LUA;
        return $redis->eval($script, [$key, $val], 1);
    }

    /**
     * 获取插件的菜单json
     * @param $type
     * @return string
     */
    public static function getPluginMenuJson($type)
    {
        $menu = (new AdminNode())->getAllList(['type' => $type], 'id,pid,name,path,icon,is_menu,sort')['data']->toArray();
        return json_encode(makeTree($menu));
    }

    /**
     * 图片转base64
     * @param string $src
     * @return array
     */
    public static function image2Base64(string $src) : array
    {
        $type = getimagesize($src); // 取得图片的大小，类型等
        $content = file_get_contents($src);
        // 已经将图片转成base64编码
        $file_content = base64_encode($content);

        $imgType = '';
        switch ($type[2]) {//判读图片类型
            case 1:
                $imgType = "gif";
                break;
            case 2:
                $imgType = "jpg";
                break;
            case 3:
                $imgType = "png";
                break;
        }

        // 合成图片的base64编码,方便在页面中展示
        $img = 'data:image/' . $imgType . ';base64,' . $file_content;

        return dataReturn(0, 'success', $img);
    }
}