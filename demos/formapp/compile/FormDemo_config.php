<?php
 return array(
	'app' => array(
		'name' => 'FormDemo',
		'configPath' => 'D:\\PHPAPP\\phpwindframework\\demos\\formapp\\compile\\FormDemo_config.php',
		'rootPath' => 'D:\\PHPAPP\\phpwindframework\\demos\\formapp',
	),
	'modules' => array(
		'default' => array(
			'path' => 'controllers',
		),
	),
	'filters' => array(
		'WindFormFilter' => array(
			'filterPath' => 'WIND:component.form.WindFormFilter',
		),
	),
	'template' => array(
		'dir' => 'templates',
		'default' => 'index',
		'ext' => 'html',
		'resolver' => 'default',
		'isCache' => 'false',
		'cacheDir' => 'cache',
		'compileDir' => 'compile',
	),
	'applications' => array(
		'web' => array(
			'class' => 'WIND:core.WindWebApplication',
		),
		'command' => array(
			'class' => 'WIND:core.WindCommandApplication',
		),
	),
	'errorMessage' => array(
		'errorAction' => 'WIND:core.WindErrorAction',
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