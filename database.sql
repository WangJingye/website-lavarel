SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tbl_admin
-- ----------------------------
DROP TABLE IF EXISTS `tbl_admin`;
CREATE TABLE `tbl_admin`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户名称',
  `password` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '密码',
  `realname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '联系电话',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '头像地址',
  `salt` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `is_admin` tinyint(4) NULL DEFAULT 0 COMMENT '0:普通用户 1:管理员',
  `last_login_time` int(11) NULL DEFAULT 0 COMMENT '最后登录时间',
  `passwd_modify_time` int(11) NULL DEFAULT 0 COMMENT '密码最后修改日期',
  `create_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT 0 COMMENT '信息修改时间',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '用户账号状态 0:删除,1:锁定（不可登陆）[2-8保留] 9 正常 ',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台用户表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tbl_admin
-- ----------------------------
INSERT INTO `tbl_admin` VALUES (1, 'admin', 'de5adcf92bd1be1f221e3bad88f97f6e', '超级管理员', '', '11@qq.com', '', '6544', 1, 1623143641, 0, 1566546983, 1623143641, 1);

-- ----------------------------
-- Table structure for tbl_menu
-- ----------------------------
DROP TABLE IF EXISTS `tbl_menu`;
CREATE TABLE `tbl_menu`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单文件路径',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单描述',
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级菜单ID',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单icon样式',
  `sort` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '菜单权重排序号',
  `depth` tinyint(4) NULL DEFAULT 1 COMMENT '菜单等级',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '菜单状态 1 有效 0 无效',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建菜单时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '修改菜单时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`parent_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台菜单数据表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tbl_menu
-- ----------------------------
INSERT INTO `tbl_menu` VALUES (1, '系统管理', '', 'root', 0, 'glyphicon glyphicon-cog', 10, 1, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (2, '业务管理', '', '', 0, 'glyphicon glyphicon-briefcase', 9, 1, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (3, '菜单权限管理', '', '', 1, 'glyphicon glyphicon-list', 0, 2, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (4, '菜单列表', 'system/menu/index', '', 3, '', 0, 3, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (5, '编辑菜单', 'system/menu/edit-menu', '', 4, '', 0, 4, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (6, '角色列表', 'system/role/index', '', 3, '', 0, 3, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (7, '编辑角色', 'system/role/edit-role', '', 6, '', 0, 4, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (8, '设置角色权限', 'system/role/set-role-menu', '', 6, '', 0, 4, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (9, '设置角色用户', 'system/role/set-role-admin', '', 6, '', 0, 4, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (10, '后台账号管理', '', '', 1, 'glyphicon glyphicon-user', 0, 2, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (11, '账号列表', 'system/admin/index', '', 10, '', 0, 3, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (12, '编辑账号', 'system/admin/edit-admin', '', 11, '', 0, 4, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (13, '菜单启用/禁用', 'system/menu/set-status', '', 4, '', 0, 4, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (14, '账号启用/禁用', 'system/admin/set-status', '', 11, '', 0, 4, 1, 1562982765, 1562982765);
INSERT INTO `tbl_menu` VALUES (15, '重置密码', 'system/admin/reset-password', '', 11, '', 0, 4, 1, 1563005512, 1564936542);
INSERT INTO `tbl_menu` VALUES (16, '个人信息', 'system/admin/profile', '', 11, '', 0, 4, 1, 1563005512, 1564936542);
INSERT INTO `tbl_menu` VALUES (17, '后台首页', '', '', 2, 'glyphicon glyphicon-home', 0, 2, 1, 1572591987, 1572591987);
INSERT INTO `tbl_menu` VALUES (18, '网站信息', 'erp/site-info/base-info', '', 17, '', 8, 3, 1, 1573021780, 1573021780);
INSERT INTO `tbl_menu` VALUES (19, '首页', 'erp/site-info/index', '', 17, '', 9, 3, 1, 1591234735, 1591234735);
INSERT INTO `tbl_menu` VALUES (20, '微信相关', 'erp/site-info/wechat', '', 17, '', 7, 3, 1, 1573021780, 1573021780);
INSERT INTO `tbl_menu` VALUES (21, '小程序管理', 'erp/site-info/app-info', '', 17, '', 6, 3, 1, 1573021780, 1573021780);

-- ----------------------------
-- Table structure for tbl_role
-- ----------------------------
DROP TABLE IF EXISTS `tbl_role`;
CREATE TABLE `tbl_role`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '描述',
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 1 COMMENT '角色状态 0 无效 1 有效',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tbl_role
-- ----------------------------
INSERT INTO `tbl_role` VALUES (1, '管理员', 'root', 1, 1562982778, 1562982993);

-- ----------------------------
-- Table structure for tbl_role_admin
-- ----------------------------
DROP TABLE IF EXISTS `tbl_role_admin`;
CREATE TABLE `tbl_role_admin`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL COMMENT '用户ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tbl_role_admin
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `tbl_role_menu`;
CREATE TABLE `tbl_role_menu`  (
  `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `menu_id` int(11) NOT NULL COMMENT '菜单ID',
  `create_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `role-menu`(`role_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tbl_role_menu
-- ----------------------------
INSERT INTO `tbl_role_menu` VALUES (1, 1, 1, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (2, 1, 3, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (3, 1, 4, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (4, 1, 5, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (5, 1, 13, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (6, 1, 6, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (7, 1, 7, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (8, 1, 8, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (9, 1, 9, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (10, 1, 10, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (11, 1, 11, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (12, 1, 12, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (13, 1, 14, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (14, 1, 15, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (15, 1, 2, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (16, 1, 17, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (17, 1, 19, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (18, 1, 18, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (19, 1, 20, 1623143967);
INSERT INTO `tbl_role_menu` VALUES (20, 1, 21, 1623143967);

-- ----------------------------
-- Table structure for tbl_site_info
-- ----------------------------
DROP TABLE IF EXISTS `tbl_site_info`;
CREATE TABLE `tbl_site_info`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `web_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `web_host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `web_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `default_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '123456',
  `wechat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '微信相关',
  `app_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
  `about_us` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '关于我们',
  `expire_order_pay` int(11) NULL DEFAULT 0,
  `expire_order_pending` int(11) NULL DEFAULT 0,
  `spread` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '分销配置',
  `create_time` int(11) NULL DEFAULT 0,
  `update_time` int(11) NULL DEFAULT 0,
  `expire_order_finish` int(11) NULL DEFAULT 0,
  `expire_order_comment` int(11) NULL DEFAULT 0,
  `flash_showing` tinyint(4) NULL DEFAULT 0 COMMENT '团购场次是否启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '站点信息' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tbl_site_info
-- ----------------------------
INSERT INTO `tbl_site_info` VALUES (1, '后台管理系统', '', '', '123456', '', '', '111', 15, 1440, '{\"type\":\"1\",\"depth\":\"3\",\"back\":[\"20\",\"10\",\"5\"]}', 1573022804, 1623143924, 1, 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
