<?php
return array(
	'web-apps' => array(
		'blog' => array(
			'modules' => array(
				'default' => array(
					'controller-path' => 'controller', 
					'controller-suffix' => 'Controller', 
					'error-handler' => 'BLOG:controller.ErrorController'
				)
			), 
			'filters' => array(
				'user' => array(
					'class' => 'WIND:web.filter.WindFormFilter', 
					'pattern' => 'default/Index/(login|dreg)', 
					'form' => 'BLOG:model.UserForm'
				)
			),
			'components' => array(
				'db' => array(
					'config' => array(
						'resource' => 'config.db_config.php'
					)
				)
			)
		)
	),
	'router' => array('config' => array('resource' => 'BLOG:config.route_config.php')),
);