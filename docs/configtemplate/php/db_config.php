<?php
/**
 * @author xiaoxia xu <xiaoxa.xuxx@aliyun-inc.com> 2011-3-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
/**
 * dbConfig的配置
 * 可以配置多个链接，多个链接的时候必需指定每个链接的type类型是master/slave，当配置一个的时候可以不配置type项 
 */
return array(
	'conn1' => array(
		'dsn' => 'mysql:host=localhost;dbname=test',
		'user' => 'root',
		'pwd' => 'root',
		'charset' => 'utf8',
		'tablePrefix' => 'pw_'
	),
	'manage' => array(
		'class' => 'COM:db.WindConnectionManager',
		'db1' => array(
			'dsn' => 'mysql:host=localhost;dbname=test',
			'user' => 'root',
			'pwd' => 'root',
			'charset' => 'utf8',
			'tablePrefix' => 'pw_'
		),
	),
);