<?php

namespace app\admin\service;

use app\admin\validate\UserValidate;
use app\model\user\User;
use app\model\user\UserBalanceLog;
use app\model\user\UserGroup;
use app\model\user\UserLabelRelation;
use app\model\user\UserLevel;
use think\db\exception\DbException;
use think\exception\ValidateException;
use think\facade\Db;
use think\response\Json;

class UserService
{
    /**
     * 获取用户列表
     * @param array $param
     * @return array|Json
     * @throws DbException
     */
    public function getList(array $param)
    {
        $limit = $param['limit'];

        $res = $this->filterParam($param);
        if ($res['code'] != 0) {
            return json($res);
        }

        $where = $this->buildWhere($param);

        $userModel = new User();
        $list = $userModel->with([
            'group'
        ])->where($where)->order('id desc')->paginate($limit);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 创建用户
     * @param array $param
     * @return array
     */
    public function addUser(array $param): array
    {
        if (isset($param['file'])) {
            unset($param['file']);
        }

        // 检验完整性
        try {
            validate(UserValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $userModel = new User();
        $has = $userModel->checkUnique([
            'phone' => $param['phone']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '手机号已经存在');
        }

        $param['create_time'] = now();
        $param['code'] = uniqid();
        $param['password'] = makePassword($param['password']);
        return $userModel->insertOne($param);
    }

    /**
     * 编辑用户
     * @param array $param
     * @return array
     */
    public function editUser(array $param): array
    {
        if (isset($param['file'])) {
            unset($param['file']);
        }

        // 检验完整性
        try {
            validate(UserValidate::class)->scene('edit')->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $userModel = new User();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['phone', '=', $param['phone']];
        $has = $userModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '手机号已经存在');
        }

        if (!empty($param['password'])) {
            $param['password'] = makePassword($param['password']);
        } else {
            unset($param['password']);
        }

        return $userModel->updateById($param, $param['id']);
    }

    /**
     * 更改余额
     * @param array $param
     * @return array
     */
    public function changeBalance(array $param): array
    {
        if ($param['balance'] <= 0) {
            return dataReturn(-5, '余额值应大于0');
        }

        Db::startTrans();
        try {

            $userModel = new User();
            $userInfo = $userModel->where('id', $param['user_id'])->lock(true)->find();

            // 用户拥有的余额小于减去的金额，则减去余额
            if ($userInfo['balance'] < $param['balance'] && $param['type'] == 2) {
                $param['balance'] = $userInfo['balance'];
            }

            $logParam = [
                'type' => 3,
                'user_id' => $param['user_id'],
                'balance' => ($param['type'] == 1) ? $param['balance'] : (0 - $param['balance']),
                'remark' => '后台修改余额',
                'create_time' => now()
            ];
            (new UserBalanceLog())->insert($logParam);

            if ($param['type'] == 1) {
                $userModel->where('id', $param['user_id'])->inc('balance', $param['balance'])->update();
            } else {
                $userModel->where('id', $param['user_id'])->dec('balance', $param['balance'])->update();
            }

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return dataReturn(-2, $e->getMessage());
        }

        return dataReturn(0, '设置成功');
    }

    /**
     * 构建基础信息
     * @return array
     */
    public function buildBaseParam(): array
    {
        $userLevelModel = new UserLevel();
        $levelList = $userLevelModel->getAllList([], 'id,title,level', 'id asc')['data'];

        $userGroupModel = new UserGroup();
        $groupList = $userGroupModel->getAllList([], 'id,name', 'id asc')['data'];

        return [
            'level' => $levelList,
            'group' => $groupList,
            'source' => config('shop.source')
        ];
    }

    private function buildWhere(array $param): array
    {
        $where = [];

        // 注册来路
        if (!empty($param['source_id'])) {
            $where[] = ['source_id', '=', $param['source_id']];
        }

        // 等级
        if (!empty($param['vip_level'])) {
            $where[] = ['vip_level', '=', $param['vip_level']];
        }

        // 用户分组
        if (!empty($param['group_id'])) {
            $where[] = ['group_id', '=', $param['group_id']];
        }

        // 用户标签
        if (!empty($param['label_id'])) {
             $userLabelRelationModel = new UserLabelRelation();
            $userList = $userLabelRelationModel->field('user_id')->whereIn('label_id', $param['label_id'])->select();
            
            $userIds = [];
            foreach ($userList as $vo) {
                $userIds[] = $vo['user_id'];
            }

            $where[] = ['id', 'in', $userIds];
        }

        // 手机号
        if (!empty($param['phone'])) {
            $where[] = ['phone', '=', $param['phone']];
        }

        // 昵称
        if (!empty($param['nickname'])) {
            $where[] = ['nickname', '=', $param['nickname']];
        }

        // 成交次数
        if (!empty($param['start_total_times']) && !empty($param['end_total_times'])) {
            $where[] = ['total_times', 'between', [$param['start_total_times'], $param['end_total_times']]];
        }

        // 消费金额
        if (!empty($param['start_total_spend']) && !empty($param['end_total_spend'])) {
            $where[] = ['total_spend', 'between', [$param['start_total_spend'], $param['end_total_spend']]];
        }

        // 注册时间
        if (!empty($param['register_time'])) {
            $where[] = ['register_time', 'between', [$param['register_time'][0], $param['register_time'][1]]];
        }

        // 上次访问时间
        if (!empty($param['last_visit_time'])) {
            $where[] = ['last_visit_time', 'between', [$param['last_visit_time'][0], $param['last_visit_time'][1]]];
        }

        return $where;
    }

    private function filterParam(array $param): array
    {
        $res = $this->checkRangeCondition($param['start_total_times'], $param['end_total_times'], '成交次数');
        if ($res['code'] != 0) {
            return $res;
        }

        $res = $this->checkRangeCondition($param['start_total_spend'], $param['end_total_spend'], '消费金额');
        if ($res['code'] != 0) {
            return $res;
        }

        return dataReturn(0);
    }

    private function checkRangeCondition($start, $end, $notice): array
    {
        if (($start !== '' && $end === '') ||
            ($start === '' && $end !== '')) {
            return dataReturn(-1, $notice . '范围有误');
        }

        if (($start !== '' && $end !== '') &&
            ($start >= $end)) {
            return dataReturn(-2, $notice . '范围有误');
        }

        return dataReturn(0);
    }
}