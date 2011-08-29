<?php return array(
	'windWebApp' => array(
		'path' => 'WIND:core.web.WindWebApplication',
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
		'path' => 'COM:log.WindLogger',
		'scope' => 'singleton',
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
		'path' => 'WIND:core.web.WindDispatcher',
		'scope' => 'application',
	),
	'forward' => array(
		'path' => 'WIND:core.web.WindForward',
		'scope' => 'prototype',
		'properties' => array(
			'windView' => array(
				'ref' => 'windView',
			),
		),
	),
	'router' => array(
		'path' => 'COM:router.WindRouter',
		'scope' => 'application',
	),
	'urlHelper' => array(
		'path' => 'WIND:core.web.WindUrlHelper',
		'scope' => 'application',
	),
	'windView' => array(
		'path' => 'COM:viewer.WindView',
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
		'path' => 'COM:viewer.WindViewerResolver',
		'scope' => 'prototype',
		'properties' => array(
			'windLayout' => array(
				'ref' => 'layout',
			),
		),
	),
	'layout' => array(
		'path' => 'COM:viewer.WindLayout',
		'scope' => 'prototype',
	),
	'template' => array(
		'path' => 'COM:viewer.compiler.WindViewTemplate',
		'scope' => 'prototype',
	),
	'db' => array(
		'path' => 'COM:db.WindConnection',
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
		'path' => 'COM:parser.WindConfigParser',
		'scope' => 'singleton',
	),
	'windCache' => array(
		'path' => 'COM:cache.strategy.WindFileCache',
		'config' => array(
			'dir' => 'data.config',
			'suffix' => 'php',
			'expires' => '0',
		),
	),
	'viewCache' => array(
		'path' => 'COM:cache.strategy.WindFileCache',
		'config' => array(
			'dir' => 'data.view',
			'suffix' => 'php',
			'expires' => '10',
		),
	),
);