<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
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
	    'isMerge' => 'true',
	),
	'filters' => array(
		'WindFormFilter' => array(
			'filterPath' => 'WIND:core.filter.WindFormFilter',
		),
	    'isMerge' => 'true',
	),
	'templates' => array(
		'default' => array(
			'dir' => 'front',
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
		'default' => 'WIND:core.viewer.WindViewer',
	    'isMerge' => 'true',
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
);
?>