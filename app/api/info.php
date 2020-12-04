<?php
return [
    'name' => 'api',
    'identifier' => 'api.module',// 模块唯一标识[必填]，格式：模块名.[应用市场ID].module.[应用市场分支ID]
    'theme' => '',
    'mobile_theme' => '',
    'title' => '接口',
    'intro' => '接口管理模块',
    'author' => 'hiphp',
    'icon' => '/static/m_api/images/app.png',
    'iconfont' => 'default/css/iconfont.css',
    'version' => '1.0.2',
    'author_url' => '',
    'module_depend'=>[],
    'tables'=>['api_app','api_controller','api_action','api_param','api_code','api_token'],
    'language'=>[],
    'db_prefix'=>'pre_',
    'config_icon'=>true,
    'config'=>[
        [
            'title'=>'基本',
            'url'=>url('module/setting', ['group'=>'api', 'tab'=>0]),
            'fields'=>[
                [
                    'name'=>'cache_expire',
                    'type'=>'input',
                    'title'=>'数据缓存',
                    'value'=>'1800',
                    'tips'=>'接口文档页面数据缓存时间',
                ],
                [
                    'name'=>'https',
                    'type'=>'radio',
                    'title'=>'HTTPS访问',
                    'value'=>0,
                    'tips'=>'所有接口强制HTTPS访问',
                    'options'=>'0:否;1:是'
                ],
                [
                    'name'=>'user_token_expire',
                    'type'=>'input',
                    'title'=>'Oauth令牌时效',
                    'value'=>2,
                    'tips'=>'会员Oauth令牌的TOKEN时效,单位:小时',
                ],
                [
                    'name'=>'jwt_expire',
                    'type'=>'input',
                    'title'=>'Jwt令牌时效',
                    'value'=>24,
                    'tips'=>'会员Jwt令牌的TOKEN时效,单位:小时',
                ],
                [
                    'name'=>'jwt_key',
                    'type'=>'input',
                    'title'=>'Jwt令牌秘钥',
                    'value'=>'hiphp',
                    'tips'=>'用于会员Jwt令牌生成和校验的秘钥',
                ],
                [
                    'name'=>'user_token_keep',
                    'type'=>'radio',
                    'title'=>'Oauth令牌续期',
                    'value'=>0,
                    'tips'=>'每次会员请求令牌校验通过后，失效时间会自动延长一个时效周期',
                    'options'=>'0:否;1:是'
                ],
                [
                    'name'=>'test_member',
                    'type'=>'input',
                    'title'=>'用户白名单',
                    'value'=>'',
                    'tips'=>'允许使用API文档测试工具的用户ID白名单, 用逗号隔开',
                ],
            ]
        ]
    ]
];