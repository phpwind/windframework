<?php
return array(
	'blog' => array(
		'components' => array(
			'db' => array(
				'path' => 'WIND:db.WindConnection',
				'scope' => 'singleton', 
				'config' => array(
					'resource' => 'config.db_config.php',
				),
			),
			'windSession' => array(
				'path' => 'WIND:http.session.WindSession',
				'scope' => 'singleton',
				
			),
		),
		'modules' => array(
			'default' => array(
				'controller-path' => 'controller',
				'controller-suffix' => 'Controller',
				'error-handler' => 'WIND:web.WindErrorHandler',
				'template-dir'=> 'template',
				'compile-dir' => 'compile.blog.template',
			),
			'error' => array(
				'controller-path' => 'WIND:web',
				'controller-suffix' => '',
				'compile-dir' => 'compile.blog.template',
			),
		),
		'filters' => array(
			'class' => 'WIND:core.filter.WindFilterChain',
			'filter' => array(
				'name' => 'login',
				'class' => 'WIND:web.filter.WindFormFilter',
				'pattern' => 'default_Index_post',	
				'form' => 'BLOG:model.LoginForm',
			),
		),
		'iscache' => false,
	),
);