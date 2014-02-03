CREATE DATABASE jtest;
USE jtest;

DROP TABLE IF EXISTS app;
CREATE TABLE `app` (
  `app_id` int(20) NOT NULL,
  `secret` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(20) NOT NULL,
  `user_country` char(2) NOT NULL,
  `user_locale` char(5) NOT NULL,
  `user_age` int(3) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `app_user`;
CREATE TABLE `app_user` (
  `app_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `user_score` int(20) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`app_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_user` ADD INDEX (`app_id`);
ALTER TABLE `app_user` ADD INDEX (`user_id`);
ALTER TABLE `app_user` ADD INDEX (`created_on`);
