<?php


return [

    // 权限跳过
    'skip_auth' => [
        'index/index' => 1,
        'index/home' => 1,
        'index/myApp' => 1,
        'comImageCate/index' => 1,
        'comImages/index' => 1,
        'articleCate/index' => 1,
        'attachment/uploadOtherFile' => 1,
        'attachment/uploadFile' => 1,
        'attachment/upload' => 1,
        'goodsCate/index' => 1,
        'goods/index' => 1,
        'article/index' => 1
    ],

    // 系统表不允许自动修改
    'sys_table' => [
        'admin_node' => 1,
        //'admin_role' => 1,
        'admin_user' => 1,
        //'com_images' => 1,
    ]
];