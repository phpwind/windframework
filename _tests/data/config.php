<?php
  return array(
	'wind' => array(
		'imports' => array(
			'components' => array(
				'resource' => 'TEST:data.component_config',
				'suffix' => 'xml',
				'init-delay' => 'false',
				'is-append' => 'true',
			),
			'classes' => array(
				'resource' => 'TEST:data.classes_config',
				'suffix' => 'xml',
				'init-delay' => 'false',
				'is-append' => 'true',
			),
		),
		'web-apps' => array(
			'testApp' => array(
				'class' => 'windWebApp',
				'root-path' => '',
				'factory' => array(
					'class-definition' => 'components',
					'class' => 'WIND:core.factory.WindComponentFactory',
				),
				'filters' => array(
					'class' => 'WIND:core.filter.WindFilterChain',
					'filter1' => array(
						'class' => 'WIND:core.web.filter.WindLoggerFilter',
					),
				),
				'router' => array(
					'class' => 'urlBasedRouter',
				),
				'modules' => array(
					'default' => array(
						'path' => 'actionControllers',
						'default' => array(
							'path' => 'template',
							'ext' => 'htm',
							'view-resolver' => array(
								'class' => 'WIND:core.viewer.WindViewer',
								'is-cache' => 'false',
								'cache-dir' => 'cache',
								'compile-dir' => 'compile',
							),
						),
					),
				),
			),
		),
	),
	'components' => array(
		'windWebApp' => array(
			'path' => 'WIND:core.web.WindWebApplication',
			'scope' => 'request',
			'proxy' => 'true',
		),
		'windLogger' => array(
			'path' => 'WIND:component.log.WindLogger',
			'scope' => 'request',
			'config' => array(
				'path' => '',
			),
		),
		'urlBasedRouter' => array(
			'path' => 'WIND:core.router.WindUrlBasedRouter',
			'scope' => 'application',
			'proxy' => 'true',
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
		'viewResolver' => array(
			'path' => 'WIND:core.viewer.WindViewer',
			'scope' => 'request',
		),
		'db' => array(
			'path' => 'WIND:component.db.WindConnectionManager',
			'scope' => 'singleton',
		),
	),
);
?>