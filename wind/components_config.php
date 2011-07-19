<?php return array(
	'windWebApp' => array(
		'path' => 'WIND:core.web.WindWebApplication',
		'scope' => 'singleton',
		'properties' => array(
			'dispatcher' => array(
				'ref' => 'dispatcher',
			),
		),
	),
	'dispatcher' => array(
		'path' => 'WIND:core.web.WindDispatcher',
		'scope' => 'prototype',
		'properties' => array(
			'urlHelper' => array(
				'ref' => 'urlHelper',
			),
		),
	),
	'forward' => array(
		'path' => 'WIND:core.web.WindForward',
		'scope' => 'prototype',
	),
	'urlBasedRouter' => array(
		'path' => 'WIND:core.router.WindUrlBasedRouter',
		'scope' => 'prototype',
		'config' => array(
			'module' => array(
				'url-param' => 'm',
				'default-value' => 'default',
			),
			'controller' => array(
				'url-param' => 'c',
				'default-value' => 'index',
			),
			'action' => array(
				'url-param' => 'a',
				'default-value' => 'run',
			),
		),
	),
	'urlHelper' => array(
		'path' => 'WIND:core.web.WindUrlHelper',
		'scope' => 'singleton',
		'properties' => array(
			'windRouter' => array(
				'ref' => 'urlBasedRouter',
			),
		),
		'config' => array(
			'url-pattern' => array(
				'value' => '-/',
			),
			'route-suffix' => array(
				'value' => 'htm',
			),
			'route-param' => array(
				'value' => 'r',
			),
		),
	),
	'windView' => array(
		'init-method' => 'init',
		'path' => 'COM:viewer.WindView',
		'scope' => 'prototype',
		'config' => array(
			'template-dir' => array(
				'value' => 'template',
			),
			'template-ext' => array(
				'value' => 'htm',
			),
			'is-cache' => array(
				'value' => 'true',
			),
		),
		'properties' => array(
			'viewResolver' => array(
				'ref' => 'viewResolver',
			),
		),
	),
	'viewResolver' => array(
		'path' => 'COM:viewer.WindViewerResolver',
		'scope' => 'prototype',
		'properties' => array(
			'urlHelper' => array(
				'ref' => 'urlHelper',
			),
		),
	),
	'template' => array(
		'path' => 'COM:viewer.compiler.WindViewTemplate',
		'scope' => 'prototype',
		'config' => array(
			'resource' => '',
		),
	),
	'errorMessage' => array(
		'path' => 'WIND:core.web.WindErrorMessage',
		'scope' => 'prototype',
	),
);