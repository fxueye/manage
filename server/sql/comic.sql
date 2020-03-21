/*
Navicat MySQL Data Transfer

Source Server         : 235
Source Server Version : 50724
Source Host           : 192.168.31.235:3306
Source Database       : comic

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2020-03-21 11:51:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `c_log`
-- ----------------------------
DROP TABLE IF EXISTS `c_log`;
CREATE TABLE `c_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `user_name` varchar(50) DEFAULT NULL COMMENT '用户名',
  `operation` varchar(50) DEFAULT NULL COMMENT '用户操作',
  `method` varchar(200) DEFAULT NULL COMMENT '请求方法',
  `params` varchar(5000) DEFAULT NULL COMMENT '请求参数',
  `time` bigint(20) NOT NULL COMMENT '执行时长(毫秒)',
  `ip` varchar(64) DEFAULT NULL COMMENT 'IP地址',
  `create_by` varchar(50) DEFAULT NULL COMMENT '创建人',
  `create_time` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `update_by` varchar(50) DEFAULT NULL COMMENT '更新人',
  `update_time` bigint(20) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统日志';

-- ----------------------------
-- Records of c_log
-- ----------------------------

-- ----------------------------
-- Table structure for `c_menu`
-- ----------------------------
DROP TABLE IF EXISTS `c_menu`;
CREATE TABLE `c_menu` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(50) DEFAULT NULL COMMENT '菜单名称',
  `parent_id` bigint(20) DEFAULT '0' COMMENT '父菜单ID，一级菜单为0',
  `url` varchar(200) DEFAULT '' COMMENT '菜单URL,类型：1.普通页面（如用户管理， /sys/user） 2.嵌套完整外部页面，以http(s)开头的链接 3.嵌套服务器页面，使用iframe:前缀+目标URL(如SQL监控， iframe:/druid/login.html, iframe:前缀会替换成服务器地址)',
  `perms` varchar(500) DEFAULT '' COMMENT '授权(多个用逗号分隔，如：sys:user:add,sys:user:edit)',
  `type` int(11) DEFAULT '0' COMMENT '类型   0：目录   1：菜单   2：按钮',
  `icon` varchar(50) DEFAULT '' COMMENT '菜单图标',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `create_by` varchar(50) DEFAULT '' COMMENT '创建人',
  `create_time` bigint(20) DEFAULT '0' COMMENT '创建时间',
  `update_by` varchar(50) DEFAULT '' COMMENT '更新人',
  `update_time` bigint(20) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='菜单管理';

-- ----------------------------
-- Records of c_menu
-- ----------------------------
INSERT INTO `c_menu` VALUES ('1', '系统管理', '0', '', '', '0', 'el-icon-lx-settings', '0', 'admin', '1584071757', 'admin', '1584071757');
INSERT INTO `c_menu` VALUES ('2', '用户管理', '1', '/sys/user', '', '1', '', '0', 'admin', '1584071757', 'admin', '1584071757');
INSERT INTO `c_menu` VALUES ('3', '角色管理', '1', '/sys/role', '', '1', '', '0', 'admin', '1584071757', 'admin', '1584071757');
INSERT INTO `c_menu` VALUES ('4', '菜单管理', '1', '/sys/menu', '', '1', '', '0', 'admin', '1584071757', 'admin', '1584071757');
INSERT INTO `c_menu` VALUES ('5', '系统日志', '1', '/sys/log', '', '1', '', '0', 'admin', '1584071757', 'admin', '1584071757');
INSERT INTO `c_menu` VALUES ('6', '添加', '2', '', 'sys:user:add', '2', '', '0', 'admin', '1584703700', 'admin', '1584703700');
INSERT INTO `c_menu` VALUES ('7', '删除', '2', '', 'sys:user:del', '2', '', '0', 'admin', '1584703736', 'admin', '1584703736');
INSERT INTO `c_menu` VALUES ('8', '编辑', '2', '', 'sys:user:edit', '2', '', '0', 'admin', '1584703766', 'admin', '1584703766');
INSERT INTO `c_menu` VALUES ('9', '查询', '2', '', 'sys:user:select', '2', '', '0', 'admin', '1584703794', 'admin', '1584703794');

-- ----------------------------
-- Table structure for `c_role`
-- ----------------------------
DROP TABLE IF EXISTS `c_role`;
CREATE TABLE `c_role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(100) DEFAULT NULL COMMENT '角色名称',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注',
  `create_by` varchar(50) DEFAULT NULL COMMENT '创建人',
  `create_time` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `update_by` varchar(50) DEFAULT NULL COMMENT '更新人',
  `update_time` bigint(20) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='角色';

-- ----------------------------
-- Records of c_role
-- ----------------------------
INSERT INTO `c_role` VALUES ('1', 'admin', '超级管理员', 'admin', '1584071747', 'admin', '1584071747');

-- ----------------------------
-- Table structure for `c_role_menu`
-- ----------------------------
DROP TABLE IF EXISTS `c_role_menu`;
CREATE TABLE `c_role_menu` (
  `role_id` bigint(20) DEFAULT NULL COMMENT '角色ID',
  `menu_id` bigint(20) DEFAULT NULL COMMENT '菜单ID',
  `create_by` varchar(50) DEFAULT NULL COMMENT '创建人',
  `create_time` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `update_by` varchar(50) DEFAULT NULL COMMENT '更新人',
  `update_time` bigint(20) DEFAULT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色与菜单对应关系';

-- ----------------------------
-- Records of c_role_menu
-- ----------------------------
INSERT INTO `c_role_menu` VALUES ('1', '1', 'admin', '1584705888', 'admin', '1584705888');
INSERT INTO `c_role_menu` VALUES ('1', '2', 'admin', '1584705888', 'admin', '1584705888');
INSERT INTO `c_role_menu` VALUES ('1', '6', 'admin', '1584705888', 'admin', '1584705888');
INSERT INTO `c_role_menu` VALUES ('1', '8', 'admin', '1584705888', 'admin', '1584705888');
INSERT INTO `c_role_menu` VALUES ('1', '9', 'admin', '1584705888', 'admin', '1584705888');
INSERT INTO `c_role_menu` VALUES ('1', '3', 'admin', '1584705888', 'admin', '1584705888');
INSERT INTO `c_role_menu` VALUES ('1', '4', 'admin', '1584705888', 'admin', '1584705888');
INSERT INTO `c_role_menu` VALUES ('1', '5', 'admin', '1584705888', 'admin', '1584705888');

-- ----------------------------
-- Table structure for `c_user`
-- ----------------------------
DROP TABLE IF EXISTS `c_user`;
CREATE TABLE `c_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(100) DEFAULT NULL COMMENT '手机号',
  `status` tinyint(4) DEFAULT NULL COMMENT '状态  0：禁用   1：正常',
  `create_by` varchar(50) DEFAULT NULL COMMENT '创建人',
  `create_time` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `update_by` varchar(50) DEFAULT NULL COMMENT '更新人',
  `update_time` bigint(20) DEFAULT NULL COMMENT '更新时间',
  `last_login_ip` varchar(50) DEFAULT '' COMMENT '最后一次登录ip',
  `last_login_time` bigint(50) DEFAULT '0' COMMENT '最后登录时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户';

-- ----------------------------
-- Records of c_user
-- ----------------------------
INSERT INTO `c_user` VALUES ('1', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '281431280@qq.com', '15137022093', '0', 'admin', '1583820317', 'admin', '1584698108', '192.168.31.1', '1584762335');

-- ----------------------------
-- Table structure for `c_user_oauth`
-- ----------------------------
DROP TABLE IF EXISTS `c_user_oauth`;
CREATE TABLE `c_user_oauth` (
  `id` varchar(64) NOT NULL,
  `type` char(64) NOT NULL,
  `nickname` char(64) NOT NULL,
  `avatar` char(255) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `access_token` varchar(64) NOT NULL,
  `refresh_token` varchar(64) DEFAULT NULL,
  `expires_in` int(11) NOT NULL,
  `create_time` bigint(20) DEFAULT NULL,
  `update_time` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_oauth_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='认证用户';

-- ----------------------------
-- Records of c_user_oauth
-- ----------------------------

-- ----------------------------
-- Table structure for `c_user_role`
-- ----------------------------
DROP TABLE IF EXISTS `c_user_role`;
CREATE TABLE `c_user_role` (
  `user_id` bigint(20) DEFAULT NULL COMMENT '用户ID',
  `role_id` bigint(20) DEFAULT NULL COMMENT '角色ID',
  `create_by` varchar(50) DEFAULT NULL COMMENT '创建人',
  `create_time` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `update_by` varchar(50) DEFAULT NULL COMMENT '更新人',
  `update_time` bigint(20) DEFAULT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户与角色对应关系';

-- ----------------------------
-- Records of c_user_role
-- ----------------------------
INSERT INTO `c_user_role` VALUES ('1', '1', 'admin', '1584698108', 'admin', '1584698108');
