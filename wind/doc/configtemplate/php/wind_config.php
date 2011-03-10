<?php
/**
 * @author xiaoxia xu <xiaoxa.xuxx@aliyun-inc.com> 2011-3-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
return array(
	'imports' => array(
		'components' => array(
			'resource' => 'WIND:config.components_config',
			'suffix' => 'xml',
			'init-delay' => 'false',
			'is-append' => 'true',
		),
	),
	'web-apps' => array(
		'default' => array(
			'class' => 'windWebApp',
			'root-path' => '',
			'factory' => array(
				'class-definition' => 'components',
				'class' => 'WIND:core.factory.WindComponentFactory',
			),
			'router' => array(
				'class' => 'urlBasedRouter',
			),
			'modules' => array(
				'default' => array(
					'path' => 'controllers',
					'controller-suffix' => array(
						'value' => 'Controller',
					),
					'view' => array(
						'class' => 'windView',
					),
				),
			),
		),
	),
);