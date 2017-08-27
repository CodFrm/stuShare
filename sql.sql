/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : stushare

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-08-28 00:17:43
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_accounting
-- ----------------------------
INSERT INTO `share_accounting` VALUES ('3', '1', '1501560930', '1501641570', '192.168.1.13', '10.8.0.6', 'BA98B0D09B708E53ECA868034E64174D');
INSERT INTO `share_accounting` VALUES ('4', '1', '1501560934', '123', '192.168.1.13', '10.8.0.6', 'BA98B0D09B708E53ECA868034E64174D');
INSERT INTO `share_accounting` VALUES ('5', '1', '1501641963', '1501642085', '192.168.1.13', '10.8.0.6', '31D020C56C48335129AC689D4097066D');
INSERT INTO `share_accounting` VALUES ('6', '1', '1501642094', '1501642106', '192.168.1.13', '10.8.0.6', '7D36B41661B907B175151011C7A20A38');
INSERT INTO `share_accounting` VALUES ('7', '1', '1503291305', '1503291306', '192.168.1.13', '10.8.0.6', 'DDA99124360D14B678DE20009A701966');
INSERT INTO `share_accounting` VALUES ('8', '1', '1503291981', '1503291982', '192.168.1.13', '10.8.0.6', '0408AE701CD3688E97687D94B2E5C4E1');
INSERT INTO `share_accounting` VALUES ('9', '1', '1503292014', '1503292015', '192.168.1.13', '10.8.0.6', 'C8D72160071B0DAA44A072661BBD7EC0');
INSERT INTO `share_accounting` VALUES ('10', '1', '1503292264', '1503808626', '192.168.1.13', '10.8.0.6', '5C71C3058359E693591252B79B5D29E3');
INSERT INTO `share_accounting` VALUES ('11', '1', '1503842671', '1503843084', '192.168.1.13', '10.8.0.6', '574E773B2CEBA6099106078812219060');

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
INSERT INTO `share_auth` VALUES ('3', 'user->api', null);
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
INSERT INTO `share_config` VALUES ('base_auth', '1');
INSERT INTO `share_config` VALUES ('movie_app_update_u', 'ww6du.com');
INSERT INTO `share_config` VALUES ('movie_app_update_v', '14');
INSERT INTO `share_config` VALUES ('pc_update_u', 'ww23w.12');
INSERT INTO `share_config` VALUES ('pc_update_v', '1422');
INSERT INTO `share_config` VALUES ('regip', '86400');

