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
namespace app\api\service;

use app\admin\validate\LoginValidate;
use app\api\validate\UserValidate;
use app\model\user\User;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use think\Exception;
use think\exception\ValidateException;
use think\facade\Db;
use utils\SparkTools;

class LoginService
{
    /**
     * 根据短信登录
     * @param array $param
     * @return array
     */
    public function doLoginBySms(array $param): array
    {
        if (empty($param['phone']) || empty($param['code'])) {
            return dataReturn(-1, '手机号或验证码不能为空');
        }

        // 检测验证码
        $code = cache($param['phone'] . '_' . SparkTools::SMS_LOGIN_KEY);
        if ($param['code'] != $code) {
            return dataReturn(-4, "验证码错误");
        }
        cache($param['phone'] . '_' . SparkTools::SMS_LOGIN_KEY, null);

        $userModel = new User();
        $userInfo = $userModel->findOne([
            'phone' => $param['phone']
        ])['data'];
        if (empty($userInfo)) {
            return dataReturn(-2, '手机号不存在');
        }

        if ($userInfo['status'] == 2) {
            return dataReturn(-3, '该账号已经禁用');
        }

        return $this->finalLoginReturn($userInfo);
    }

    /**
     * 根据账号密码登录
     * @param array $param
     * @return array
     */
    public function doLoginByAccount(array $param): array
    {
        if (empty($param['phone']) || empty($param['password'])) {
            return dataReturn(-1, '手机号或密码不能为空');
        }

        $userModel = new User();
        $userInfo = $userModel->findOne([
            'phone' => $param['phone']
        ])['data'];
        if (empty($userInfo)) {
            return dataReturn(-2, '用户名密码错误');
        }

        if ($userInfo['status'] == 2) {
            return dataReturn(-3, '该账号已经禁用');
        }

        // 比较密码
        if (makePassword($param['password']) != $userInfo['password']) {
            return dataReturn(-4, "用户名密码错误");
        }

        return $this->finalLoginReturn($userInfo);
    }

    /**
     * 用户注册
     * @param array $param
     * @return array
     */
    public function userReg(array $param): array
    {
        Db::startTrans();
        try {

            if (empty($param['phone']) || empty($param['password'])) {
                throw new Exception('手机号或密码不能为空', -1);
            }

            if (empty($param['code'])) {
                throw new Exception('验证码不能为空', -2);
            }

            // 检测验证码
            $code = cache($param['phone'] . '_' . SparkTools::SMS_REGISTER_KEY);

            if ($param['code'] != $code) {
                throw new Exception('验证码错误', -3);
            }

            cache($param['phone'] . '_' . SparkTools::SMS_REGISTER_KEY, null);

            $userModel = new User();
            $has = $userModel->field('id')->where('phone', $param['phone'])->find();

            if (!empty($has)) {
                throw new Exception('该用手机号已经注册，请直接登录。', -4);
            }

            $domain = getConfByType('base');
            $regParam = [
                'code' => uniqid(),
                'source_id' => 4,
                'nickname' => $param['phone'],
                'phone' => $param['phone'],
                'avatar' => $domain['website_url'] . '/static/admin/default/image/avatar.png',
                'password' => makePassword($param['password']),
                'register_time' => now(),
                'create_time' => now()
            ];

            $res = $userModel->insertOne($regParam);
            if ($res['code'] == 0) {
                $res['msg'] = '注册成功';
            }

            // 触发注册后事件
            $regParam['id'] = $res['data'];
            event('AfterReg', ['reg_param' => $param, 'user_param' => $regParam]);

            Db::commit();
            return $res;
        } catch (\Exception $e) {
            Db::rollback();
            return dataReturn($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 微信授权登录
     * @param array $param
     * @return array
     */
    public function loginByWechat(array $param): array
    {
        $code = $param['code'];
        $loginCode = $param['login_code'];
        $default = request()->domain() . '/static/admin/default/image/avatar.png';
        $avatar = (isset($param['avatar']) && !empty($param['avatar'])) ? $param['avatar'] : $default;

        try {

            $miniapp = getConfByType('miniapp');
            $res = $this->getPhoneNumberFromWx($code, $miniapp);
            if (!isset($res['phone_info'])) {
                return dataReturn(-11, '小程序配置错误', $res);
            }
        } catch (\Exception $e) {
            return dataReturn(-12, '小程序配置错误');
        }

        $phone = $res['phone_info']['phoneNumber'];

        $userModel = new User();
        $userInfo = $userModel->findOne([
            'phone' => $phone
        ])['data'];

        if (empty($userInfo)) {

            $config = [
                'app_id' => $miniapp['miniapp_app_id'],
                'secret' => $miniapp['miniapp_app_secret'],
            ];

            $app = Factory::miniProgram($config);
            $result = $app->auth->session($loginCode);
            // 根据openid
            if (!isset($result['openid'])) {
                return dataReturn(-1, '系统错误');
            }

            // 用openid注册用户
            $regParam = [
                'code' => uniqid(),
                'source_id' => 2, // 微信小程序
                'open_id' => $result['openid'],
                'nickname' => '微信用户',
                'phone' => $phone,
                'avatar' => $avatar,
                'password' => '',
                'register_time' => now(),
                'create_time' => now()
            ];
            $regUserData = $userModel->insertOne($regParam);

            $userInfo = [
                'id' => $regUserData['data'],
                'nickname' => '微信用户',
                'avatar' => $avatar,
            ];

            // 触发注册后事件
            $regParam['id'] = $regUserData['data'];
            event('AfterReg', ['reg_param' => $param, 'user_param' => $regParam]);
        }

        return $this->finalLoginReturn($userInfo);
    }

    /**
     * 统一登陆返回
     * @param $userInfo
     * @return array
     */
    protected function finalLoginReturn($userInfo)
    {
        $token = setJWT([
            'id' => $userInfo['id'],
            'name' => $userInfo['nickname'],
            'avatar' => $userInfo['avatar'],
        ]);

        return dataReturn(0, '登录成功', [
            'token' => (string)$token,
            'userInfo' => [
                'id' => $userInfo['id'],
                'name' => $userInfo['nickname'],
                'avatar' => $userInfo['avatar'],
            ]
        ]);
    }

    /**
     * 从微信拿手机号
     * @param string $code
     * @param array $appInfo
     * @param $refresh int
     * @return array|mixed
     * @throws InvalidConfigException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function getPhoneNumberFromWx(string $code, array $appInfo, int $refresh = 0)
    {
        $app = Factory::miniProgram([
            'app_id' => $appInfo['miniapp_app_id'],
            'secret' => $appInfo['miniapp_app_secret']
        ]);

        // 拿手机号
        $accessToken = $app->access_token;
        if ($refresh == 0) {
            $token = $accessToken->getToken(); // token 数组  token['access_token'] 字符串
        } else {
            $token = $accessToken->getToken(true); // 强刷token
        }
        $token = $token['access_token'];
        $url = "https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=$token";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['code' => $code]));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        curl_close($curl);

        $res = json_decode($res, true);
        if (isset($res['errcode']) && $res['errcode'] == 40001) { // token过期

            if ($refresh == 1) {
                return dataReturn(-1, '系统错误');
            }

            return $this->getPhoneNumberFromWx($code, $appInfo, 1);
        }

        return $res;
    }
}