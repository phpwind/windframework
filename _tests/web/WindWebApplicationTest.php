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
		require_once 'web\WindForward.php';
		require_once 'base\WindFactory.php';
		require_once 'data\ForWindFactoryTest.php';
		$this->front = Wind::application("long", 
			array(
				'web-apps' => array(
					'long' => array(
						'modules' => array(
							'default' => array(
								'controller-path' => 'data', 
								'controller-suffix' => 'Controller', 
								'error-handler' => 'TEST:data.ErrorControllerTest'))))));
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
		$_SERVER['SCRIPT_FILENAME'] = "index.php";
		$_SERVER['SCRIPT_NAME'] = 'index.php';
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_FILENAME'] . '?c=long';
		ob_start();
		$this->front->run();
		$this->assertEquals(ob_get_clean(), 'LongController-run');
	}
	
	public function testDoDispatch() {
		$forward = new WindForward();
		$forward->setIsReAction(true);
		$forward->setAction('/long/test');
		ob_start();
		$this->front->createApplication()->doDispatch($forward);
		$this->assertEquals(ob_get_clean(), 'LongController-test');
	}
	
	public function testSetConfig() {
		$this->front->createApplication()->setConfig(array('components' => array(
				'long' => array('path' => 'TEST:data.ForWindFactoryTest'))));
		$this->assertEquals('ForWindFactoryTest', get_class(Wind::getApp()->getComponent('long')));
		$this->assertEquals(Wind::getApp()->getResponse()->getCharset(), 'utf-8');
	}

	/**
	 * Tests WindWebApplication->getGlobal()
	 * @dataProvider dataForGetGlobal
	 */
	public function testGetGlobal($data, $key = '') {
		$this->front->createApplication()->setGlobal($data, $key);
		$this->assertEquals("shilong", Wind::getApp()->getGlobal("name"));
	}

	public function dataForGetGlobal() {
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
		$this->assertNotEquals($this->front->createApplication()->getModules($name), $config);
		Wind::getApp()->setModules($name, $config, $replace);
		$this->assertEquals(Wind::getApp()->getModules($name), $config);
	}

	public function dataForSetModules() {
		$args = array();
		$config = $this->getTestConfig();
		$args[] = array("xxx", array('name' => 'xxx') + $config, true);
		$args[] = array("shilong", array('name' => 'shilong') + $config, true);
		$args[] = array("wuq", array('name' => 'wuq') + $config, true);
		return $args;
	}

	/**
	 * Tests WindWebApplication->getComponent()
	 * getInstance在WindFactory已测试
	 */
	public function testGetComponent() {
		$this->assertTrue($this->front->createApplication()->getComponent("forward") instanceof WindForward);
	}

	/**
	 * Tests WindWebApplication->getRequest()
	 */
	public function testGetRequest() {
		$this->assertTrue($this->front->createApplication()->getRequest() instanceof WindHttpRequest);
	}

	/**
	 * Tests WindWebApplication->getResponse()
	 */
	public function testGetResponse() {
		$this->assertTrue($this->front->createApplication()->getResponse() instanceof WindHttpResponse);
	}

	private function getTestConfig() {
		return array(
			'controller-path' => 'data', 
			'controller-suffix' => 'Controller', 
			'error-handler' => 'shilong');
	}

}

