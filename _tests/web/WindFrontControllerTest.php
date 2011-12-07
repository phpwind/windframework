<?php
require_once 'web\WindFrontController.php';

/**
 * WindFrontController test case.
 */
class WindFrontControllerTest extends BaseTestCase {
	
	/**
	 * @var WindFrontController
	 */
	private $WindFrontController;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		$this->WindFrontController = Wind::application("long", 
			array('web-apps' => array('long' => $this->appConfigData())));
	
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindFrontController = null;
		parent::tearDown();
	}

	/**
	 * Tests WindFrontController->registeComponent()
	 * 
	 */
	public function testRegisteComponent() {
		$httprequest = new WindHttpRequest();
		$httprequest->setAttribute(123, 'long');
		$httpresponse = new WindHttpResponse();
		$httpresponse->setData(123, 'long');
		$aaa = new stdClass();
		$aaa->long = 123;
		$this->WindFrontController->registeComponent($aaa, 'aaa');
		$this->WindFrontController->registeComponent($httpresponse, 'response');
		$this->WindFrontController->registeComponent($httprequest, 'request');
		$this->WindFrontController->createApplication();
		$this->assertEquals(123, Wind::getApp()->getRequest()->getAttribute('long'));
		$this->assertEquals(123, Wind::getApp()->getResponse()->getData('long'));
		$this->assertEquals(123, Wind::getApp()->getComponent('aaa')->long);
	}

	public function testCreateApplication() {
		$this->assertEquals($this->WindFrontController->createApplication(), Wind::getApp());
	}

	public function testGetAppConfig() {
		$config = $this->WindFrontController->getAppConfig('long');
		$this->assertArrayEquals($config, $this->appConfigData());
	}
	
	public function testGetApp() {
		$this->assertEquals($this->WindFrontController->createApplication(), $this->WindFrontController->getApp());
	}
	
	public function testGetAppName() {
		$this->WindFrontController->createApplication();
		$this->assertEquals($this->WindFrontController->getAppName(), 'default');
	}
	
	private function appConfigData() {
		return array(
			'modules' => array(
				'default' => array(
					'controller-path' => 'data', 
					'controller-suffix' => 'Controller', 
					'error-handler' => 'TEST:data.ErrorControllerTest')));
	}
}

