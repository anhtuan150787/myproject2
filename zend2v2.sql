/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : myproject2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-08-07 11:17:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu_parent` int(11) DEFAULT '0',
  `menu_status` tinyint(1) NOT NULL,
  `menu_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('71', '6666', '0', '1', '');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `users_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `users_fullname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `users_phone` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `users_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `users_registered` datetime NOT NULL,
  `users_status` tinyint(1) NOT NULL,
  `users_picture` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_salt` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `users_login_time` datetime DEFAULT NULL,
  `users_user_agent` text COLLATE utf8_unicode_ci,
  `group_users_id` smallint(6) NOT NULL,
  `users_forget_request_key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`users_id`,`users_email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'Anh Tuấn', '0944518855', 'anhtuan150787@gmail.com', 'e49ed34b584d6a4ac562647850517b13', '2017-08-02 00:00:00', '1', null, '65601daf9197d19f660233df5f7961c9', '2017-08-05 15:12:24', '', '1', '266e6c5b76ef440585b0f15235534ed6');
