<?php

class WindRouterFactoryTest extends BaseTestCase {
	private $routerFactory;
	private $request;
	private $response;
	
	public function testCreate() {
		$router = $this->routerFactory->create($this->request, $this->response);
		$this->assertEquals(get_class($router), 'WindUrlBasedRouter');
	}
	
	protected function setUp() {
		parent::setUp();
		require_once 'core/router/WindRouterFactory.php';
		require_once 'core/WindHttpRequest.php';
		require_once 'core/WindSystemConfig.php';
		$this->routerFactory = WindRouterFactory::getFactory();
		$this->request = new WindHttpRequest();
		$this->response = $this->request->getResponse();
		$config = require 'data/config.php';
		$config['router'] = array('parser' => 'url');
		$config['routerParsers'] = array(
			'url' => array('rule' => array('a' => 'run', 'c' => 'index', 'm' => 'default'), 
				'class' => 'WIND:core.router.WindUrlBasedRouter'));
		$this->response->setData(new WindSystemConfig($config), 'WindSystemConfig');
	}
}

