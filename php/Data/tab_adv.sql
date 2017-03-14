/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : 192.168.31.207:3306
Source Database       : db_data37

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-08-10 15:00:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tab_adv
-- ----------------------------
DROP TABLE IF EXISTS `tab_adv`;
CREATE TABLE `tab_adv` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '广告名称',
  `pos_id` int(11) NOT NULL COMMENT '广告位置',
  `data` text NOT NULL COMMENT '图片地址',
  `click_count` int(11) NOT NULL COMMENT '点击量',
  `url` varchar(500) NOT NULL COMMENT '链接地址',
  `sort` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `start_time` int(11) DEFAULT NULL,
  `end_time` int(11) unsigned DEFAULT '0' COMMENT '结束时间',
  `target` varchar(20) DEFAULT '_blank',
  `mini_pic` int(10) NOT NULL COMMENT '缩略图',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='广告表';

-- ----------------------------
-- Records of tab_adv
-- ----------------------------
INSERT INTO `tab_adv` VALUES ('2', '首页轮播图1', '1', '69', '0', 'http://www.vlcms.com/', '0', '1', '0', '2016', '2016', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('3', '首页轮播图2', '1', '68', '0', 'http://www.vlcms.com/', '0', '1', '0', '2016', '2016', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('22', '游戏中心轮播图3', '7', '206', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '207');
INSERT INTO `tab_adv` VALUES ('23', '游戏中心轮播图4', '7', '208', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '209');
INSERT INTO `tab_adv` VALUES ('19', '首页轮播图4', '1', '70', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('24', '游戏中心轮播图5', '7', '210', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '211');
INSERT INTO `tab_adv` VALUES ('20', '游戏中心轮播图1', '7', '202', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '203');
INSERT INTO `tab_adv` VALUES ('21', '游戏中心轮播图2', '7', '204', '0', 'http://www.vlcms.com/', '0', '1', '0', '0', '0', '_blank', '205');
INSERT INTO `tab_adv` VALUES ('18', '首页轮播图3', '1', '67', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('25', '礼包中心广告图1', '9', '212', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('26', '《霸道天下》新手礼包', '10', '213', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('27', '《青丘狐传说》钻石礼包', '11', '198', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('28', '《大话西游》媒体礼包', '12', '214', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('29', '《武神赵子龙》新手礼包', '13', '215', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('30', '《天将雄狮》钻石礼包', '14', '216', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('31', '《功夫熊猫3》媒体礼包', '15', '217', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
INSERT INTO `tab_adv` VALUES ('32', '媒体首页底部广告', '16', '222', '0', 'http://www.vlcms.com/', '0', '1', '0', null, '0', '_blank', '0');
