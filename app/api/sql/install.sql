CREATE TABLE `pre_api_app` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT '' COMMENT '应用名称',
  `name` varchar(100) DEFAULT '' COMMENT '应用名',
  `app_id` varchar(30) DEFAULT '',
  `app_secret` varchar(50) DEFAULT '',
  `callback_uri` varchar(60) DEFAULT '' COMMENT '授权回调地址',
  `white_list` text DEFAULT '' COMMENT 'IP白名单',
  `app_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '应用类型:0模块1插件',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `version` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `app_id` (`app_id`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='应用管理';

CREATE TABLE `pre_api_controller` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '应用id',
  `title` varchar(60) DEFAULT '' COMMENT '控制器名称',
  `name` varchar(30) DEFAULT '' COMMENT '控制器名',
  `map_name` varchar(30) DEFAULT '' COMMENT '接口映射名',
  `version` varchar(10) DEFAULT '' COMMENT '版本号',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序[文档排序]',
  `restful` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '生成默认restful操作方法',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='接口控制器列表';

CREATE TABLE `pre_api_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '控制器id',
  `title` varchar(60) DEFAULT '' COMMENT '方法名称',
  `name` varchar(60) DEFAULT '' COMMENT '方法名',
  `request_type` varchar(10) DEFAULT '' COMMENT '请求类型',
  `api_auth` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否需要接口令牌',
  `user_auth` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否需要用户令牌',
  `format` varchar(35) DEFAULT 'json' COMMENT '返回数据格式',
  `test_auth` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否允许在文档带测试',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序[文档排序/代码方法排序/路由接口顺序]',
  `doc_def` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '文档默认首页',
  `codes` text DEFAULT '' COMMENT '方法代码',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='接口控制器列表';

CREATE TABLE `pre_api_param` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '方法id',
  `title` varchar(60) DEFAULT '' COMMENT '参数名称',
  `name` varchar(60) DEFAULT '' COMMENT '参数名',
  `data_type` varchar(60) DEFAULT '' COMMENT '数据类型',
  `param_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '参数类型:0请求参数;1返回参数',
  `is_need` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必须',
  `def_val` varchar(60) DEFAULT '' COMMENT '参数默认值',
  `rule` text DEFAULT '' COMMENT '验证规则',
  `intro` varchar(100) DEFAULT '' COMMENT '参数说明',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序[文档排序]',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='接口参数表';

CREATE TABLE `pre_api_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '方法id',
  `code` varchar(20) DEFAULT '' COMMENT '返回码',
  `title` varchar(100) DEFAULT '' COMMENT '返回码说明',
  `solution` varchar(100) DEFAULT '' COMMENT '解决方法',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序[文档排序]',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='接口返回码表';

CREATE TABLE `pre_api_token` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '接口APPID',
  `token` varchar(35) DEFAULT '' COMMENT '接口令牌',
  `expire` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '令牌时效',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `app_id` (`app_id`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='接口令牌表';