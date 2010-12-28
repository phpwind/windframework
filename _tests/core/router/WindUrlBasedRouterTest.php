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
		$_SERVER['SCRIPT_FILENAME'] = "index.php";
		$_SERVER['SCRIPT_NAME'] = 'index.php';
		$_SERVER['HTTP_HOST'] = 'localhost:80';
		$this->setGet($a, $c, $m);
		$this->router->doParser($this->request, $this->response);
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
		$this->router->doParser($this->request, $this->response);
		$this->assertEquals($this->router->getAction(), $a);
		$this->assertEquals($this->router->getController(), $c);
		$this->assertEquals($this->router->getModule(), $m);
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
	
	protected function setUp() {
		parent::setUp();
		$config = array('rule' => array('a' => 'run', 'c' => 'index', 'm' => 'default'), 
			'class' => 'WIND:core.router.WindUrlBasedRouter');
		require_once 'core/router/WindUrlBasedRouter.php';
		require_once 'core/WindHttpRequest.php';
		$this->router = new WindUrlBasedRouter($config);
		$this->request = new WindHttpRequest();
		$this->response = $this->request->getResponse();
	}
}

