
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `bc_msg`;
CREATE TABLE `bc_msg` (
  `when` datetime NOT NULL,
  `sender` varchar(32) NOT NULL,
  `content` tinytext NOT NULL,
  KEY `when` (`when`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bc_user`;
CREATE TABLE `bc_user` (
  `nick` varchar(32) NOT NULL,
  `salt` varchar(40) NOT NULL,
  `secret` varchar(40) NOT NULL,
  `firstSeen` datetime NOT NULL,
  PRIMARY KEY (`nick`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
