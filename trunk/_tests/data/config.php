<?php
 return array(
 	'rootPath' => '',
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
		'default' => array(
			'class' => 'WIND:core.WindErrorAction',
		),
	),
	'filters' => array(
		'WindFormFilter' => array(
			'class' => 'WIND:core.filter.WindFormFilter',
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
		'default' => array(
			'class' => 'WIND:core.viewer.WindViewer',
		),
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
			'class' => 'WIND:core.router.WindUrlBasedRouter',
		),
	),
	'extensionConfig' => array(
		'formConfig' => 'WIND:component.form.form_config',
		'dbConfig' => 'WIND:component.form.db_config',
	),
);
?>