<?php
return array(
	'web-apps' => array(
		'command' => array(
			'root-path' => dirname(__DIR__),
			'modules' => array(
				'default' => array(
					'controller-path' => 'COMMAND:src.controller',
					'controller-suffix' => 'Command'
				)
			),
		),
		'web' => array(
			'root-path' => dirname(__DIR__),
			'modules' => array(
				'default' => array(
					'controller-path' => 'WEB:src.controller',
				)
			),
		)
	)
);