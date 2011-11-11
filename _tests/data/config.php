<?php
return array(
	'web-apps' => array(
		'test' => array(
			'modules' => array(
				'default' => array(
					'controller-path' => 'controller', 
					'controller-suffix' => 'Controller',
				)
			), 
			'components' => array(
				'db' => array(
					'config' => array(
						'resource' => 'data.db_config.php'
					),
				),
			),
		),
		'shilong' => array(
			'modules' => array(
				'shilong' => array(
					'controller-path' => 'controller', 
					'controller-suffix' => 'Controller',
				)
			),
			'filters' => array(
				'filter' => array(
					'class' => 'TEST:data.ForWindClassProxyTest', 
					'pattern' => 'default_Index_run',
				)
			), 
			'components' => array(
				'windCache' => array(
				'path' => 'WIND:cache.strategy.WindFileCache',
				'scope' => 'singleton',
				'config' => array(
					'dir' => 'TEST:data.caches',
					'suffix' => 'php',
					'expires' => '0',
					),
				),
			),
			'iscache' => true,
		)
	),
	'router' => array('config' => array('resource' => 'data.route_config.php')),
);