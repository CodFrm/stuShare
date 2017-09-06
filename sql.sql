/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : stushare

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-09-06 18:16:36
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
  PRIMARY KEY (`aid`),
  KEY `account_uid` (`uid`),
  CONSTRAINT `account_uid` FOREIGN KEY (`uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

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
INSERT INTO `share_accounting` VALUES ('12', '1', '1504446686', '1504446813', '127.0.0.1', '10.8.0.6', 'D499004EE0CEE37D629E8C2A5094271B');
INSERT INTO `share_accounting` VALUES ('13', '1', '1504446883', '1504446941', '127.0.0.1', '10.8.0.6', '3172D0B9C3B689DC9E02BE005C12B584');
INSERT INTO `share_accounting` VALUES ('14', '1', '1504541054', '1504541073', '10.1.6.31', '10.8.0.62', 'EDEA8C28DB77A520BA5D00502D4E0DB3');
INSERT INTO `share_accounting` VALUES ('15', '1', '1504541103', '1504541227', '10.1.6.31', '10.8.0.62', '3D8ECAECBB9152C9EEB4728130945635');
INSERT INTO `share_accounting` VALUES ('16', '1', '1504541235', '1504541372', '10.1.6.31', '10.8.0.62', '8B8A85B38B3ED620071DE59447A8EA2C');
INSERT INTO `share_accounting` VALUES ('17', '1', '1504541399', '1504541430', '10.1.6.31', '10.8.0.62', '03048C9C10AEE534310BE6E0B94DA578');
INSERT INTO `share_accounting` VALUES ('18', '1', '1504541446', '1504541477', '10.1.6.31', '10.8.0.62', '245077A7669639B77BCB2C00832DAD03');
INSERT INTO `share_accounting` VALUES ('19', '1', '1504541897', '10', '10.1.6.31', '10.8.0.62', 'ED7847A8DA0DD099C81E8A940B519E6D');
INSERT INTO `share_accounting` VALUES ('20', '1', '1504542037', '1504542214', '10.1.6.31', '10.8.0.62', '3CC2A7CEA04518072282605DB6C342C3');
INSERT INTO `share_accounting` VALUES ('21', '1', '1504542295', '1504542515', '10.1.6.31', '10.8.0.62', 'B026E466A3C44CA39B79496A5D26002A');
INSERT INTO `share_accounting` VALUES ('22', '1', '1504542600', '1504542658', '10.1.6.31', '10.8.0.62', '4B6EE8D13E7D194ED073200E8915C357');
INSERT INTO `share_accounting` VALUES ('23', '1', '1504542744', '1504542768', '10.1.6.31', '10.8.0.62', 'A909D3626167E0D10ADA515EE1519784');
INSERT INTO `share_accounting` VALUES ('24', '1', '1504542936', '1504543002', '10.1.6.31', '10.8.0.62', '550107073D0050E882AA69297869D0BD');
INSERT INTO `share_accounting` VALUES ('25', '1', '1504543026', '1504543037', '10.1.6.31', '10.8.0.62', '6C1D1CE91B43A88DA345E3901AA8706E');

-- ----------------------------
-- Table structure for share_auth
-- ----------------------------
DROP TABLE IF EXISTS `share_auth`;
CREATE TABLE `share_auth` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_interface` varchar(255) NOT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_auth
-- ----------------------------
INSERT INTO `share_auth` VALUES ('1', 'user->index', null);
INSERT INTO `share_auth` VALUES ('2', 'user->money', null);
INSERT INTO `share_auth` VALUES ('3', 'user->api', null);
INSERT INTO `share_auth` VALUES ('4', 'user->movie->post', null);
INSERT INTO `share_auth` VALUES ('5', 'admin', null);
INSERT INTO `share_auth` VALUES ('6', 'radius', '允许用户通过认证');
INSERT INTO `share_auth` VALUES ('7', 'user->movie->api', '影视解析');

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
INSERT INTO `share_config` VALUES ('pc_notice_msg', '公告测试');
INSERT INTO `share_config` VALUES ('pc_notice_time', '1504245919');
INSERT INTO `share_config` VALUES ('pc_update_u', 'http://www.baidu.com');
INSERT INTO `share_config` VALUES ('pc_update_v', '0.1');
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
INSERT INTO `share_feedback` VALUES ('1', '123123', '发到我范文芳违法二姑夫二个人挺好听基于亏空', '0', '1504455614');

-- ----------------------------
-- Table structure for share_group
-- ----------------------------
DROP TABLE IF EXISTS `share_group`;
CREATE TABLE `share_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '群组id',
  `group_name` varchar(255) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_group
-- ----------------------------
INSERT INTO `share_group` VALUES ('1', '普通用户');
INSERT INTO `share_group` VALUES ('2', '自行车');
INSERT INTO `share_group` VALUES ('3', '影视VIP1');
INSERT INTO `share_group` VALUES ('4', '管理员');
INSERT INTO `share_group` VALUES ('5', '摩托车');
INSERT INTO `share_group` VALUES ('6', '小汽车');

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
INSERT INTO `share_groupauth` VALUES ('3', '7');

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
INSERT INTO `share_ip` VALUES ('10.23.161.219', '1504490193', '-1');
INSERT INTO `share_ip` VALUES ('10.1.6.31', '1504490193', '-1');

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_log
-- ----------------------------
INSERT INTO `share_log` VALUES ('1', '1', '充值金额0.01元', '10', '1504449446', 'get:order=>VnUs_A81pXWw7JcNMVsbis0sbXQRNdHrMyiNb8rf2zsH1bMOVGQA8yXYtaW_p3oT,money=>0.01,remarks=>Farmer,s=>money, post: ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('2', '1', '充值金额0.01元', '10', '1504544792', 'get:order=>1NdwNXo83IK6RWXIJ2kDsz_nYxyURfpNUhjvvgv62LBfnUNQ3NMHwTpBC0WbLMj_,money=>0.01,remarks=>Farmer,s=>money, post: ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('3', '1', '充值金额0.01元', '10', '1504586203', 'get:order=>NPBB6yDOAXjhriCTyZz0N_zCYFkIjwIpC8fxz3IjRq6-prKJDnkBPArwbUplpqDk,money=>0.01,remarks=>Farmer,s=>money, post: ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('4', '1', '续费VIP消费14.9元', '10', '1504630421', 'get:s=>money, post:tid=>2,month=>1, ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('5', '1', '续费VIP消费19.9元', '10', '1504630466', 'get:s=>money, post:tid=>3,month=>1, ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('6', '1', '续费VIP消费9.9元', '10', '1504630557', 'get:s=>money, post:tid=>1,month=>1, ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('7', '1', '续费VIP消费9.9元', '10', '1504630571', 'get:s=>money, post:tid=>1,month=>1, ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('8', '1', '续费VIP消费19.9元', '10', '1504630579', 'get:s=>money, post:tid=>3,month=>1, ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('9', '1', '续费VIP消费9.9元', '10', '1504630595', 'get:s=>money, post:tid=>1,month=>1, ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('10', '1', '续费VIP消费19.9元', '10', '1504630607', 'get:s=>money, post:tid=>3,month=>1, ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('11', '1', '续费VIP消费19.9元', '10', '1504630631', 'get:s=>money, post:tid=>3,month=>1, ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('12', '1', '续费VIP消费59.6元', '10', '1504630662', 'get:s=>money, post:tid=>2,month=>4, ip:127.0.0.1');
INSERT INTO `share_log` VALUES ('13', '1', '充值金额0.01元', '10', '1504692273', 'get:order=>20170906200040011100940096747081,money=>0.01,remarks=>Farmer,s=>money, post: ip:127.0.0.1');

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
INSERT INTO `share_order` VALUES ('1NdwNXo83IK6RWXIJ2kDsz_nYxyURfpNUhjvvgv62LBfnUNQ3NMHwTpBC0WbLMj_', '1504544792', '0.0100', 'Farmer');
INSERT INTO `share_order` VALUES ('20170906200040011100940096747081', '1504692273', '0.0100', 'Farmer');
INSERT INTO `share_order` VALUES ('NPBB6yDOAXjhriCTyZz0N_zCYFkIjwIpC8fxz3IjRq6-prKJDnkBPArwbUplpqDk', '1504586203', '0.0100', 'Farmer');
INSERT INTO `share_order` VALUES ('VnUs_A81pXWw7JcNMVsbis0sbXQRNdHrMyiNb8rf2zsH1bMOVGQA8yXYtaW_p3oT', '1504449446', '0.0100', 'Farmer');

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
-- Table structure for share_set_meal
-- ----------------------------
DROP TABLE IF EXISTS `share_set_meal`;
CREATE TABLE `share_set_meal` (
  `tid` int(11) NOT NULL AUTO_INCREMENT COMMENT '套餐id',
  `group_id` int(11) NOT NULL COMMENT '群组id',
  `bandwidth` int(11) NOT NULL COMMENT '带宽',
  `description` text NOT NULL COMMENT '描述',
  `set_meal_money` double NOT NULL COMMENT '购买金额',
  PRIMARY KEY (`tid`),
  KEY `smg_id` (`group_id`),
  CONSTRAINT `smg_id` FOREIGN KEY (`group_id`) REFERENCES `share_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_set_meal
-- ----------------------------
INSERT INTO `share_set_meal` VALUES ('1', '2', '4', '4M带宽,满足日常需求,游戏,查资料,上百度,高清电影随意看', '9.9');
INSERT INTO `share_set_meal` VALUES ('2', '5', '7', '7M带宽,满足日常需求,游戏,看直播,超清电影不在话下', '14.9');
INSERT INTO `share_set_meal` VALUES ('3', '6', '10', '10M带宽,满足以上所有需求,适合经常下载东西的人使用', '19.9');

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
INSERT INTO `share_token` VALUES ('1', 'tgg1dyA01504245559', '1504246395');
INSERT INTO `share_token` VALUES ('1', 'ZTXlHAn51504246519', '1504246520');
INSERT INTO `share_token` VALUES ('1', 'n1EKljFw1504442904', '1504442904');
INSERT INTO `share_token` VALUES ('1', 'UkdjAoRb1504447379', '1504447379');
INSERT INTO `share_token` VALUES ('1', 'uiGhsrzg1504447379', '1504447650');
INSERT INTO `share_token` VALUES ('1', 'uwDt015x1504449100', '1504494618');
INSERT INTO `share_token` VALUES ('1', '3excqnUW1504451061', '1504451061');
INSERT INTO `share_token` VALUES ('1', 'JpkDzk481504497557', '1504497715');
INSERT INTO `share_token` VALUES ('1', 'hqwIk0eX1504541035', '1504541035');
INSERT INTO `share_token` VALUES ('1', 'POwfpqil1504544814', '1504613177');
INSERT INTO `share_token` VALUES ('1', 'bg7ksupR1504613193', '1504630864');
INSERT INTO `share_token` VALUES ('1', 'Fbp2zcsg1504692209', '1504692279');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of share_user
-- ----------------------------
INSERT INTO `share_user` VALUES ('1', 'Farmer', 'zouqin123', 'code.farmer@qq.com', '1500692842', '816.21');
INSERT INTO `share_user` VALUES ('2', 'admin', '123456789', 'test@qq.com', '1500994558', '0.00');
INSERT INTO `share_user` VALUES ('3', 'qwe123', 'qwe123', 'qqq@qq.com', '1503843239', '0.00');
INSERT INTO `share_user` VALUES ('6', 'qwe1234', 'qwe123', 'qq3q@qq.com', '1503847986', '0.00');
INSERT INTO `share_user` VALUES ('7', 'qwe123523', 'qwe123', 'dsfw3@qq.com', '1503848004', '0.00');
INSERT INTO `share_user` VALUES ('8', 'woceshi', 'qwe123', 'sdsdgv@qq.com', '1504434410', '0.00');
INSERT INTO `share_user` VALUES ('9', 'wrewer', 'qwe123', 'vsdf@qq.com', '1504444335', '0.00');

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
INSERT INTO `share_usergroup` VALUES ('2', '4', '-1');
INSERT INTO `share_usergroup` VALUES ('3', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('6', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('7', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('8', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('9', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('3', '2', '-1');
INSERT INTO `share_usergroup` VALUES ('1', '4', '-1');
INSERT INTO `share_usergroup` VALUES ('1', '3', '-1');
INSERT INTO `share_usergroup` VALUES ('1', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('1', '5', '-1');

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
