/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : v2

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-6-18 11:23:43
*/

SET FOREIGN_KEY_CHECKS=0;


-- ----------------------------
-- Table structure for `v2_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `v2_auth_group`;
CREATE TABLE `v2_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '' COMMENT '用户组名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `rules` char(80) NOT NULL DEFAULT '' COMMENT '规则',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Records of v2_auth_group
-- ----------------------------

-- ----------------------------
-- Table structure for `v2_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `v2_auth_group_access`;
CREATE TABLE `v2_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组明显表';

-- ----------------------------
-- Records of v2_auth_group_access
-- ----------------------------

-- ----------------------------
-- Table structure for `v2_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `v2_auth_rule`;
CREATE TABLE `v2_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则名',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '中文名称',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '权限类型 1：URL 2：菜单 两种都是访问权限',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `condition` char(100) NOT NULL DEFAULT '' COMMENT '附加规则',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of v2_auth_rule
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_member`
-- ----------------------------
DROP TABLE IF EXISTS `v2_member`;
CREATE TABLE `v2_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `describe` text COLLATE utf8_unicode_ci NOT NULL COMMENT '个人简介',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员状态',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `ix_uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员信息表（基本应用）';

-- ----------------------------
-- Records of v2_member
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_ucenter_member`
-- ----------------------------
DROP TABLE IF EXISTS `v2_ucenter_member`;
CREATE TABLE `v2_ucenter_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) NULL COMMENT '用户邮箱',
  `mobile` char(15) NULL COMMENT '用户手机',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE,
  UNIQUE KEY `email` (`email`) USING BTREE,
  UNIQUE KEY `mobile` (`mobile`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户中心表';

-- ----------------------------
-- Records of v2_ucenter_member
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_oauth`
-- ----------------------------
DROP TABLE IF EXISTS `v2_oauth`;
CREATE TABLE `v2_oauth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '接口名称',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '接口标示',
  `config` text NOT NULL DEFAULT '' COMMENT '接口配置 serialize',
  `description` varchar(250) NOT NULL DEFAULT '' COMMENT '描述',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT 'logo',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='oauth认证方案';

-- ----------------------------
-- Records of v2_oauth
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_oauth_user`
-- ----------------------------
DROP TABLE IF EXISTS `v2_oauth_user`;
CREATE TABLE `v2_oauth_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '系统内部的用户ID',
  `openid` varchar(80) NOT NULL COMMENT '第三方平台的用户唯一标识',
  `oauth_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第三方',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '绑定时间',
  PRIMARY KEY (`id`),
  -- 保证uid在渠道唯一
  UNIQUE KEY `oauth_id_uid` (`oauth_id`,`uid`) USING BTREE,
  -- 保证openid在渠道唯一
  UNIQUE KEY `oauth_id_openid` (`oauth_id`,`openid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='oauth开发平台绑定用户表';

-- ----------------------------
-- Records of v2_oauth_user
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_openid_unionid`
-- ----------------------------
DROP TABLE IF EXISTS `v2_openid_unionid`;
CREATE TABLE `v2_openid_unionid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unionid` int(10) unsigned NOT NULL COMMENT 'unionid',
  `openid` varchar(80) NOT NULL COMMENT '第三方平台的用户唯一标识',
  -- `oauth_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第三方',
  PRIMARY KEY (`id`)
  -- 保证uid在渠道唯一
  -- UNIQUE KEY `oauth_id_uid` (`oauth_id`,`uid`) USING BTREE,
  -- 保证openid在渠道唯一
  -- UNIQUE KEY `oauth_id_openid` (`oauth_id`,`openid`) USING BTREE,
  -- KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='oauth unionid';

-- ----------------------------
-- Records of v2_openid_unionid
-- ----------------------------


-- 网页授权access_token中控服务（此access_token与基础支持中的access_token不同）
-- http://mp.weixin.qq.com/wiki/4/9ac2e7b1f1d22e9e57260f6553822520.html
-- https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316505&token=e03343b355571ca4d0e8b6bdc52e67cf7dc288c7&lang=zh_CN
-- http://mp.weixin.qq.com/wiki/15/54ce45d8d30b6bf6758f68d2e95bc627.html
-- unionid是解决在一个开发者账号下的多个应用间统一用户帐号的问题的


-- ----------------------------
-- Table structure for `v2_oauth_token`
-- ----------------------------
DROP TABLE IF EXISTS `v2_oauth_token`;
CREATE TABLE `v2_oauth_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `oauth_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第三方',
  `openid` int(10) unsigned NOT NULL COMMENT '授权用户唯一标识',
  `access_token` varchar(520) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '接口调用凭证',
  `refresh_token` varchar(520) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '用户刷新access_token',
  `scope` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '用户授权的作用域，使用逗号（,）分隔',
  `expires_in` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'access_token接口调用凭证超时时间，单位（秒）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_id_openid` (`oauth_id`,`openid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='微信网页授权access_token中控服务表';

-- ----------------------------
-- Records of v2_oauth_token
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_address`
-- ----------------------------
DROP TABLE IF EXISTS `v2_address`;
CREATE TABLE `v2_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `lng` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '经度',
  `lat` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '纬度',
  `location` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '地理位置',
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '手机号码',
  `user_name` char(16) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `user_sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '收货人性别，0-未知，1-男，2-女',
  `address_tag` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '标签，家，公司，学校，其它，自定义（请放心，只对自己可见）',
  `active_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '活跃时间（上次使用时间）',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否为默认地址，1-默认，0-非默认',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='收货地址表';

-- ----------------------------
-- Records of v2_address
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_shop`
-- ----------------------------
DROP TABLE IF EXISTS `v2_shop`;
CREATE TABLE `v2_shop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seller_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id，所属商户',
  `shop_type` tinyint(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '商店类型，1-快餐，2-烧烤，3-烧烤半成品，4-水果超市',
  `shop_business_type` tinyint(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '商店经营类型，1-平台自营，2-第三方合作',
  `settlement_account` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '提款账号（目前定为支付宝账号，后期可支持多种方案，可采用json储存收款方案）',
  `bank_name` char(15) NOT NULL DEFAULT '' COMMENT '开户行',
  `bank_user_name` char(15) NOT NULL COMMENT '开户行人姓名',
  `bank_card` char(15) NOT NULL COMMENT '卡号',
  `is_seller_settlement` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否使用商户提现账号作为提款账号，0-不是，1-是',
  `shop_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '商店名称',
  `describe` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '简介',
  `logo` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'logo',
  `store_bitmap` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '门店图片',
  `lng` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '经度',
  `lat` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '纬度',
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `address_reference` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '地址参照物',
  `contacts_name` char(16) NOT NULL DEFAULT '' COMMENT '负责人姓名',
  `contacts_mobile` char(15) NOT NULL COMMENT '联系人手机号码',
  `contacts_email` char(32) NOT NULL COMMENT '联系人电子邮箱',
  `business_hours` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '营业时间段[{"date":[1,2],"time":[["10:30","12:30"],……]},……]',
  `total_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总营业额 - 这个做排序，展示有用 生产环境中不是绝对可靠，以订单记录为准',
  `total_sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总销量 - 这个做排序，展示有用 生产环境中不是绝对可靠，以订单记录为准',
  `business_status` tinyint(4) DEFAULT '0' COMMENT '商店营业状态，0-休息中，1-正常营业',
  `status` tinyint(4) DEFAULT '0' COMMENT '商店状态，0-待审核，1-已审核（已上线）',
  `print_mode` tinyint(1) NOT NULL DEFAULT '1' COMMENT '打印模式，1-新单打印，2-确认打印',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `sort` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '排序（序号越小越靠前1为最小值)',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  KEY `shop_type` (`shop_type`) USING BTREE,
  KEY `shop_business_type` (`shop_business_type`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE,
  KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商店表';

-- ----------------------------
-- Records of v2_shop
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_seller`
-- ----------------------------
DROP TABLE IF EXISTS `v2_seller`;
CREATE TABLE `v2_seller` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `seller_name` char(16) NOT NULL COMMENT '商户名，如：五味真火',
  `logo` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '商户logo',
  `company_name` varchar(120) NOT NULL COMMENT '公司名称',
  `describe` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '商户简介',
  `settlement_account` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '提款账号（目前定为支付宝账号，后期可支持多种方案，可采用json储存收款方案）',
  `bank_name` char(15) NOT NULL DEFAULT '' COMMENT '开户行',
  `bank_user_name` char(15) NOT NULL COMMENT '开户行人姓名',
  `bank_card` char(15) NOT NULL COMMENT '卡号',
  `cash` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '保证金',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '税率',
  `contacts_name` char(16) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `contacts_mobile` char(15) NOT NULL COMMENT '联系人手机号码',
  `contacts_email` char(32) NOT NULL COMMENT '联系人电子邮箱',
  `contacts_phone` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '联系人座机号码',
  `paper_img` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '法人执证件照片',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建IP',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`) USING BTREE,
  UNIQUE KEY `seller_name` (`seller_name`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商户表';

-- ----------------------------
-- Records of v2_seller
-- ----------------------------
-- 注意这里有个id，商户表不同于其它会员表，子账号表，配送员表等表，它不能简单的表示一个用户账户

-- 后台创建子账号会从用户中心创建账号，所有应用的登陆信息都统一保存在用户中心体系中

-- 至少需要几张表：子账号表，子账号权限表，子账号角色表，子账号角色权限关联表，子账号角色关联表
-- 其他表：职位表，部门表，用户权限表（高危权限，如删除，查看财务等权限需单独分配给指定用户）
-- 其他表2：子账号-店铺关联表（多对多） （角色，权限部门……，都是基于店铺独立的）
-- 商店可以通过创建商户子账号来进行管理，如：五味真火@嘻嘻 （商户名@登录名）【这种方式废弃，都统一使用“用户中心”账号，在 统一通行证·子账号平台】
-- 子账号有身份，所属不同部门，权限职责不同



-- ----------------------------
-- Table structure for `v2_seller_subaccount`
-- ----------------------------
DROP TABLE IF EXISTS `v2_seller_subaccount`;
CREATE TABLE `v2_seller_subaccount` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `seller_id` int(10) unsigned NOT NULL COMMENT '所属商家ID',
  `avatar` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `name` char(16) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `mobile` char(15) NOT NULL COMMENT '联系人手机号码',
  `email` char(32) NOT NULL COMMENT '联系人电子邮箱',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  `office_address` char(32) NOT NULL COMMENT '办公地点',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建IP',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商户子账号表';

-- ----------------------------
-- Records of v2_seller_subaccount
-- ----------------------------
-- 商家创建子账号会从用户中心创建账号，所有应用的登陆信息都统一保存在用户中心体系中


-- ----------------------------
-- Table structure for `v2_distribution_clerk`
-- ----------------------------
DROP TABLE IF EXISTS `v2_distribution_clerk`;
CREATE TABLE `v2_distribution_clerk` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `avatar` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `name` char(16) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `mobile` char(15) NOT NULL COMMENT '联系人手机号码',
  `email` char(32) NOT NULL COMMENT '联系人电子邮箱',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  `describe` text COLLATE utf8_unicode_ci NOT NULL COMMENT '简介',
  `office_address` char(32) NOT NULL COMMENT '办公地点',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建IP',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='配送员表';

-- ----------------------------
-- Records of v2_distribution_clerk
-- ----------------------------
-- 后台创建配送员账号会从用户中心创建账号，所有应用的登陆信息都统一保存在用户中心体系中


-- ----------------------------
-- Table structure for `v2_distribution_order`
-- ----------------------------
DROP TABLE IF EXISTS `v2_distribution_order`;
CREATE TABLE `v2_distribution_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '配送员uid',
  `version` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版本控制，用于乐观锁',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '配送状态，0-创建订单（接单），1-顺利送达到顾客手中，2-配送未完成',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单创建时间',
  `completion_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单送达完成时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单创建ip',
  PRIMARY KEY (`id`),
  KEY `status` (`status`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='配送订单表';

-- ----------------------------
-- Records of v2_distribution_order
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_distribution_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `v2_distribution_order_log`;
CREATE TABLE `v2_distribution_order_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '此条目所属的配送订单的id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详情',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志创建时间',
  `mid` tinyint(4) NOT NULL DEFAULT '0' COMMENT '日志类型，0-创建订单，……',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='配送订单日志表';

-- ----------------------------
-- Records of v2_distribution_order_log
-- ----------------------------



-- ----------------------------
-- Table structure for `v2_printer`
-- ----------------------------
DROP TABLE IF EXISTS `v2_printer`;
CREATE TABLE `v2_printer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '打印机ID',
  `machine_code` char(32) NOT NULL DEFAULT '' COMMENT '打印机终端号',
  `msign` char(32) NOT NULL DEFAULT '' COMMENT '打印机终端密匙',
  `mobilephone` char(32) NOT NULL DEFAULT '' COMMENT '终端内部的手机号（方便充值）',
  `printname` char(16) NOT NULL DEFAULT '' COMMENT '打印机终端名称',
  `version` char(16) NOT NULL DEFAULT '' COMMENT '打印机型号',
  `network` tinyint(1) NOT NULL DEFAULT '1' COMMENT '打印机网络模式，1-手机卡，2-WIFI',
  `note` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '打印机备注',
  `contacts_mobile` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '管理人员手机号码',
  `contacts_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '负责人',
  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建IP',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `finish_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上报次数',
  `last_finish_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后上报IP',
  `last_finish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后上报时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '打印机状态，1-启用，0-关闭',
  `refresh_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上次网络状态刷新时间（由后台程序动态刷新）',
  `network_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '打印机网络状态（由后台程序动态刷新）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `machine_code` (`machine_code`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='打印机表';

-- ----------------------------
-- Records of v2_printer
-- ----------------------------

-- ----------------------------
-- Table structure for `v2_shop_printer`
-- ----------------------------
DROP TABLE IF EXISTS `v2_shop_printer`;
CREATE TABLE `v2_shop_printer` (
  `shop_id` mediumint(8) unsigned NOT NULL COMMENT '商店id',
  `printer_id` mediumint(8) unsigned NOT NULL COMMENT '打印机id',
  UNIQUE KEY `shop_printer_id` (`printer_id`,`shop_id`) USING BTREE,
  KEY `printer_id` (`printer_id`) USING BTREE,
  KEY `shop_id` (`shop_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商店 - 打印机关联表';

-- ----------------------------
-- Records of v2_shop_printer
-- ----------------------------

-- ----------------------------
-- Table structure for `v2_order`
-- ----------------------------
DROP TABLE IF EXISTS `v2_order`;
CREATE TABLE `v2_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户选择的商店，用户选择由商店该商店配送，0为云商店，需分单',
  `final_shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最终选择的商店ID，由客服确认审核订单时分配，或者自动分单',
  `order_name` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '订单名称',
  `order_no` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '订单号（暂不启用，直接用订单id）',
  `pay_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '支付方式，1-餐到付款，2-在线支付',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid，0-未登录用户',
  
  `version` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版本控制，用于乐观锁',

  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单状态，0-创建订单，待确认（在线支付订单必须支付成功后才能确认），1-已确认，2-已取消（用户触发），3-已取消（被作废，管理员触发），4-待配送，5-配送中，6-已送达（已完成），7-申请退款，8-退款中，9-已退款，10-订单超时关闭',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态，0-未支付，1-已支付',
  `print_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '打印状态，0-未推送，1-已推送（推送成功），2-推送失败，3-已打印（打印成功），4-打印失败',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',

  `box_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '餐盒总价',
  `product_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '餐品总价',
  `freight` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '配送费',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总价（全部总价）',
  `preferential_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额（优惠合计）',
  `real_pay` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实付金额',
  `preferential_json` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '优惠活动列表',

  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '用户备注',
  `delivery_time` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '送达时间',
  `meals_num` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用餐人数',

  `lng` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '经度',
  `lat` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '纬度',
  `location` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '地理位置',
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '手机号码',
  `user_name` char(16) NOT NULL DEFAULT '' COMMENT '姓名',
  `user_sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '收货人性别，0-未知，1-男，2-女',
  `address_tag` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '标签，家，公司，学校，其它，自定义（请放心，只对自己可见）',
  
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单创建时间',
  `print_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '打印时间',
  `send_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '配送时间',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `completion_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单完成时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',

  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单创建ip',
  `create_terminal` tinyint(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '订单创建设备，0-未知，1-pc，2-wap',
  `create_useragent` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '订单创建客户端代理信息',
  `webhook_json` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '支付回调信息',
  PRIMARY KEY (`id`),
  KEY `final_shop_id` (`final_shop_id`) USING BTREE,
  KEY `pay_type` (`pay_type`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `pay_status` (`pay_status`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE,
  KEY `print_status` (`print_status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='订单表';

-- ----------------------------
-- Records of v2_order
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_order_item`
-- ----------------------------
DROP TABLE IF EXISTS `v2_order_item`;
CREATE TABLE `v2_order_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '此条目所属的订单的id',
  `product_id` int(10) unsigned NOT NULL COMMENT '此条目的原产品id',
  `product_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `product_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '餐品单价',
  `product_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '餐品数量',
  `box_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '餐盒单价',
  `box_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '餐盒数量',
  `product_unit` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '单位',
  `product_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '图片',
  `box_summary` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '餐盒小结',
  `product_summary` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '餐品小结',
  `summary` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '小结（包含餐品金和餐盒金额）',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='订单条目表';

-- ----------------------------
-- Records of v2_order_item
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `v2_order_log`;
CREATE TABLE `v2_order_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '此条目所属的订单的id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详情',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志创建时间',
  `mid` tinyint(4) NOT NULL DEFAULT '0' COMMENT '日志类型，0-创建订单，……',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='订单日志表';

-- ----------------------------
-- Records of v2_order_log
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_order_note`
-- ----------------------------
DROP TABLE IF EXISTS `v2_order_note`;
CREATE TABLE `v2_order_note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '此条目所属的订单的id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详情',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '备注创建时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='订单备注表';

-- ----------------------------
-- Records of v2_order_note
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_order_cancel_reason`
-- ----------------------------
DROP TABLE IF EXISTS `v2_order_cancel_reason`;
CREATE TABLE `v2_order_cancel_reason` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '此条目所属的订单的id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `reason` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详情',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='订单取消原因表';

-- ----------------------------
-- Records of v2_order_cancel_reason
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_order_paper`
-- ----------------------------
DROP TABLE IF EXISTS `v2_order_paper`;
CREATE TABLE `v2_order_paper` (
  `order_id` mediumint(8) unsigned NOT NULL COMMENT '订单id',
  `paper_id` mediumint(8) unsigned NOT NULL COMMENT '票据id',
  `serial` mediumint(8) unsigned NOT NULL COMMENT '订单票据的序号',
  `printer_id` mediumint(8) unsigned NOT NULL COMMENT '打印机id',
  `print_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '票据打印状态（不是订单的打印状态），0-未打印（等待上报），1-打印失败，2-打印成功',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '票据生成时间',
  `print_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '票据打印时间',
  UNIQUE KEY `order_paper_id` (`paper_id`,`order_id`) USING BTREE,
  UNIQUE KEY `paper_id` (`paper_id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单票据表';

-- ----------------------------
-- Records of v2_order_paper
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_order_paper_log`
-- ----------------------------
DROP TABLE IF EXISTS `v2_order_paper_log`;
CREATE TABLE `v2_order_paper_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paper_id` int(10) unsigned NOT NULL COMMENT '此条目所属的票据的id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详情',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志创建时间',
  `mid` tinyint(4) NOT NULL DEFAULT '0' COMMENT '日志类型，0-生成票据，1-票据打印失败，……',
  PRIMARY KEY (`id`),
  KEY `paper_id` (`paper_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='订单票据 - 日志表';

-- ----------------------------
-- Records of v2_order_paper_log
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_products`
-- ----------------------------
DROP TABLE IF EXISTS `v2_products`;
CREATE TABLE `v2_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属商店ID，0为云商店，当为0时该餐品为云餐品，平台统一，分发更新统一',
  `sort` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '菜单排序（序号越小越靠前1为最小值)',
  `min_buy_count` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '最小购买数量',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `unit` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '单位',
  `sales_time` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '可售时间段（json表示）（空为不限制）',
  `describe` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '描述',
  `img` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '图片',
  `sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总售出份数 - 这个做排序，展示有用 生产环境中不是绝对可靠，以订单记录为准',
  `box_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '餐盒单价',
  `box_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '餐盒数量',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  `inventory` int(10) unsigned NOT NULL COMMENT '每日库存',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，1-上架，0-下架',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`) USING BTREE,
  KEY `sales` (`sales`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='餐品表';

-- ----------------------------
-- Records of v2_products
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_cloud_products_category`
-- ----------------------------
DROP TABLE IF EXISTS `v2_cloud_products_category`;
CREATE TABLE `v2_cloud_products_category` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '云产品分类ID',
  `parent_id`  int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '云产品父分类ID',
  `shop_type` tinyint(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '商店类型，1-快餐，2-烧烤，3-烧烤半成品，4-水果超市',
  `name`  varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '云产品分类名称',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `sort`  int(11) NOT NULL DEFAULT 0 COMMENT '排序（序号越小越靠前1为最小值)',
  `status` tinyint(4) DEFAULT '0' COMMENT '分类状态，1-正常，0-隐藏',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `shop_type` (`shop_type`) USING BTREE,
  KEY `sort` (`sort`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT COLLATE=utf8_unicode_ci COMMENT='云餐品 - 分类表';
-- 这里可以扩展一个字段 city_code 每个城市，一个分站，一个站点一个云
-- ----------------------------
-- Records of v2_cloud_products_category
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_cloud_category_extend`
-- ----------------------------
DROP TABLE IF EXISTS `v2_cloud_products_category_extend`;
CREATE TABLE `v2_cloud_products_category_extend` (
  `products_id`  int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '产品ID',
  `category_id`  int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类ID',
  UNIQUE KEY `products_cloud_category_id` (`products_id`,`category_id`) USING BTREE,
  KEY `products_id` (`products_id`) USING BTREE,
  KEY `category_id` (`category_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT COLLATE=utf8_unicode_ci COMMENT='云餐品 - 扩展分类表';

-- ----------------------------
-- Records of v2_cloud_products_category_extend
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_products_category`
-- ----------------------------
DROP TABLE IF EXISTS `v2_shop_products_category`;
CREATE TABLE `v2_shop_products_category` (
  `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `parent_id`  int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父分类ID',
  `shop_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `name`  varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '分类名称',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `sort`  int(11) NOT NULL DEFAULT 0 COMMENT '排序（序号越小越靠前1为最小值)',
  `status` tinyint(4) DEFAULT '0' COMMENT '分类状态，1-正常，0-隐藏',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `shop_id` (`shop_id`) USING BTREE,
  KEY `sort` (`sort`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT COLLATE=utf8_unicode_ci COMMENT='商店自定义 - 餐品分类表';

-- ----------------------------
-- Records of v2_shop_products_category
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_shop_products_category_extend`
-- ----------------------------
DROP TABLE IF EXISTS `v2_shop_products_category_extend`;
CREATE TABLE `v2_shop_products_category_extend` (
  `products_id`  int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '产品ID',
  `category_id`  int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类ID',
  UNIQUE KEY `products_cloud_category_id` (`products_id`,`category_id`) USING BTREE,
  KEY `products_id` (`products_id`) USING BTREE,
  KEY `category_id` (`category_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT COLLATE=utf8_unicode_ci COMMENT='商店自定义 - 餐品扩展分类表';

-- ----------------------------
-- Records of v2_shop_products_category_extend
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_pic`
-- ----------------------------
DROP TABLE IF EXISTS `v2_pic`;
CREATE TABLE `v2_pic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态 1-可见，0-不可见',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '原名称',
  PRIMARY KEY (`id`),
  KEY `md5` (`md5`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='上传图片记录表';

-- ----------------------------
-- Records of v2_pic
-- ----------------------------


-- 财务管理

-- 已完成的完成订单 > 待入账（生成财务订单） > 已入账 > 待结算 > 已结算 > 账户余额 > 提现规则 > 提现(减余额，做好日志) > 打款 > 打款成功（到账通知）

-- 定时入账，定时结算，结算成功后资金进入(商户?)账户余额，用户根据提现规则随时提现


-- 财务订单表，财务都是独立于订单的，独立于店铺的（如有分单的店铺则已接受分单的店铺为主）

-- 余额系统，是独立于店铺的（每个店铺有独立的收款账号，当然也可是是商户的收款账号）

-- 余额表，余额收支明细表


-- ----------------------------
-- Table structure for `v2_finance_order`
-- ----------------------------
DROP TABLE IF EXISTS `v2_finance_order`;
CREATE TABLE `v2_finance_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '账单id,财务订单id',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '原（已完成）订单id',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商店id',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `version` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版本控制，用于乐观锁',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态，0-创建订单（已入账），1-已结算，2-',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单创建时间',
  `completion_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单完成时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单创建ip',
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='财务订单表';

-- ----------------------------
-- Records of v2_finance_order
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_finance_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `v2_finance_order_log`;
CREATE TABLE `v2_finance_order_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `finance_id` int(10) unsigned NOT NULL COMMENT '此条目所属的财务订单的id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详情',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志创建时间',
  `mid` tinyint(4) NOT NULL DEFAULT '0' COMMENT '日志类型，0-创建订单，……',
  PRIMARY KEY (`id`),
  KEY `finance_id` (`finance_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='财务订单日志表';

-- ----------------------------
-- Records of v2_finance_order_log
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_withdrawals_order`
-- ----------------------------
DROP TABLE IF EXISTS `v2_withdrawals_order`;
CREATE TABLE `v2_withdrawals_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '提现id',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商店id',
  `version` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版本控制，用于乐观锁',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态，0-创建订单（申请体现），1-审核通过（已发往渠道处理，等待渠道反馈），2-审核不通过（您会收到提示短信，我们客服也会和您取得联系），3-已完成（渠道反馈已到账）',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `settlement_json` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '收款账号信息json',
  `webhook_json` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '收款回调信息',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单创建时间',
  `completion_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单送达完成时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `create_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单创建ip',
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='提现订单表';

-- ----------------------------
-- Records of v2_withdrawals_order
-- ----------------------------
-- 根据收款账号不同，处理渠道也不同，典型的两个例子就是线上支付宝渠道和线下打款渠道，线上渠道直接发往对应渠道，然后等待反馈，线下渠道需要人工打款，然后手动输入打款单号，已完成订单


-- ----------------------------
-- Table structure for `v2_withdrawals_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `v2_withdrawals_order_log`;
CREATE TABLE `v2_withdrawals_order_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `withdrawals_id` int(10) unsigned NOT NULL COMMENT '此条目所属的提现订单的id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详情',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志创建时间',
  `mid` tinyint(4) NOT NULL DEFAULT '0' COMMENT '日志类型，0-创建订单，……',
  PRIMARY KEY (`id`),
  KEY `withdrawals_id` (`withdrawals_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='提现订单日志表';

-- ----------------------------
-- Records of v2_withdrawals_order_log
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_shop_money`
-- ----------------------------
DROP TABLE IF EXISTS `v2_shop_money`;
CREATE TABLE `v2_shop_money` (
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商店id',
  `version` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版本控制，用于乐观锁',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  PRIMARY KEY (`shop_id`),
  UNIQUE KEY `shop_id` (`shop_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商店余额表';

-- ----------------------------
-- Records of v2_shop_money
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_shop_money_log`
-- ----------------------------
DROP TABLE IF EXISTS `v2_shop_money_log`;
CREATE TABLE `v2_shop_money_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商店id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额变动值',
  `balance` tinyint(1) NOT NULL DEFAULT '0' COMMENT '日志类型，0-收入，1-支出',
  `note` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '详情',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志创建时间',
  `mid` tinyint(4) NOT NULL DEFAULT '0' COMMENT '日志类型，0-入账，……',
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商店余额日志表';

-- ----------------------------
-- Records of v2_shop_money_log
-- ----------------------------


-- 订单评价（所属店铺）（店铺评价，配送评价），菜品评价（所属订单，所属店铺，所属菜品），对配送员评价，商店留言（所属店铺）都有追加功能


-- ----------------------------
-- Table structure for `v2_order_evaluation`
-- ----------------------------
DROP TABLE IF EXISTS `v2_order_evaluation`;
CREATE TABLE `v2_order_evaluation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '追加',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商店id',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `shop_point` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '商店评分',
  `distribution_point` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '配送评分',
  `is_anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否匿名，0-公开，1-匿名',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型：0-评价，1-追评，2-商家回复',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态，0-待审核，1-正常，2-被举报，3-被屏蔽',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `content` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='订单评价表';

-- ----------------------------
-- Records of v2_order_evaluation
-- ----------------------------

-- ----------------------------
-- Table structure for `v2_products_evaluation`
-- ----------------------------
DROP TABLE IF EXISTS `v2_products_evaluation`;
CREATE TABLE `v2_products_evaluation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '追加',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商店id',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `products_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '餐品id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `products_point` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '餐品评分',
  `is_anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否匿名，0-公开，1-匿名',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型：0-评价，1-追评，2-商家回复',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态，0-待审核，1-正常，2-被举报，3-被屏蔽',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `content` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='餐品评价表';

-- ----------------------------
-- Records of v2_products_evaluation
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_distribution_evaluation`
-- ----------------------------
DROP TABLE IF EXISTS `v2_distribution_evaluation`;
CREATE TABLE `v2_distribution_evaluation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '追加',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商店id',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `shop_point` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '商店评分',
  `distribution_point` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '配送评分',
  `is_anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否匿名，0-公开，1-匿名',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型：0-评价，1-追评，2-配送员回复',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态，0-待审核，1-正常，2-被举报，3-被屏蔽',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `content` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='配送员评价表';

-- ----------------------------
-- Records of v2_distribution_evaluation
-- ----------------------------


-- ----------------------------
-- Table structure for `v2_shop_leave_message`
-- ----------------------------
DROP TABLE IF EXISTS `v2_shop_leave_message`;
CREATE TABLE `v2_shop_leave_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '追加',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商店id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `is_anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否匿名，0-公开，1-匿名',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型：0-留言，1-追加，2-商家回复',
  `is_visible` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '可见性，0-所有人可见，1-仅参与者可见（自己和商家可见，其他用户不可见）',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态，0-待审核，1-正常，2-被举报，3-被屏蔽',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除，0-正常，1-已删除',
  `content` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商店留言表';

-- ----------------------------
-- Records of v2_shop_leave_message
-- ----------------------------


-- 会员营销体系
-- 红包，代金券（优惠券），活动（店铺活动，单品活动（单品组合活动））


-- ----------------------------
-- Table structure for `v2_sms_verification_code`
-- ----------------------------
DROP TABLE IF EXISTS `v2_sms_verification_code`;
CREATE TABLE `v2_sms_verification_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_code` int(10) unsigned NOT NULL COMMENT '业务编号',
  `code` char(4) NOT NULL DEFAULT '' COMMENT '验证码',
  `mobile` char(15) NULL COMMENT '手机号码',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，0-未验证，1-已验证',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `check_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '验证时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='短信验证码表';

-- ----------------------------
-- Records of v2_sms_verification_code
-- ----------------------------



-- 采购系统相当于点餐系统的克隆版  可以当成另外一个应用来做

-- 采购订单，提供商，店铺，（商户店铺/提供商店铺）采购财务

-- 采购是（商户）店铺对（提供商）店铺的，当然商户可以直接采购进自己的仓库，然后让分店从仓库中进场采购