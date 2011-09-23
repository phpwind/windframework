<?php
return array(
	'blog' => array(
		'components' => array(
			'db' => array(
				'config' => array(
					'resource' => 'config.db_config.php',
				),
			),
			'windView' => array(
	 			'config' => array(
					'is-compile' => '1',
					),
			),
		),
		'modules' => array(
			'default' => array(
				'controller-path' => 'controller',
				'controller-suffix' => 'Controller',
				'error-handler' => 'BLOG:controller.Error',
			),
			'error' => array(
				'controller-path' => 'controller',
				'controller-suffix' => 'Controller',
			),
		),
		'filters' => array(
			'user' => array(
				'class' => 'WIND:web.filter.WindFormFilter',
				'pattern' => 'default_Index_login|default_Index_register',	
				'form' => 'BLOG:model.UserForm',
			),
		),
		'iscache' => false,
	),
);