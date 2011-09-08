<?php return array(
	'windApplication' => array(
		'path' => 'WIND:web.WindWebApplication',
		'scope' => 'singleton',
		'properties' => array(
			'dispatcher' => array(
				'ref' => 'dispatcher',
			),
			'handlerAdapter' => array(
				'ref' => 'router',
			),
		),
	),
	'windLogger' => array(
		'path' => 'WIND:log.WindLogger',
		'scope' => 'singleton',
		'destroy' => 'flush',
		'constructor-arg' => array(
			'0' => array(
				'value' => 'data.log',
			),
			'1' => array(
				'value' => '0',
			),
		),
	),
	'dispatcher' => array(
		'path' => 'WIND:web.WindDispatcher',
		'scope' => 'application',
	),
	'forward' => array(
		'path' => 'WIND:web.WindForward',
		'scope' => 'prototype',
		'properties' => array(
			'windView' => array(
				'ref' => 'windView',
			),
		),
	),
	'router' => array(
		'path' => 'WIND:router.WindRouter',
		'scope' => 'application',
	),
	'urlHelper' => array(
		'path' => 'WIND:web.WindUrlHelper',
		'scope' => 'application',
	),
	'windView' => array(
		'path' => 'WIND:viewer.WindView',
		'scope' => 'prototype',
		'config' => array(
			'template-dir' => 'template',
			'template-ext' => 'htm',
			'is-compile' => '0',
			'compile-dir' => 'compile.template',
			'is-cache' => '0',
		),
		'properties' => array(
			'viewResolver' => array(
				'ref' => 'viewResolver',
			),
			'viewCache' => array(
				'ref' => 'viewCache',
			),
		),
	),
	'viewResolver' => array(
		'path' => 'WIND:viewer.WindViewerResolver',
		'scope' => 'prototype',
		'properties' => array(
			'windLayout' => array(
				'ref' => 'layout',
			),
		),
	),
	'layout' => array(
		'path' => 'WIND:viewer.WindLayout',
		'scope' => 'prototype',
	),
	'template' => array(
		'path' => 'WIND:viewer.compiler.WindViewTemplate',
		'scope' => 'prototype',
	),
	'db' => array(
		'path' => 'WIND:db.WindConnection',
		'scope' => 'singleton',
		'config' => array(
			'resource' => 'db_config.xml',
		),
	),
	'errorMessage' => array(
		'path' => 'WIND:core.web.WindErrorMessage',
		'scope' => 'prototype',
	),
	'configParser' => array(
		'path' => 'WIND:parser.WindConfigParser',
		'scope' => 'singleton',
	),
	'windCache' => array(
		'path' => 'WIND:cache.strategy.WindFileCache',
		'scope' => 'singleton',
		'config' => array(
			'dir' => 'data.caches',
			'suffix' => 'php',
			'expires' => '0',
		),
	),
);