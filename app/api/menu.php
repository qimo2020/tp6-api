<?php
return [
    [
        'module'=>'api',
        'title'=>'接口',
        'icon'=>'icon iconfont iconjiekouweihu',
        'param'=>'',
        'url'=>'api/index/index',
        'create_time'=>time(),
        'childs'=>[
            [
                'module' => 'api',
                'title' => '应用管理',
                'icon' => 'icon iconfont iconapps',
                'param' => '',
                'url' => 'api/apps/index',
                'sort' => 0,
                'create_time' => time(),
            ],
            [
                'module' => 'api',
                'title' => '接口列表',
                'icon' => 'icon iconfont iconjiekouliebiao',
                'param' => '',
                'url' => 'api/controllers/index',
                'sort' => 0,
                'create_time' => time(),
            ]
        ]
    ]
];