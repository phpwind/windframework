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
	
	private $front;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'web\WindWebApplication.php';
		require_once 'base\WindFactory.php';
		require_once 'data\ForWindFactoryTest.php';
		$_SERVER['REQUEST_URI'] = '?test/long/default/long';
		$this->front = Wind::application("long", array('web-apps' => array('long' => array('modules' => array('default' => array('controller-path' => 'data', 
					'controller-suffix' => 'Controller', 
					'error-handler' => 'TEST:data.ErrorControllerTest')))),'router' => array('config' => array('routes' => array('WindRoute' => array(
	            'class'   => 'WIND:router.route.WindRoute',
			    'default' => true,
		   ))))));
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();
	}


	/**
	 * Tests WindWebApplication->run()
	 */
	public function testRun() {
		$this->front->run();
		
	}
	
	public function testFilter(){
		$this->markTestIncomplete();
	}

	/**
	 * Tests WindWebApplication->runProcess()
	 */
	public function testRunProcess() {
		
		$this->markTestIncomplete();
	}

	/**
	 * Tests WindWebApplication->getGlobal()
	 * @dataProvider dataForGetGlobal
	 */
	public function testGetGlobal($data, $key = '') {
		Wind::getApp('long')->setGlobal($data, $key);
		$this->assertEquals("shilong", Wind::getApp('long')->getGlobal("name"));
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
		$this->assertNotEquals(Wind::getApp('long')->getModules($name), $config);
		Wind::getApp('long')->setModules($name, $config,$replace);
		$this->assertEquals(Wind::getApp('long')->getModules($name), $config);
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
	 * Tests WindWebApplication->getComponent()
	 * getInstance在WindFactory已测试
	 */
	public function testGetComponent() {
		$this->assertTrue(Wind::getApp('long')->getComponent("forward") instanceof WindForward);
	}

	/**
	 * Tests WindWebApplication->getRequest()
	 */
	public function testGetRequest() {
		$this->assertTrue(Wind::getApp('long')->getRequest() instanceof WindHttpRequest);
	}

	/**
	 * Tests WindWebApplication->getResponse()
	 */
	public function testGetResponse() {
		$this->assertTrue(Wind::getApp('long')->getResponse() instanceof WindHttpResponse);
	}
	
	private function getTestConfig(){
		return array('controller-path' => 'data', 
					 'controller-suffix' => 'Controller',
					 'error-handler' => 'shilong',
					);
	}
	
}

