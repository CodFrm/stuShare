/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : stushare

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-08-02 10:53:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for share_accounting
-- ----------------------------
DROP TABLE IF EXISTS `share_accounting`;
CREATE TABLE `share_accounting` (
  `aid` int(11) NOT NULL AUTO_INCREMENT COMMENT '计费id',
  `uid` int(11) NOT NULL,
  `login_time` int(11) NOT NULL,
  `logout_time` int(11) DEFAULT '-1',
  `nas_ip` varchar(255) NOT NULL,
  `allot_ip` varchar(255) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_accounting
-- ----------------------------
INSERT INTO `share_accounting` VALUES ('3', '1', '1501560930', '1501641570', '192.168.1.13', '10.8.0.6', 'BA98B0D09B708E53ECA868034E64174D');
INSERT INTO `share_accounting` VALUES ('4', '1', '1501560934', '123', '192.168.1.13', '10.8.0.6', 'BA98B0D09B708E53ECA868034E64174D');
INSERT INTO `share_accounting` VALUES ('5', '1', '1501641963', '1501642085', '192.168.1.13', '10.8.0.6', '31D020C56C48335129AC689D4097066D');
INSERT INTO `share_accounting` VALUES ('6', '1', '1501642094', '1501642106', '192.168.1.13', '10.8.0.6', '7D36B41661B907B175151011C7A20A38');

-- ----------------------------
-- Table structure for share_auth
-- ----------------------------
DROP TABLE IF EXISTS `share_auth`;
CREATE TABLE `share_auth` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_interface` varchar(255) NOT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_auth
-- ----------------------------
INSERT INTO `share_auth` VALUES ('1', 'user->index', null);
INSERT INTO `share_auth` VALUES ('2', 'user->money', null);
INSERT INTO `share_auth` VALUES ('3', 'user->api->online', null);
INSERT INTO `share_auth` VALUES ('4', 'user->movie->post', null);
INSERT INTO `share_auth` VALUES ('5', 'admin', null);
INSERT INTO `share_auth` VALUES ('6', 'radius', '允许用户通过认证');

-- ----------------------------
-- Table structure for share_config
-- ----------------------------
DROP TABLE IF EXISTS `share_config`;
CREATE TABLE `share_config` (
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_config
-- ----------------------------
INSERT INTO `share_config` VALUES ('update_u', 'www.baidu.com');
INSERT INTO `share_config` VALUES ('update_v', '1');

-- ----------------------------
-- Table structure for share_group
-- ----------------------------
DROP TABLE IF EXISTS `share_group`;
CREATE TABLE `share_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '群组id',
  `group_name` varchar(255) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_group
-- ----------------------------
INSERT INTO `share_group` VALUES ('1', '普通用户');
INSERT INTO `share_group` VALUES ('2', '网络VIP1');
INSERT INTO `share_group` VALUES ('3', '测试用');
INSERT INTO `share_group` VALUES ('4', '管理员');

-- ----------------------------
-- Table structure for share_groupauth
-- ----------------------------
DROP TABLE IF EXISTS `share_groupauth`;
CREATE TABLE `share_groupauth` (
  `group_id` int(11) NOT NULL,
  `auth_id` int(11) NOT NULL,
  KEY `auth_id` (`auth_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `auth_id` FOREIGN KEY (`auth_id`) REFERENCES `share_auth` (`auth_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_id` FOREIGN KEY (`group_id`) REFERENCES `share_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_groupauth
-- ----------------------------
INSERT INTO `share_groupauth` VALUES ('1', '1');
INSERT INTO `share_groupauth` VALUES ('1', '2');
INSERT INTO `share_groupauth` VALUES ('2', '1');
INSERT INTO `share_groupauth` VALUES ('2', '2');
INSERT INTO `share_groupauth` VALUES ('3', '1');
INSERT INTO `share_groupauth` VALUES ('1', '3');
INSERT INTO `share_groupauth` VALUES ('1', '4');
INSERT INTO `share_groupauth` VALUES ('4', '5');
INSERT INTO `share_groupauth` VALUES ('4', '1');
INSERT INTO `share_groupauth` VALUES ('4', '2');
INSERT INTO `share_groupauth` VALUES ('4', '3');
INSERT INTO `share_groupauth` VALUES ('4', '4');
INSERT INTO `share_groupauth` VALUES ('4', '6');
INSERT INTO `share_groupauth` VALUES ('2', '6');

-- ----------------------------
-- Table structure for share_inv_code
-- ----------------------------
DROP TABLE IF EXISTS `share_inv_code`;
CREATE TABLE `share_inv_code` (
  `inv_code` varchar(6) NOT NULL,
  `inv_uid` int(11) NOT NULL,
  `inv_use_uid` int(11) DEFAULT NULL,
  `inv_use_time` int(11) NOT NULL,
  PRIMARY KEY (`inv_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_inv_code
-- ----------------------------
INSERT INTO `share_inv_code` VALUES ('savrwz', '1', '1', '0');

-- ----------------------------
-- Table structure for share_log
-- ----------------------------
DROP TABLE IF EXISTS `share_log`;
CREATE TABLE `share_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_uid` int(11) NOT NULL,
  `log` varchar(255) NOT NULL,
  `log_type` int(11) NOT NULL,
  `log_time` int(11) NOT NULL,
  `log_req` text NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_uid` (`log_uid`),
  CONSTRAINT `log_uid` FOREIGN KEY (`log_uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_log
-- ----------------------------

-- ----------------------------
-- Table structure for share_order
-- ----------------------------
DROP TABLE IF EXISTS `share_order`;
CREATE TABLE `share_order` (
  `order_number` varchar(255) NOT NULL,
  `order_time` int(11) NOT NULL,
  `order_money` float(11,4) NOT NULL,
  `order_remarks` varchar(255) NOT NULL,
  PRIMARY KEY (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_order
-- ----------------------------

-- ----------------------------
-- Table structure for share_token
-- ----------------------------
DROP TABLE IF EXISTS `share_token`;
CREATE TABLE `share_token` (
  `uid` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  KEY `token_uid` (`uid`),
  CONSTRAINT `token_uid` FOREIGN KEY (`uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_token
-- ----------------------------
INSERT INTO `share_token` VALUES ('1', 'T3AGcaGy1500988808', '1500988953');
INSERT INTO `share_token` VALUES ('1', 'AnwyvZED1500993702', '1500993704');
INSERT INTO `share_token` VALUES ('1', 'pGs0fvXb1500994207', '1500994208');
INSERT INTO `share_token` VALUES ('1', 'Yi8Utw1W1501034516', '1501036644');
INSERT INTO `share_token` VALUES ('1', 'mZ3zYbs41501036773', '1501046542');
INSERT INTO `share_token` VALUES ('1', 'AUkmLQK71501046544', '1501048596');
INSERT INTO `share_token` VALUES ('1', 'SfYFmPci1501048892', '1501050530');
INSERT INTO `share_token` VALUES ('1', 'E8ZHpU151501573556', '1501573590');
INSERT INTO `share_token` VALUES ('1', 'zPzXzInF1501576048', '1501576452');
INSERT INTO `share_token` VALUES ('1', 'wbDdHIoS1501578743', '1501579431');

-- ----------------------------
-- Table structure for share_user
-- ----------------------------
DROP TABLE IF EXISTS `share_user`;
CREATE TABLE `share_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(16) CHARACTER SET utf8 NOT NULL,
  `password` varchar(18) CHARACTER SET utf8 NOT NULL,
  `email` varchar(32) CHARACTER SET utf8 NOT NULL,
  `reg_time` int(11) NOT NULL,
  `money` float(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of share_user
-- ----------------------------
INSERT INTO `share_user` VALUES ('1', 'Farmer', 'qwe123', 'code.farmer@qq.com', '1500692842', '0.00');
INSERT INTO `share_user` VALUES ('2', 'admin', '123456789', 'test@qq.com', '1500994558', '0.00');

-- ----------------------------
-- Table structure for share_usergroup
-- ----------------------------
DROP TABLE IF EXISTS `share_usergroup`;
CREATE TABLE `share_usergroup` (
  `uid` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `expire_time` int(11) NOT NULL DEFAULT '-1' COMMENT '用户组到期时间 永久为-1',
  KEY `uid` (`uid`),
  KEY `user_group_id` (`group_id`),
  CONSTRAINT `uid` FOREIGN KEY (`uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_group_id` FOREIGN KEY (`group_id`) REFERENCES `share_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_usergroup
-- ----------------------------
INSERT INTO `share_usergroup` VALUES ('1', '4', '-1');
INSERT INTO `share_usergroup` VALUES ('2', '4', '-1');

-- ----------------------------
-- Table structure for share_video
-- ----------------------------
DROP TABLE IF EXISTS `share_video`;
CREATE TABLE `share_video` (
  `vid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `release_time` varchar(255) NOT NULL,
  `mark` varchar(255) NOT NULL,
  `introduction` text NOT NULL,
  `time` int(11) NOT NULL,
  `live` int(11) NOT NULL,
  `pay` float(11,2) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 未录入 1 录入',
  `father_vid` int(11) NOT NULL DEFAULT '-1' COMMENT '-1为合集 否则为视频',
  PRIMARY KEY (`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_video
-- ----------------------------
