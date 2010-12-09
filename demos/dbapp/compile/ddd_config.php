<?php
 return array(
	'app' => array(
		'name' => 'ddd',
		'rootPath' => 'E:\\www\\bbs\\phpwind_wind\\demos\\dbapp',
		'configPath' => 'E:\\www\\bbs\\phpwind_wind\\demos\\dbapp/compile/\\ddd_config.php',
	),
	'modules' => array(
		'default' => array(
			'name' => 'default',
			'path' => 'actionControllers',
		),
	),
	'database' => array(
		'connections' => array(
			'phpwind_8' => array(
				'driver' => 'mysql',
				'type' => 'master',
				'host' => 'localhost',
				'user' => 'root',
				'password' => 'suqian0512h',
				'port' => '3306',
				'name' => 'phpwind_8',
			),
			'phpwind_beta' => array(
				'driver' => 'mysql',
				'type' => 'slave',
				'host' => 'localhost',
				'user' => 'root',
				'password' => 'suqian0512h',
				'port' => '3306',
				'name' => 'phpwind_beta',
			),
			'user' => array(
				'driver' => 'mssql',
				'type' => 'slave',
				'host' => 'localhost',
				'user' => 'sa',
				'password' => '151@suqian',
				'name' => 'phpwind',
			),
		),
		'drivers' => array(
			'mysql' => array(
				'builder' => 'mysql',
				'class' => 'WIND:component.db.drivers.mysql.WindMySql',
			),
			'mssql' => array(
				'builder' => 'mssql',
				'class' => 'WIND:component.db.drivers.mssql.WindMsSql',
			),
		),
		'builders' => array(
			'mysql' => array(
				'class' => 'WIND:component.db.drivers.mysql.WindMySqlBuilder',
			),
			'mssql' => array(
				'class' => 'WIND:component.db.drivers.mssql.WindMsSqlBuilder',
			),
		),
	),
	'applications' => array(
		'web' => array(
			'name' => 'default',
			'class' => 'WIND:core.WindWebApplication',
		),
		'command' => array(
			'name' => 'default',
			'class' => 'WIND:core.WindCommandApplication',
		),
	),
	'errorMessage' => array(
		'errorAction' => 'WIND:core.WindErrorAction',
	),
	'filters' => array(
		'WindFormFilter' => array(
			'filterName' => 'WindFormFilter',
			'filterPath' => 'WIND:component.form.WindFormFilter',
		),
	),
	'template' => array(
		'path' => 'template',
		'name' => 'index',
		'ext' => 'htm',
		'resolver' => 'default',
		'isCache' => 'false',
		'cacheDir' => 'cache',
		'compileDir' => 'compile',
	),
	'viewerResolvers' => array(
		'default' => 'WIND:component.viewer.WindViewer',
		'pw' => 'WIND:component.viewer.WindPWViewer',
		'smarty' => 'libs.WindSmarty',
	),
	'router' => array(
		'parser' => 'url',
	),
	'routerParsers' => array(
		'url' => array(
			'rule' => array(
				'a' => 'run',
				'c' => 'index',
				'm' => 'default',
			),
			'path' => 'WIND:component.router.WindUrlBasedRouter',
		),
	),
);
?>