-- ----------------------------
-- Table structure for share_feedback
-- ----------------------------
DROP TABLE IF EXISTS `share_feedback`;
CREATE TABLE `share_feedback` (
  `uid` int(11) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `type` int(11) NOT NULL COMMENT '1 vpn  2  影视',
  `time` int(11) NOT NULL,
  KEY `fed_uid` (`uid`),
  CONSTRAINT `fed_uid` FOREIGN KEY (`uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_feedback
-- ----------------------------
INSERT INTO `share_feedback` VALUES ('1', '中文', '可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?可以吗?', '0', '1503850102');

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
-- Table structure for share_ip
-- ----------------------------
DROP TABLE IF EXISTS `share_ip`;
CREATE TABLE `share_ip` (
  `ip` varchar(16) NOT NULL,
  `ip_time` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_ip
-- ----------------------------
INSERT INTO `share_ip` VALUES ('127.0.0.1', '1503848004', '-1');

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
-- Table structure for share_server
-- ----------------------------
DROP TABLE IF EXISTS `share_server`;
CREATE TABLE `share_server` (
  `svid` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `config` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`svid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of share_server
-- ----------------------------
INSERT INTO `share_server` VALUES ('1', '192.168.1.13', '树莓派', 0x636C69656E740A6465762074756E0A70726F746F207463700A72656D6F7465203139322E3136382E312E313320313139340A7265736F6C762D726574727920696E66696E6974650A6E6F62696E640A706572736973742D6B65790A706572736973742D74756E0A3C63613E0A2D2D2D2D2D424547494E2043455254494649434154452D2D2D2D2D0A4D4949434754434341594B67417749424167494A414C756B56377179654776664D413047435371475349623344514542437755414D4138784454414C42674E560A42414D4D42484A76623351774868634E4D5463774E544D784D5445794D6A41345768634E4D6A63774E5449354D5445794D6A4134576A41504D513077437759440A5651514444415279623239304D4947664D413047435371475349623344514542415155414134474E4144434269514B4267514469436D4E543642696E4A6E304F0A37526A623834642F713258676F4341504A5472596D6A66687678766D654E6855724334556E694A4A356D662F4C4B496B3256495777742B61577A516B623236550A5239626F7179456A77504E76396B52412F464B5873576357737869795245432F454C337133302F662F342B69443765454E4355715746704A304A62793945794A0A49504E316F4C3172574641634470313752637233477545434B45397767774944415141426F333077657A416442674E5648513445466751554249616D4C754C650A446B4D766967706E5877676E5471655174325577507759445652306A424467774E6F41554249616D4C754C65446B4D766967706E5877676E54716551743257680A453651524D4138784454414C42674E5642414D4D42484A76623353434351433770466536736E6872337A414D42674E5648524D45425441444151482F4D4173470A413155644477514541774942426A414E42676B71686B6947397730424151734641414F4267514379477A79665255476F483432585144583130545A37525555590A464A595279424A72646F376D6245764642772B6E5078344C7443654F4F2F66315A65777A3141666B3935413564785966444C48536C745A345052666A316F4D6F0A4E37445348667A6231545531637576644B582B7565576274684771504A446F4A625352506C674C707645527A5736656258634577533879584170326B73746E660A3530695662687A6E614362556939624155773D3D0A2D2D2D2D2D454E442043455254494649434154452D2D2D2D2D0A3C2F63613E0A636F6D702D6C7A6F0A7665726220330A617574682D757365722D706173730A);
INSERT INTO `share_server` VALUES ('2', '192.168.1.10', '测试', 0x656D6D6D6D6D6D);

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
INSERT INTO `share_token` VALUES ('1', 'xawgEfoj1503376788', '1503381544');
INSERT INTO `share_token` VALUES ('1', 'OmtqHf061503750687', '1503750688');
INSERT INTO `share_token` VALUES ('1', 'SDxGIIp81503750768', '1503750770');
INSERT INTO `share_token` VALUES ('1', 'fl3L2KQL1503751377', '1503751377');
INSERT INTO `share_token` VALUES ('1', 'iscwlTPu1503751828', '1503751829');
INSERT INTO `share_token` VALUES ('1', 'yeAoHAvH1503752129', '1503752130');
INSERT INTO `share_token` VALUES ('1', '3Ox7LXTc1503753277', '1503753278');
INSERT INTO `share_token` VALUES ('1', 'z3LgQdhm1503802099', '1503802100');
INSERT INTO `share_token` VALUES ('1', 'kbDduj7i1503802442', '1503802444');
INSERT INTO `share_token` VALUES ('1', 'OpYl86Qx1503802529', '1503802530');
INSERT INTO `share_token` VALUES ('1', '74pzH5P21503802683', '1503802684');
INSERT INTO `share_token` VALUES ('1', 'rkjqp9ic1503802987', '1503802988');
INSERT INTO `share_token` VALUES ('1', 'KsSee3KD1503803059', '1503803060');
INSERT INTO `share_token` VALUES ('1', 'Kk0guxhQ1503803413', '1503803414');
INSERT INTO `share_token` VALUES ('1', 'XxtxypvY1503803622', '1503803624');
INSERT INTO `share_token` VALUES ('1', 'FiDuKr4D1503803753', '1503803754');
INSERT INTO `share_token` VALUES ('1', 'txEh3XWY1503804009', '1503804011');
INSERT INTO `share_token` VALUES ('1', 'XZkUio881503806552', '1503806553');
INSERT INTO `share_token` VALUES ('1', 'Is3KxXwA1503806736', '1503806737');
INSERT INTO `share_token` VALUES ('1', 'ol6PfUEl1503806825', '1503806826');
INSERT INTO `share_token` VALUES ('1', 'j5d3UorZ1503806947', '1503806950');
INSERT INTO `share_token` VALUES ('1', 'HFRgelRZ1503807155', '1503807156');
INSERT INTO `share_token` VALUES ('1', 'dnTYW1Ur1503807195', '1503807196');
INSERT INTO `share_token` VALUES ('1', 'T4eWIn0c1503807245', '1503807246');
INSERT INTO `share_token` VALUES ('1', 'cZXXu3Tm1503807943', '1503807944');
INSERT INTO `share_token` VALUES ('1', 'aTyo4q9J1503808311', '1503808312');
INSERT INTO `share_token` VALUES ('1', 'pYnHmOUf1503808329', '1503808330');
INSERT INTO `share_token` VALUES ('1', 'EoqrHFXb1503808712', '1503808713');
INSERT INTO `share_token` VALUES ('1', '5iP0mqWq1503808811', '1503808812');
INSERT INTO `share_token` VALUES ('1', 'ofqsclhL1503808932', '1503808933');
INSERT INTO `share_token` VALUES ('1', '0d5Wjv9E1503809006', '1503809007');
INSERT INTO `share_token` VALUES ('1', 'iZrY57l41503809189', '1503809190');
INSERT INTO `share_token` VALUES ('1', 'vKha9HrG1503809234', '1503809235');
INSERT INTO `share_token` VALUES ('1', 'W1gugT8G1503809349', '1503809350');
INSERT INTO `share_token` VALUES ('1', 'oKJuReJP1503809510', '1503809511');
INSERT INTO `share_token` VALUES ('1', 'olT6Qrad1503809554', '1503809555');
INSERT INTO `share_token` VALUES ('1', 'gglg4zwg1503809740', '1503809741');
INSERT INTO `share_token` VALUES ('1', 'aj46ROwW1503811313', '1503811314');
INSERT INTO `share_token` VALUES ('1', 'Gs0AyO5P1503811744', '1503811745');
INSERT INTO `share_token` VALUES ('1', 'UHxibrnm1503811830', '1503811836');
INSERT INTO `share_token` VALUES ('1', 'EPHdpXxG1503812037', '1503812038');
INSERT INTO `share_token` VALUES ('1', 'hA5OiTmu1503812485', '1503812486');
INSERT INTO `share_token` VALUES ('1', 'Q6HjnOFs1503841400', '1503841401');
INSERT INTO `share_token` VALUES ('1', 'PHdKyOa91503841585', '1503841586');
INSERT INTO `share_token` VALUES ('1', 'T38f7LkO1503841750', '1503841751');
INSERT INTO `share_token` VALUES ('1', '8wAQD4J11503842425', '1503843047');
INSERT INTO `share_token` VALUES ('1', 'DkpiwEE41503847989', '1503847991');
INSERT INTO `share_token` VALUES ('1', 'gHK8sP7s1503848302', '1503848303');
INSERT INTO `share_token` VALUES ('1', '64Wj0hHS1503848607', '1503848608');
INSERT INTO `share_token` VALUES ('1', 'E9RgwHsI1503848688', '1503848689');
INSERT INTO `share_token` VALUES ('1', 'HHSIuHoJ1503849056', '1503849057');
INSERT INTO `share_token` VALUES ('1', 'z6Ow9wl51503850047', '1503850101');
INSERT INTO `share_token` VALUES ('1', 'yjozzbmX1503850273', '1503850274');
INSERT INTO `share_token` VALUES ('1', 'LOwh0Ihs1503850360', '1503850361');
INSERT INTO `share_token` VALUES ('1', 'tIqIDkU21503850423', '1503850424');
INSERT INTO `share_token` VALUES ('1', 'Z0X0yzrW1503850465', '1503850466');
INSERT INTO `share_token` VALUES ('1', 'DIshrnT21503850492', '1503850493');
INSERT INTO `share_token` VALUES ('1', 'nt7pkkHH1503850539', '1503850540');
INSERT INTO `share_token` VALUES ('1', '1jHmIKxX1503850572', '1503850573');

-- ----------------------------
-- Table structure for share_user
-- ----------------------------
DROP TABLE IF EXISTS `share_user`;
CREATE TABLE `share_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(16) CHARACTER SET utf8 NOT NULL,
  `password` varchar(18) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(32) CHARACTER SET utf8 NOT NULL,
  `reg_time` int(11) NOT NULL,
  `money` float(11,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of share_user
-- ----------------------------
INSERT INTO `share_user` VALUES ('1', 'Farmer', 'qwe123', 'code.farmer@qq.com', '1500692842', '0.00');
INSERT INTO `share_user` VALUES ('2', 'admin', '123456789', 'test@qq.com', '1500994558', '0.00');
INSERT INTO `share_user` VALUES ('3', 'qwe123', 'qwe123', 'qqq@qq.com', '1503843239', '0.00');
INSERT INTO `share_user` VALUES ('6', 'qwe1234', 'qwe123', 'qq3q@qq.com', '1503847986', '0.00');
INSERT INTO `share_user` VALUES ('7', 'qwe123523', 'qwe123', 'dsfw3@qq.com', '1503848004', '0.00');

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
INSERT INTO `share_usergroup` VALUES ('3', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('6', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('7', '1', '-1');

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
