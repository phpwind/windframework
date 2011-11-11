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
		$this->WindFrontController = Wind::application("long", array('web-apps' => array('long' => array('modules' => array('default' => array('controller-path' => 'data', 
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
		$this->WindFrontController = null;
		parent::tearDown();
	}

	/**
	 * Tests WindFrontController->registeComponent()
	 * 
	 */
	public function testRegisteComponent() {
		$_SERVER['REQUEST_URI'] = '?test/long/default/long';
		$httprequest = new WindHttpRequest();
		$httprequest->setAttribute(123, 'long');
		$httpresponse = new WindHttpResponse();
		$httpresponse->setData(123, 'long');
		$aaa = new stdClass();
		$aaa->long = 123;
		$this->WindFrontController->registeComponent($aaa, 'aaa');
		$this->WindFrontController->registeComponent($httpresponse, 'response');
		$this->WindFrontController->registeComponent($httprequest, 'request');
		$this->WindFrontController->run();
		$this->assertEquals(123, $this->WindFrontController->getApp('long')->getRequest()->getAttribute('long'));
		$this->assertEquals(123, $this->WindFrontController->getApp('long')->getResponse()->getData('long'));
		$this->assertEquals(123, $this->WindFrontController->getApp('long')->getComponent('aaa')->long);
	}
}

