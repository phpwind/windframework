<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
return array(
	'connections' => array(
		'phpwind' => array(
			'driver' => 'mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password' => 'root',
			'port' => '3306',
			'name' => 'phpwind803',
			'charset' => 'utf8',
		),
	),
	'drivers' => array(
		'mysql' => array(
			'builder' => 'mysql',
			'class' => 'WIND:component.db.drivers.mysql.WindMySql',
		),
	),
	'builders' => array(
		'mysql' => array(
			'class' => 'WIND:component.db.drivers.mysql.WindMySqlBuilder',
		),
	),
);