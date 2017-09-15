/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : stushare

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-09-15 18:34:17
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
) ENGINE=InnoDB AUTO_INCREMENT=3280 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_accounting
-- ----------------------------

-- ----------------------------
-- Table structure for share_auth
-- ----------------------------
DROP TABLE IF EXISTS `share_auth`;
CREATE TABLE `share_auth` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_interface` varchar(255) NOT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

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
INSERT INTO `share_config` VALUES ('email', '1505470177');
INSERT INTO `share_config` VALUES ('movie_notice_msg', '内侧期间送VIP全场免费！！\n独播每天都会更新不同的影片\n有什么想看的可以去设置的反馈里申请哦\n目前支持 搜狐 芒果 优酷\n爱奇艺影视即将支持\n\n交流群：175853183');
INSERT INTO `share_config` VALUES ('movie_notice_time', '1505371984');
INSERT INTO `share_config` VALUES ('movie_update_u', '1');
INSERT INTO `share_config` VALUES ('movie_update_v', '2');
INSERT INTO `share_config` VALUES ('pc_notice_msg', '运行快两个星期了,不知道大家有什么感受\n希望大家能够去官网或者客户端反馈意见\n这样我们能更好的完善\nhttp://sv.icodef.com/user/index/feedback');
INSERT INTO `share_config` VALUES ('pc_notice_time', '1505371984');
INSERT INTO `share_config` VALUES ('pc_update_u', 'http://sv.icodef.com/static/win.zip');
INSERT INTO `share_config` VALUES ('pc_update_v', '1.0');
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
  CONSTRAINT `share_feedback_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_feedback
