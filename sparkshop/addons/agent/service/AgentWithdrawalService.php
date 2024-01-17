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

namespace addons\agent\service;

use addons\agent\model\AgentUser;
use addons\agent\model\AgentWithdrawal;
use addons\agent\validate\ApplyValidate;
use think\Exception;
use think\exception\ValidateException;
use think\facade\Db;

class AgentWithdrawalService
{
    /**
     * 申请提现
     * @param array $param
     * @param int $type
     * @return array
     */
    public function apply(array $param, int $type = 1) : array
    {
        $scene = 'bank';
        if ($param['type'] != '银行卡') {
            $scene = 'other';
        }

        try {
            validate(ApplyValidate::class)->scene($scene)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        if ($type == 1) {
            $userInfo = getUserInfo();
        } else {
            $userInfo = getFrontUserInfo();
        }

        // 检测配置
        $config = getConfByType('agent');

        Db::startTrans();
        try {

            $agentUserModel = new AgentUser();
            $agentUserInfo = $agentUserModel->where('user_id', $userInfo['id'])->lock(true)->find();
            if (empty($agentUserInfo)) {
                throw new Exception('您不是分销员', -1);
            }

            if ($param['apply_amount'] > $agentUserInfo['residue_amount']) {
                throw new Exception('您最多可以提现' . $agentUserInfo['residue_amount'] . '元', -2);
            }

            if ($param['apply_amount'] < $config['min_apply_amount']) {
                throw new Exception('最低提现金额为' . $config['min_apply_amount'] . '元', -3);
            }

            $agentWithdrawalModel = new AgentWithdrawal();
            // 是否有正在申请的
            $hasApply = $agentWithdrawalModel->findOne(['user_id' => $userInfo['id'], 'status' => 1], 'id')['data'];
            if (!empty($hasApply)) {
                throw new Exception('您有一笔提现申请正在处理中，请勿重复申请', -4);
            }

            // 扣除提现金额
            $agentUserModel->where('user_id', $userInfo['id'])->dec('residue_amount', $param['apply_amount'])->update();

            // 写入提现申请表
            $param['status'] = 1;
            $param['create_time'] = now();
            $param['user_id'] = $userInfo['id'];

            $agentWithdrawalModel->insert($param);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return dataReturn($e->getCode(), $e->getMessage());
        }

        return dataReturn(0);
    }

    /**
     * 提下申请记录
     * @param array $param
     * @param int $type
     * @return array
     */
    public function withdrawalLog(array $param, int $type = 1) : array
    {
        if ($type == 1) {
            $userInfo = getUserInfo();
        } else {
            $userInfo = getFrontUserInfo();
        }

        $agentWithdrawalModel = new AgentWithdrawal();
        $field = 'id,type,user_id,card_no,name,bank,apply_amount,account,status,create_time';

        return $agentWithdrawalModel->getPageList($param['limit'], ['user_id' => $userInfo['id']], $field);
    }

    /**
     * 申请记录
     * @param array $param
     * @return array
     */
    public function getAdminWithdrawalLog(array $param) : array
    {
        $agentWithdrawalModel = new AgentWithdrawal();
        $where = [];
        if (!empty($param['status'])) {
            $where[] = ['status', '=', $param['status']];
        }

        if (!empty($param['user_id'])) {
            $where[] = ['user_id', '=', $param['user_id']];
        }

        if (!empty($param['date'])) {
            $where[] = ['create_time', '>=', $param['date'] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $param['date'] . ' 23:59:59'];
        }

        $list = $agentWithdrawalModel->with(['user'])->where($where)->paginate($param['limit']);
        return dataReturn(0, 'success', $list);
    }

    /**
     * 处理打款审核
     * @param array $param
     * @return array
     */
    public function dealWithdrawal(array $param) : array
    {
        if (empty($param['remark'])) {
            return dataReturn(-1, '请输入备注');
        }

        Db::startTrans();
        try {

            $agentWithdrawalModel = new AgentWithdrawal();
            $agentUserModel = new AgentUser();

            $info = $agentWithdrawalModel->where('id', $param['id'])->find();
            if (empty($info)) {
                throw new Exception('提现记录不存在', -1);
            }

            $userInfo = $agentUserModel->where('user_id', $info['user_id'])->lock(true)->find();
            // 同意打款
            if ($param['type'] == 1) {

                $agentUserModel->where('id', $userInfo['id'])
                    ->inc('draw_amount', $info['apply_amount'])
                    ->inc('draw_num', 1)
                    ->update();

                $agentWithdrawalModel->where('id', $info['id'])->update([
                    'status' => 2,
                    'desc' => $param['remark'],
                    'update_time' => now()
                ]);
            } else { // 拒绝打款

                $agentUserModel->where('id', $userInfo['id'])
                    ->inc('residue_amount', $info['apply_amount'])
                    ->update();

                $agentWithdrawalModel->where('id', $info['id'])->update([
                    'status' => 3,
                    'desc' => $param['remark'],
                    'update_time' => now()
                ]);
            }

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return dataReturn($e->getCode(), $e->getMessage());
        }

        return dataReturn(0, '审核成功');
    }
}