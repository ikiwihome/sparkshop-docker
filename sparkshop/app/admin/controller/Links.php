<?php


namespace app\admin\controller;

use app\admin\service\LinksService;
use app\model\system\WebsiteLinks;
use think\facade\View;

class Links extends Base
{
    /**
    * 获取列表
    */
    public function index(LinksService $linksService)
    {
        if (request()->isAjax()) {

            return json($linksService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
    * 添加
    */
    public function add(LinksService $linksService)
    {
        if (request()->isPost()) {

            return json($linksService->addLinks(input('post.')));
        }

        return View::fetch();
    }

    /**
    * 编辑
    */
    public function edit(LinksService $linksService, WebsiteLinks $websiteLinksModel)
    {
         if (request()->isPost()) {

            return json($linksService->editLinks(input('post.')));
         }

         $id = input('param.id');
         View::assign([
            'info' => $websiteLinksModel->findOne([
                'id' => $id
            ])['data']
         ]);

         return View::fetch();
    }

    /**
    * 删除
    */
    public function del(WebsiteLinks $websiteLinksModel)
    {
        return json($websiteLinksModel->delById(input('param.id')));
   }
}

