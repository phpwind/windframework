<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
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
			'suffix' => 'controller',
			'controllerSuffix' => 'controller',
			'actionSuffix' => 'action',
			'method' => 'run',
		),
	),
	'error' => array(
		'isMerge' => 'true',
		'default' => 'WIND:core.WindErrorAction',
	),
	'filters' => array(
		'isMerge' => 'true',
		'WindFormFilter' => array(
			'filterPath' => 'WIND:component.form.WindFormFilter',
		),
	),
	'templates' => array(
		'default' => array(
			'dir' => 'template',
			'default' => 'index',
			'ext' => 'htm',
			'resolver' => 'default',
			'isCache' => 'false',
			'cacheDir' => 'cache',
			'compileDir' => 'compile',
		),
		'wind' => array(
			'dir' => 'template',
			'default' => 'index',
			'ext' => 'htm',
			'resolver' => 'default',
			'isCache' => 'false',
			'cacheDir' => 'cache',
			'compileDir' => 'compile',
		),
	),
	'viewerResolvers' => array(
		'isMerge' => 'true',
		'default' => 'WIND:core.viewer.WindViewer',
	),
	'router' => array(
		'parser' => 'url',
	),
	'routerParsers' => array(
		'isMerge' => 'true',
		'url' => array(
			'rule' => array(
				'a' => 'run',
				'c' => 'index',
				'm' => 'default',
			),
			'path' => 'WIND:core.router.WindUrlBasedRouter',
		),
	),
	'extensionConfig' => array(
		'formConfig' => 'WIND:component.form.form_config',
		'dbConfig' => 'WIND:component.form.form_config',
	)
);
?>