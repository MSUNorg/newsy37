/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : 192.168.31.207:3306
Source Database       : db_data37

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-08-10 15:01:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tab_adv_pos
-- ----------------------------
DROP TABLE IF EXISTS `tab_adv_pos`;
CREATE TABLE `tab_adv_pos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) NOT NULL,
  `title` char(80) NOT NULL DEFAULT '' COMMENT '广告位置名称',
  `module` varchar(100) NOT NULL COMMENT '所在模块 模块/控制器/方法',
  `type` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '广告位类型 \r\n1.单图\r\n2.多图\r\n3.文字链接\r\n4.代码',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常）',
  `data` varchar(500) NOT NULL COMMENT '额外的数据',
  `width` char(20) NOT NULL DEFAULT '' COMMENT '广告位置宽度',
  `height` char(20) NOT NULL DEFAULT '' COMMENT '广告位置高度',
  `margin` varchar(50) NOT NULL COMMENT '边缘',
  `padding` varchar(50) NOT NULL COMMENT '留白',
  `theme` varchar(50) NOT NULL DEFAULT 'all' COMMENT '适用主题，默认为all，通用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='广告位置表';

-- ----------------------------
-- Records of tab_adv_pos
-- ----------------------------
INSERT INTO `tab_adv_pos` VALUES ('1', 'slider_media', '媒体首页轮播图', 'media', '1', '1', '', '1200px', '300px', '0', '0', 'all');
INSERT INTO `tab_adv_pos` VALUES ('2', 'index_top_media', '媒体首页顶部广告', 'media', '1', '0', '', '120px', '50px', '0', '0', 'all');
INSERT INTO `tab_adv_pos` VALUES ('3', 'slider_app', 'app首页轮播图', 'app', '3', '1', '', '120px', '30px', '0', '0', 'all');
INSERT INTO `tab_adv_pos` VALUES ('7', 'slider_game_media', '游戏中心轮播图', 'media', '2', '1', '', '810', '320', '', '', 'all');
INSERT INTO `tab_adv_pos` VALUES ('16', 'index_bottom_media', '媒体首页底部广告', 'media', '1', '1', '', '390', '160', '', '', 'all');
INSERT INTO `tab_adv_pos` VALUES ('9', 'gift_top_media', '礼包中心宣传位#1', 'media', '1', '1', '', '320', '320', '', '', 'all');
INSERT INTO `tab_adv_pos` VALUES ('10', 'gift_top_media', '礼包中心宣传位#2', 'media', '1', '1', '', '253', '159', '', '', 'all');
INSERT INTO `tab_adv_pos` VALUES ('11', 'gift_top_media', '礼包中心宣传位#3', 'media', '1', '1', '', '253', '159', '', '', 'all');
INSERT INTO `tab_adv_pos` VALUES ('12', 'gift_top_media', '礼包中心宣传位#4', 'media', '1', '1', '', '265', '320', '', '', 'all');
INSERT INTO `tab_adv_pos` VALUES ('13', 'gift_top_media', '礼包中心宣传位#5', 'media', '1', '1', '', '416', '159', '', '', 'all');
INSERT INTO `tab_adv_pos` VALUES ('14', 'gift_top_media', '礼包中心宣传位#6', 'media', '1', '1', '', '207', '158', '', '', 'all');
INSERT INTO `tab_adv_pos` VALUES ('15', 'gift_top_media', '礼包中心宣传位#7', 'media', '1', '1', '', '207', '158', '', '', 'all');
