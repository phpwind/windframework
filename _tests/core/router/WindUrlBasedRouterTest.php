<?php

class WindUrlBasedRouterTest extends BaseTestCase {
	private $router;
	private $request;
	private $response;
	
	/**
	 * @param unknown_type $a
	 * @param unknown_type $c
	 * @param unknown_type $m
	 * 
	 * @dataProvider providerWithDoParser
	 */
	public function testBuildUrl($a, $c, $m) {
		throw new PHPUnit_Framework_IncompleteTestError('no complete');
		
		$_SERVER['SCRIPT_FILENAME'] = "index.php";
		$_SERVER['SCRIPT_NAME'] = 'index.php';
		$_SERVER['HTTP_HOST'] = 'localhost:80';
		$this->setGet($a, $c, $m);
		$this->router->doParse($this->request, $this->response);
		$url = $this->router->buildUrl();
		$this->assertEquals($url, 'http://localhost:80/index.php');
	}
	
	/**
	 * @param unknown_type $a
	 * @param unknown_type $c
	 * @param unknown_type $m
	 * @dataProvider providerWithDoParser
	 */
	public function testDoParser($a, $c, $m) {
		$this->setGet($a, $c, $m);
		$this->router->doParse($this->request, $this->response);
		$this->assertEquals($this->router->getAction(), $a);
		$this->assertEquals($this->router->getController(), $c);
		$this->assertEquals($this->router->getModule(), $m);
	}
	
	public function testDoParserWithNoConfig() {
		$config = $this->getConfig();
		$config['module'] = '';
		$config['controller'] = '';
		$config['action'] = '';
		$this->router->setConfig(new WindConfig($config));
		$this->setGet('add', 'index', 'default');
		$this->router->doParse($this->request, $this->response);
		$this->assertEquals($this->router->getAction(), 'run');
		$this->assertEquals($this->router->getController(), 'index');
		$this->assertEquals($this->router->getModule(), 'default');
	}
	
	public function testGetHandler() {
		try{
			$this->router->getHandler($this->request, $this->response);
		}catch(Exception $e) {
			
		}
	}
	
	public function testGetHandlerWithErrorModuleConfig() {
		$config = $this->getConfig();
		$config['module']['default-value'] = 'test';
		$this->router->setConfig(new WindConfig($config)); 
		$this->router->doParse($this->request, $this->response);
		try{
			$this->router->getHandler($this->request, $this->response);
		}catch(Exception $e) {
			$this->assertEquals('WindException', get_class($e));
			return;
		}
		$this->fail('error');
	}
	
	public function providerWithDoParser() {
		$args = array();
		$args[] = array('add', 'index', 'default');
		return $args;
	}
	
	private function setGet($a, $c, $m) {
		$_GET['a'] = $a;
		$_GET['c'] = $c;
		$_GET['m'] = $m;
	}
	
	private function getConfig() {
		return array(
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
		);
	}
	protected function setUp() {
		parent::setUp();
		require_once 'core/router/WindUrlBasedRouter.php';
		require_once 'core/request/WindHttpRequest.php';
		require_once 'core/config/WindSystemConfig.php';
		$this->router = new WindUrlBasedRouter();
		$this->request = new WindHttpRequest();
		$this->router->setConfig(new WindConfig($this->getConfig()));
		$config = include(T_P . '/data/config.php');
		$this->request->setAttribute(WindFrontController::WIND_CONFIG, new WindSystemConfig($config['wind'], null, 'testApp'));
		$this->response = $this->request->getResponse();
	}
}

