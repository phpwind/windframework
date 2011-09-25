--Table name: user 用户表
--Field: userid 用户id
--Field: username 用户名
--Field: password 密码
--Primary key userid

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
   `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `username` varchar(50) NOT NULL DEFAULT '',
   `password` varchar(50) NOT NULL DEFAULT '',
   PRIMARY KEY (`userid`)
 ) ENGINE=MyISAM;