-- ----------------------------
INSERT INTO `share_feedback` VALUES ('1', '测试', 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest', '-1', '1504525514');
INSERT INTO `share_feedback` VALUES ('2', 'admin', 'qqq1qqqqqqq1qqqqqqqqqqqqqqq', '-1', '1505288673');

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
INSERT INTO `share_group` VALUES ('3', '影视VIP');
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
  CONSTRAINT `share_groupauth_ibfk_1` FOREIGN KEY (`auth_id`) REFERENCES `share_auth` (`auth_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `share_groupauth_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `share_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
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
INSERT INTO `share_groupauth` VALUES ('2', '6');
INSERT INTO `share_groupauth` VALUES ('3', '7');
INSERT INTO `share_groupauth` VALUES ('5', '6');
INSERT INTO `share_groupauth` VALUES ('6', '6');

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
INSERT INTO `share_ip` VALUES ('127.0.0.1', '1505471391', '1');
INSERT INTO `share_ip` VALUES ('::1', '1505471209', '1');

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
  CONSTRAINT `share_log_ibfk_1` FOREIGN KEY (`log_uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_log
-- ----------------------------
INSERT INTO `share_log` VALUES ('1', '1', '充值金额0.01元', '10', '1504488595', 'get:order=>tqh1e4aH_GVejHvzp4Ubgyjnn2w0zsXaUX6YFvpbrqmQkODLRkyKlfGGtLDWuUmk,money=>0.01,remarks=>Farmer,s=>money, post: ip:::1');
INSERT INTO `share_log` VALUES ('3', '1', '充值金额0.01元', '10', '1504586244', 'get:order=>UXw1ql5WydB7Qe18rNHooqM8DpmPrlxjQueFnFvGzlYd_AGCL0qKCBMI6LWzyDms,money=>0.01,remarks=>Farmer,s=>money, post: ip:::1');
INSERT INTO `share_log` VALUES ('4', '1', '充值金额0.01元', '10', '1504611711', 'get:order=>9aCbj5xbk_UpJ2W1uODIdEGdc_NLtlq9kvXKNq0CctvMtE6ujEQGRhPIh2ROx96F,money=>0.01,remarks=>farmer,s=>money, post: ip:::1');
INSERT INTO `share_log` VALUES ('6', '1', '充值金额0.01元', '10', '1504692587', 'get:order=>20170906200040011100940096747081,money=>0.01,remarks=>Farmer,s=>money, post: ip:::1');
INSERT INTO `share_log` VALUES ('7', '1', '充值金额1元', '10', '1504702022', 'get:order=>20170906200040011100940096855966,money=>1.00,remarks=>Farmer,s=>money, post: ip:::1');
INSERT INTO `share_log` VALUES ('27', '2', '续费VIP消费14.9元', '10', '1504763367', 'get:s=>money, post:tid=>2,month=>1, ip:10.23.161.219');

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
-- Table structure for share_send_email
-- ----------------------------
DROP TABLE IF EXISTS `share_send_email`;
CREATE TABLE `share_send_email` (
  `uid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  KEY `email_uid` (`uid`),
  CONSTRAINT `email_uid` FOREIGN KEY (`uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_send_email
-- ----------------------------
INSERT INTO `share_send_email` VALUES ('1', '1505470177', '3');

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
INSERT INTO `share_server` VALUES ('1', '10.1.6.31', '主线路', 0x636C69656E740A6465762074756E0A70726F746F207463700A72656D6F74652073762E69636F6465662E636F6D20313139340A7265736F6C762D726574727920696E66696E6974650A6E6F62696E640A706572736973742D6B65790A706572736973742D74756E0A3C63613E0A2D2D2D2D2D424547494E2043455254494649434154452D2D2D2D2D0A4D4949444B7A434341684F67417749424167494A41495A62736478414E71392F4D413047435371475349623344514542437755414D424D784554415042674E560A42414D5443484E3064564E6F59584A6C4D423458445445334D4459774E7A45304E44557A4E6C6F58445449334D4459774E5445304E44557A4E6C6F77457A45520A4D4138474131554541784D496333523155326868636D5577676745694D4130474353714753496233445145424151554141344942447741776767454B416F49420A41514371414D6F4D63353059492B5041585271732F365671775A47443862465462653938744366524A5A35784550754A44357931476A4C4E53774957493747320A774E465758387A456B57747A594F5267565257677079334255594E5A635554644B61564F584A6D68496D457476485438775A69534A6E376D4441656F6E6B2F300A4F43754F77436A6D304239547141393770595549544557314F33596532524A32727A416653424248564D7969496B7A72564B3359672F7775735270784D372F510A504C57714A66302B6B646544363572647362686B426671714D6944694C507233756D394E5151787559334B44584E78736E35576B4531664F655257614D7666430A63594A4E4A6A705238624375527931314C6867505946522F706B41676C637A366A5A4E715A327065716C5A464347412F3941544D6947593168337961743556440A7579413130706D37546E7741324F3272324762464332727A41674D424141476A67594577667A416442674E564851344546675155375034366B594C33374B31650A31306351453054725635425337576F77517759445652306A424477774F6F4155375034366B594C33374B316531306351453054725635425337577168463651560A4D424D784554415042674E5642414D5443484E3064564E6F59584A6C67676B41686C75783345413272333877444159445652305442415577417745422F7A414C0A42674E564851384542414D43415159774451594A4B6F5A496876634E4151454C4251414467674542414A5468626B59746C72726475354F6F71683539424877700A4D734C6A524B4C7757586F6751756439536E6247637177664257356E4746797432594A496766516E48727A376979677A706D4E6D7166632F676F306E7652776E0A69756868774C53584451486F705831727261676954783344307A596835376E56305458427135693755514F5A41366D307661476D4F4A7755704F5346357850420A5A5A6D66356C582B4A756E594B614B55757A6B3634304E305870644C2F665331784774376B4C74796B666242316631504474744455594D3849526532676D73660A584A30477064356C7453356842794952745A36563135416E64746465534A63586A353737554A6C786356505331306D46513844504F78426547646647524774750A342F7A4A5776326F4250655A386A533537384B716F384C626D4971427356774C6A707273654F7741654C737A7678757033724A316152486B364E4C35746D383D0A2D2D2D2D2D454E442043455254494649434154452D2D2D2D2D0A3C2F63613E0A636F6D702D6C7A6F0A7665726220330A617574682D757365722D706173730A);
INSERT INTO `share_server` VALUES ('2', '10.127.133.146', '二号线路(来人啊)', 0x636C69656E740A6465762074756E0A70726F746F207463700A72656D6F74652031302E3132372E3133332E31343620313139340A7265736F6C762D726574727920696E66696E6974650A6E6F62696E640A706572736973742D6B65790A706572736973742D74756E0A3C63613E0A2D2D2D2D2D424547494E2043455254494649434154452D2D2D2D2D0A4D4949434754434341594B67417749424167494A414C756B56377179654776664D413047435371475349623344514542437755414D4138784454414C42674E560A42414D4D42484A76623351774868634E4D5463774E544D784D5445794D6A41345768634E4D6A63774E5449354D5445794D6A4134576A41504D513077437759440A5651514444415279623239304D4947664D413047435371475349623344514542415155414134474E4144434269514B4267514469436D4E543642696E4A6E304F0A37526A623834642F713258676F4341504A5472596D6A66687678766D654E6855724334556E694A4A356D662F4C4B496B3256495777742B61577A516B623236550A5239626F7179456A77504E76396B52412F464B5873576357737869795245432F454C337133302F662F342B69443765454E4355715746704A304A62793945794A0A49504E316F4C3172574641634470313752637233477545434B45397767774944415141426F333077657A416442674E5648513445466751554249616D4C754C650A446B4D766967706E5877676E5471655174325577507759445652306A424467774E6F41554249616D4C754C65446B4D766967706E5877676E54716551743257680A453651524D4138784454414C42674E5642414D4D42484A76623353434351433770466536736E6872337A414D42674E5648524D45425441444151482F4D4173470A413155644477514541774942426A414E42676B71686B6947397730424151734641414F4267514379477A79665255476F483432585144583130545A37525555590A464A595279424A72646F376D6245764642772B6E5078344C7443654F4F2F66315A65777A3141666B3935413564785966444C48536C745A345052666A316F4D6F0A4E37445348667A6231545531637576644B582B7565576274684771504A446F4A625352506C674C707645527A5736656258634577533879584170326B73746E660A3530695662687A6E614362556939624155773D3D0A2D2D2D2D2D454E442043455254494649434154452D2D2D2D2D0A3C2F63613E0A636F6D702D6C7A6F0A7665726220330A617574682D757365722D706173730A);

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
  CONSTRAINT `share_token_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_token
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of share_user
-- ----------------------------
INSERT INTO `share_user` VALUES ('1', 'Farmer', 'zouqin123', 'code.farmer@qq.com', '1504440899', '6.04');
INSERT INTO `share_user` VALUES ('2', 'admin', '741852963', '98765@qq.qq', '1504490641', '0.00');

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
  CONSTRAINT `share_usergroup_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `share_user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `share_usergroup_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `share_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_usergroup
-- ----------------------------
INSERT INTO `share_usergroup` VALUES ('1', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('1', '6', '1505555933');
INSERT INTO `share_usergroup` VALUES ('2', '1', '-1');
INSERT INTO `share_usergroup` VALUES ('2', '4', '-1');
INSERT INTO `share_usergroup` VALUES ('1', '4', '-1');
INSERT INTO `share_usergroup` VALUES ('1', '3', '-1');
INSERT INTO `share_usergroup` VALUES ('2', '3', '-1');

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
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of share_video
-- ----------------------------
