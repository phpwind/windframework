<?php
 return array(
	'applications' => array(
		'web' => array(
			'class' => 'WIND:core.WindWebApplication',
		),
		'command' => array(
			'class' => 'WIND:core.WindCommandApplication',
		),
	),
	'modules' => array(
		'default' => array(
			'path' => 'actionControllers',
			'template' => 'default',
			'controllerSuffix' => 'controller',
			'actionSuffix' => 'action',
			'method' => 'run',
		),
		'other' => array(
			'path' => 'otherControllers',
			'template' => 'wind',
			'controllerSuffix' => 'controller',
			'actionSuffix' => 'action',
			'method' => 'run',
		),
	),
	'error' => array(
		'default' => 'WIND:core.WindErrorAction',
	),
	'filters' => array(
		'WindFormFilter' => array(
			'filterPath' => 'WIND:core.filter.WindFormFilter',
		),
	),
	'templates' => array(
		'default' => array(
			'dir' => 'template',
			'default' => 'index',
			'ext' => 'htm',
			'resolver' => 'default',
			'isCache' => '0',
			'cacheDir' => 'cache',
			'compileDir' => 'compile',
		),
		'wind' => array(
			'dir' => 'template',
			'default' => 'index',
			'ext' => 'htm',
			'resolver' => 'default',
			'isCache' => '0',
			'cacheDir' => 'cache',
			'compileDir' => 'compile',
		),
	),
	'viewerResolvers' => array(
		'default' => 'WIND:core.viewer.WindViewer',
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
			'path' => 'WIND:core.router.WindUrlBasedRouter',
		),
	),
	'rootPath' => 'D:\\PHPAPP\\phpwindframework/_tests/component/config',
);
?>