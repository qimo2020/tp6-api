CREATE TABLE `pre_api_app` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT '' COMMENT '应用名称',
  `name` varchar(100) DEFAULT '' COMMENT '应用名',
  `api_secret_key` varchar(50) DEFAULT '' COMMENT '签名秘钥',
  `app_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '应用类型:0模块1插件',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `version` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='应用管理';

INSERT INTO `pre_api_app` (`id`, `title`, `name`, `api_secret_key`, `app_type`, `sort`, `status`, `version`, `create_time`, `update_time`)
VALUES
  (1,'会员授权','member','0aLnPod8aYhSk0uwewz2QAFmOzlof97J',0,0,1,1603087513,1579153029,1579153029);

CREATE TABLE `pre_api_controller` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '应用id',
  `title` varchar(60) DEFAULT '' COMMENT '控制器名称',
  `name` varchar(30) DEFAULT '' COMMENT '控制器名',
  `map_name` varchar(30) DEFAULT '' COMMENT '接口映射名',
  `version` varchar(10) DEFAULT '' COMMENT '版本号',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序[文档排序]',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='接口控制器列表';

INSERT INTO `pre_api_controller` (`id`, `app_id`, `title`, `name`, `map_name`, `version`, `sort`, `status`, `create_time`, `update_time`)
VALUES
  (1,1,'Oauth模式','oauth','','',0,1,1579153029,1579153029),
  (2,1,'Jwt模式','jwt','','',1,1,1579153029,1579153029);

CREATE TABLE `pre_api_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '控制器id',
  `title` varchar(60) DEFAULT '' COMMENT '方法名称',
  `name` varchar(60) DEFAULT '' COMMENT '方法名',
  `request_type` varchar(10) DEFAULT '' COMMENT '请求类型',
  `api_auth` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否需要接口令牌',
  `user_auth` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否需要会员鉴权(oauth)',
  `jwt_auth` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否需要会员令牌(jwt)',
  `format` varchar(35) DEFAULT 'json' COMMENT '返回数据格式',
  `test_auth` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否允许在文档带测试',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序[文档排序/代码方法排序/路由接口顺序]',
  `doc_def` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '文档默认首页',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='接口控制器列表';

INSERT INTO `pre_api_action` (`id`, `cid`, `title`, `name`, `request_type`, `api_auth`, `user_auth`, `jwt_auth`, `format`, `test_auth`, `status`, `sort`, `doc_def`, `create_time`, `update_time`)
VALUES
  (1, 1, '默认', 'token', 'post', 1, 0, 0, '["json"]', 1, 1, 0, 1, 1603077720, 1603077720),
  (2, 2, '默认', 'token', 'post', 1, 0, 0, '["json"]', 1, 1, 0, 0, 1603077720, 1603077720);

CREATE TABLE `pre_api_param` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '方法id',
  `title` varchar(60) DEFAULT '' COMMENT '参数名称',
  `name` varchar(60) DEFAULT '' COMMENT '参数名',
  `data_type` varchar(60) DEFAULT '' COMMENT '数据类型',
  `param_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '参数类型:0请求参数;1返回参数',
  `is_need` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必须',
  `def_val` varchar(60) DEFAULT '' COMMENT '参数默认值',
  `rule` text COMMENT '验证规则',
  `intro` varchar(100) DEFAULT '' COMMENT '参数说明',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序[文档排序]',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='接口参数表';

INSERT INTO `pre_api_param` (`id`, `aid`, `title`, `name`, `data_type`, `param_type`, `is_need`, `def_val`, `rule`, `intro`, `sort`, `status`, `create_time`, `update_time`)
VALUES
  ('1', '1', '账号', 'account', 'string', '0', '1', '', '', '', '0', '1', '1603095630', '1603095630'),
  ('2', '1', '密码', 'password', 'string', '0', '1', '', '', '', '1', '1', '1603095656', '1603095656'),
  ('3', '2', '账号', 'account', 'string', '0', '1', '', '', '', '0', '1', '1603095835', '1603095835'),
  ('4', '2', '密码', 'password', 'string', '0', '1', '', '', '', '1', '1', '1603095871', '1603095871');

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