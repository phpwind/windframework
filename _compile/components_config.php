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
		'scope' => 'application',
		'constructor-arg' => array(
			'0' => array(
				'value' => '',
			),
			'1' => array(
				'value' => '0',
			),
		),
	),
	'dispatcher' => array(
		'path' => 'WIND:core.web.WindDispatcher',
		'scope' => 'application',
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
			'is-compile' => '',
			'compile-dir' => 'compile.template',
			'is-cache' => '',
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
			'dir' => 'compile',
			'dbconfig-name' => 'default',
			'table-name' => 'cache',
			'field-key' => 'key',
			'field-value' => 'value',
			'field-expire' => 'expire',
		),
	),
	'viewCache' => array(
		'path' => 'COM:cache.strategy.WindFileCache',
		'config' => array(
			'dir' => 'compile.cache',
			'suffix' => 'php',
			'expires' => '10',
		),
	),
);