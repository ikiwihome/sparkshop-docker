<?php

namespace app\service;

use app\model\order\Order;
use app\model\order\OrderRefund;
use app\model\system\Plugins;
use app\model\user\User;
use app\model\user\UserCollection;
use app\model\user\UserIntegralLog;
use app\model\user\UserLevel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class UserService
{
    /**
     * 个人信息
     * @param array $param
     * @param int $userId
     * @return array
     */
    public function personalData(array $param, int $userId): array
    {
        $param['update_time'] = now();

        if (isset($param['phone'])) {
            if (empty($param['phone']) || empty($param['code'])) {
                return dataReturn(-1, "手机号或验证码不能为空");
            }

            // 检测验证码
            $code = cache($param['phone'] . '_reg_sms');
            if ($param['code'] != $code) {
                return dataReturn(-3, "验证码错误");
            }
            cache($param['phone'] . '_reg_sms', null);
            unset($param['code']);
        }

        $userModel = new User();
        return $userModel->updateById($param, $userId);
    }

    /**
     * 获取用户基础信息
     * @param int $id
     * @return array
     */
    public function getUserInfo(int $id): array
    {
        // 用户基础信息
        $userModel = new User();
        $userInfo = $userModel->findOne([
            'id' => $id
        ], 'id,code,open_id,nickname,avatar,vip_level,experience,balance,integral,phone')['data'];
        $version = config('version.version');

        return dataReturn(0, 'success', compact('userInfo', 'version'));
    }

    /**
     * 更新用户信息
     * @param array $param
     * @return array
     */
    public function updateInfo(array $param): array
    {
        if (empty($param['avatar'])) {
            return dataReturn(-1, '头像不能为空');
        }

        if (empty($param['nickname'])) {
            return dataReturn(-2, '昵称不能为空');
        }

        if (mb_strlen($param['nickname']) > 16) {
            return dataReturn(-3, '昵称不能大于16个字符');
        }

        $userInfo = getUserInfo();

        return (new User())->updateById([
            'avatar' => $param['avatar'],
            'nickname' => $param['nickname']
        ], $userInfo['id']);
    }

    /**
     * 获取我的基础信息
     * @param int $userId
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getMyBaseInfo(int $userId): array
    {
        // 用户基础信息
        $userModel = new User();
        $userInfo = $userModel->findOne([
            'id' => $userId
        ], 'id,code,open_id,nickname,avatar,vip_level,experience,balance,integral,phone,is_agent')['data'];
        if (empty($userInfo)) {
            return dataReturn(-11, '用户信息有误');
        }

        // 会员等级折扣
        $vipConf = getConfByType('shop_user_level');
        $config['userVip'] = ($vipConf['user_level_open'] == 1) ? 1 : 0;

        // 优惠券是否开启
        $pluginsModel = new Plugins();
        $couponConfig = $pluginsModel->findOne([
            'name' => 'coupon'
        ])['data'];

        $orderNum = 0;
        if (empty($couponConfig)) {
            $config['coupon'] = 0;
            $orderNum = (new Order())->whereIn('status', [3, 4, 5, 6])->where([
                'user_id' => $userId,
                'is_del' => 1,
                'user_del' => 1
            ])->where('pid', '>=', 0)->count('id');
        } else {
            $config['coupon'] = ($couponConfig['status'] == 2) ? 1 : 0;
        }

        // TODO 积分
        $config['integral'] = 0;
        $userCollection = 0;
        // 数据补充
        if ($config['integral'] == 0) {
            // 查收藏的数量
            $userCollection = (new UserCollection())->where('user_id', $userId)->count('id');
        }

        // 用户等级基础信息
        $useLevelModel = new UserLevel();
        $nextExperience = $useLevelModel->field('experience')->where('status', 1)->order('level asc')->find();
        if ($userInfo['vip_level'] == 0) {
            $levelInfo = [
                'level' => 0,
                'next_experience' => empty($nextExperience) ? 0 : $nextExperience['experience']
            ];
        } else {
            $levelList = $useLevelModel->getAllList([
                'status' => 1
            ], '*', 'level asc')['data'];

            // 下一等级经验值
            $levelInfo = [];
            $nowKey = 0;
            foreach ($levelList as $key => $vo) {
                if ($vo['level'] == $userInfo['vip_level']) {
                    $levelInfo = $vo;
                    $nowKey = $key;
                    break;
                }
            }

            if ((count($levelList) - 1) == $nowKey) {
                $levelInfo['next_experience'] = $levelList[$nowKey]['experience'];
            } else {
                $levelInfo['next_experience'] = $levelList[$nowKey + 1]['experience'];
            }
        }

        // 待处理数据
        $orderModel = new Order();
        $unPaid = $this->getOrderNumByStatus($userId, $orderModel, ['status' => 2]);
        $unExpress = $this->getOrderNumByStatus($userId, $orderModel, ['status' => 3]);
        $unReceive = $this->getOrderNumByStatus($userId, $orderModel, ['status' => 4]);
        $unAppraise = $this->getOrderNumByStatus($userId, $orderModel, [
            ['status', '=', 6],
            ['user_comments', '=', 1]
        ]);

        $orderRefundModel = new OrderRefund();
        $refund = $orderRefundModel->where('user_id', $userId)->where('status', 1)->count('id');

        $version = config('version.version');

        $orderBar = compact('unPaid', 'unExpress', 'unReceive', 'unAppraise', 'refund');
        $data = compact('userInfo', 'config', 'levelInfo', 'orderBar', 'userCollection', 'orderNum', 'version');
        return dataReturn(0, 'success', $data);
    }

    protected function getOrderNumByStatus(int $userId, Order $orderModel, array $where)
    {
        return  $orderModel->where($where)->where([
            'user_id' => $userId,
            'is_del' => 1,
            'user_del' => 1
        ])->where('pid', '>=', 0)->count('id');
    }

    /**
     * 改变手机号
     * @param array $param
     * @return array
     */
    public function changePhone(array $param): array
    {
        if (empty($param['new_phone'])) {
            return dataReturn(-1, '请输入新手机号');
        }

        if (empty($param['code'])) {
            return dataReturn(-2, '请输入验证码');
        }

        // 检测验证码
        $code = cache($param['new_phone'] . '_' . $param['type']);
        if ($param['code'] != $code) {
            return dataReturn(-3, "验证码错误");
        }
        cache($param['new_phone'] . '_' . $param['type'], null);

        $userModel = new User();
        return $userModel->updateById([
            'phone' => $param['new_phone'],
            'update_time' => now()
        ], $param['user_id']);
    }

    /**
     * 改变密码
     * @param array $param
     * @return array
     */
    public function changePassword(array $param): array
    {
        if (empty($param['old_pwd'])) {
            return dataReturn(-1, '请输入旧密码');
        }

        if (empty($param['new_pwd'])) {
            return dataReturn(-2, '请输入新密码');
        }

        if (empty($param['re_pwd'])) {
            return dataReturn(-3, '请再次输入密码');
        }

        if ($param['new_pwd'] != $param['re_pwd']) {
            return dataReturn(-4, '两次输入不一致');
        }

        $userModel = new User();
        $userInfo = $userModel->findOne(['id' => $param['user_id']])['data'];

        if (makePassword($param['old_pwd']) != $userInfo['password']) {
            return dataReturn(-5, '旧密码错误');
        }

        return $userModel->updateById([
            'password' => makePassword($param['new_pwd']),
            'update_time' => now()
        ], $param['user_id']);
    }
}