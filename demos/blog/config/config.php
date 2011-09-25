<?php
return array(
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
				'pattern' => 'default_Index_login|default_Index_dreg', 
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
);