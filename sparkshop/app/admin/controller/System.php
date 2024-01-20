<?php


namespace app\admin\controller;

use app\admin\service\SystemService;
use app\model\user\UserAgreement;
use think\facade\View;

class System extends Base
{
    /**
     * 基础配置
     */
    public function index(SystemService $systemService)
    {
        if (request()->isPost()) {

            $systemService->saveSystem(input('post.'));
            return jsonReturn(0, '保存成功');
        }

        if (request()->isAjax()) {
            return jsonReturn(0, 'success', [
                'base' => getConfByType('base'),
                'shop_base' => getConfByType('shop_base'),
                'shop_user_level' => getConfByType('shop_user_level'),
                'shop_refund' => getConfByType('shop_refund')
            ]);
        }

        return View::fetch();
    }

    /**
     * 短信配置
     */
    public function sms(SystemService $systemService)
    {
        if (request()->isPost()) {

            $systemService->saveSystem(input('post.'));
            return jsonReturn(0, '保存成功');
        }

        if (request()->isAjax()) {
            return jsonReturn(0, 'success', getConfByType('sms'));
        }

        return View::fetch();
    }

    /**
     * 支付配置
     */
    public function pay(SystemService $systemService)
    {
        if (request()->isPost()) {

            $param = input('post.');
            if (isset($param['file'])) {
                unset($param['file']);
            }

            $systemService->saveSystem($param);
            return jsonReturn(0, '保存成功');
        }

        if (request()->isAjax()) {
            return jsonReturn(0, 'success', [
                'alipay' => getConfByType('alipay'),
                'wechat' => getConfByType('wechat_pay'),
                'balance' => getConfByType('balance_pay')
            ]);
        }

        return View::fetch();
    }

    /**
     * 物流配置
     */
    public function express(SystemService $systemService)
    {
        if (request()->isPost()) {

            $systemService->saveSystem(input('post.'));
            return jsonReturn(0, '保存成功');
        }

        if (request()->isAjax()) {
            return jsonReturn(0, 'success', getConfByType('express'));
        }

        return View::fetch();
    }

    /**
     * 协议配置
     */
    public function agreement(UserAgreement $userAgreementModel)
    {
        if (request()->isPost()) {

            $param = input('post.');

            $has = $userAgreementModel->findOne([
                'type' => $param['type']
            ], 'id')['data'];

            if (!empty($has)) {
                $userAgreementModel->updateById([
                    'content' => $param['content'],
                    'update_time' => now()
                ], $has['id']);
            } else {
                $userAgreementModel->insertOne([
                    'type' => $param['type'],
                    'content' => $param['content'],
                    'create_time' => now()
                ]);
            }

            return jsonReturn(0 ,'保存成功');
        }

        if (request()->isAjax()) {

            $agreementList = $userAgreementModel->getAllList()['data'];

            $agreementMap = [
                1 => '',
                2 => ''
            ];

            if (!empty($agreementList)) {
                foreach ($agreementList as $vo) {
                    $agreementMap[$vo['type']] = $vo['content'];
                }
            }

            return jsonReturn(0, 'success', $agreementMap);
        }

        return View::fetch();
    }

    /**
     * 三方存储配置
     */
    public function store(SystemService $systemService)
    {
        if (request()->isPost()) {

            $systemService->saveSystem(input('post.'));
            return jsonReturn(0, '保存成功');
        }

        if (request()->isAjax()) {
            return jsonReturn(0, 'success', [
                'store' => getConfByType('store'),
                'aliyun' => getConfByType('store_oss'),
                'qiniu' => getConfByType('store_qiniu'),
                'qcloud' => getConfByType('store_tencent'),
            ]);
        }

        return View::fetch();
    }

    public function miniapp(SystemService $systemService)
    {
        if (request()->isPost()) {

            $systemService->saveSystem(input('post.'));
            return jsonReturn(0, '保存成功');
        }

        if (request()->isAjax()) {
            return jsonReturn(0, 'success', getConfByType('miniapp'));
        }

        return View::fetch();
    }
}