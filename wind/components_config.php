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
	'router' => array(
		'path' => 'COM:router.WindRouter',
		'scope' => 'prototype',
	),
	'urlRewriteRouter' => array(
		'path' => 'COM:router.WindUrlRewriteRouter',
		'scope' => 'singleton',
	),
	'urlHelper' => array(
		'path' => 'WIND:core.web.WindUrlHelper',
		'scope' => 'singleton',
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