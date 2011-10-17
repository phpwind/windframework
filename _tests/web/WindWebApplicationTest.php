<?php
/**
 * WindWebApplication test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
class WindWebApplicationTest extends BaseTestCase {
	
	/**
	 * @var WindWebApplication
	 */
	private $WindWebApplication;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'web\WindWebApplication.php';
		require_once 'base\WindFactory.php';
		require_once 'data\ForWindFactoryTest.php';
		$this->WindWebApplication = Wind::application("webApplication");
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindWebApplication = null;
		Wind::resetApp();
		parent::tearDown();
	}


	/**
	 * Tests WindWebApplication->run()
	 */
	public function testRun() {
		$this->WindWebApplication->setModules("shilong", $this->getTestConfig(),true);
		
		$this->dataProviderMCA('shilong','long','aaa');
		
		try {
			$this->WindWebApplication->run();
		} catch (Exception $e) {
			$this->assertEquals("Long-aaa is running", $e->getMessage());
			$forward = new WindForward();
			$forward->forwardAction("/shilong/long/run",array(),false,false);
			$this->WindWebApplication->doDispatch($forward);
			return;
		}
		$this->fail("Run Test Error!");
		
	}
	
	public function testFilter(){
		$app = Wind::application("shilong", Wind::getRealPath("TEST:data.config", "php", true));
		$this->dataProviderMCA('shilong');
		$app->run();
	}

	/**
	 * Tests WindWebApplication->runProcess()
	 */
	public function testRunProcess() {
		
		try {
			$this->WindWebApplication->runProcess("aaa");
		} catch (WindFinalException $e) {
			return;
		}
		$this->fail("RunProcess Test Error!");
	}

	/**
	 * Tests WindWebApplication->getGlobal()
	 * @dataProvider dataForGetGlobal
	 */
	public function testGetGlobal($data, $key = '') {
		$this->WindWebApplication->setGlobal($data, $key);
		$this->assertEquals("shilong", $this->WindWebApplication->getGlobal("name"));
	}
	
	public function dataForGetGlobal(){
		$args = array();
		$args[] = array('shilong', 'name');
		$object = new stdClass();
		$object->name = 'shilong';
		$args[] = array($object);
		$args[] = array(array('name' => 'shilong'));
		return $args;
	}

	/**
	 * Tests WindWebApplication->setModules()
	 * @dataProvider dataForSetModules
	 */
	public function testSetModules($name, $config, $replace = false) {
		$this->assertNotEquals($this->WindWebApplication->getModules($name), $config);
		$this->WindWebApplication->setModules($name, $config,$replace);
		$this->assertEquals($this->WindWebApplication->getModules($name), $config);
	}
	
	public function dataForSetModules(){
		$args = array();
		$config = $this->getTestConfig();
		$args[] = array("xxx", array('name' => 'xxx')+$config, true);
		$args[] = array("shilong", array('name' => 'shilong')+$config, true);
		$args[] = array("wuq", array('name' => 'wuq')+$config, true);
		return $args;
	}

	/**
	 * Tests WindWebApplication->registeComponent()
	 * registInstance在WindFactory已测试
	 */
	public function testRegisteComponent() {
		$this->WindWebApplication->registeComponent("shilong", new ForWindFactoryTest());
		$this->assertEquals("ForWindFactoryTest", get_class($this->WindWebApplication->getComponent("shilong")));
	}

	/**
	 * Tests WindWebApplication->getComponent()
	 * getInstance在WindFactory已测试
	 */
	public function testGetComponent() {
		$this->WindWebApplication->getWindFactory()->loadClassDefinitions(array(
				'windCache' => array(
					'path' => 'WIND:cache.strategy.WindFileCache',
					'scope' => 'singleton',
					'config' => array(
						'dir' => 'TEST:data.caches',
						'suffix' => 'php',
						'expires' => '0'))), false);
		$this->WindWebApplication->setConfig(array('iscache' => true));
		
		$this->assertTrue($this->WindWebApplication->getComponent("windCache") instanceof WindFileCache);
		$this->assertTrue($this->WindWebApplication->getComponent("forward") instanceof WindForward);
	}

	/**
	 * Tests WindWebApplication->getRequest()
	 */
	public function testGetRequest() {
		$this->assertTrue($this->WindWebApplication->getRequest() instanceof WindHttpRequest);
	}

	/**
	 * Tests WindWebApplication->getResponse()
	 */
	public function testGetResponse() {
		$this->assertTrue($this->WindWebApplication->getResponse() instanceof WindHttpResponse);
	}
	
	private function getTestConfig(){
		return array('controller-path' => 'data', 
					 'controller-suffix' => 'Controller',
					 'error-handler' => 'shilong',
					);
	}
	
	private function dataProviderMCA($m = 'default', $c = 'long', $a = 'run'){
		$_GET['m'] = $m;
		$_GET['c'] = $c;
		$_GET['a'] = $a;
	}

	
}

