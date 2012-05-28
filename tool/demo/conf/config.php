<?php
return array(
	'web-apps' => array(
		'demo' => array(
			'root-path' => dirname(dirname(__FILE__)),
			'modules' => array(
				'default' => array(
					'controller-path' => 'DEMO:src.controller', 
					'controller-suffix' => 'Controller', 
					'template-dir' => 'template',
					'compile-dir' => 'data.compile',
				)
			)
		)
	)
);