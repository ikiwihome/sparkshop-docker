<?php


namespace app\admin\controller;

use app\admin\service\UserService;
use app\model\user\User as UserModel;
use app\model\user\UserLabel;
use think\facade\View;

class User extends Base
{
    /**
     * 获取列表
     */
    public function index(UserService $userService, UserLabel $labelModel)
    {
        if (request()->isAjax()) {

            $list = $userService->getList(input('param.'))['data'];
            $baseMap = $userService->buildBaseParam();
            $labelList = $labelModel->getAllList([], 'id value,name', 'id asc')['data'];

            // 检测分销功能是否开启
            $hasInstallAgent = false;
            if (hasInstalled('agent')) {
                $hasInstallAgent = event('CheckAgentAppoint')[0]['data'];
            }

            return jsonReturn(0, 'success', [
                'list' => $list,
                'base' => $baseMap,
                'label' => $labelList,
                'agent' => $hasInstallAgent
            ]);
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(UserService $userService)
    {
        if (request()->isPost()) {

            return json($userService->addUser(input('post.')));
        }
    }

    /**
     * 编辑
     */
    public function edit(UserService $userService, UserModel $userModel)
    {
        if (request()->isPost()) {

            return json($userService->editUser(input('post.')));
        }

        $id = input('param.id');
        View::assign([
            'info' => $userModel->findOne([
                'id' => $id
            ])['data']
        ]);

        return View::fetch();
    }

    /**
     * 余额编辑
     */
    public function balance(UserService $userService)
    {
        return json($userService->changeBalance(input('post.')));
    }
}
