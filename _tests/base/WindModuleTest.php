<?php
/**
 * WindModule test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package base
 */
class WindModuleTest extends BaseTestCase {
	
	/**
	 * @var WindModule
	 */
	private $WindModule;
	private $front;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindModule.php';
		require_once 'data\TestFrontController.php';
		require_once 'data\ForWindFactoryTest.php';
		require_once 'data\LongController.php';
		$_SERVER['REQUEST_URI'] = '?test/long/default/WindModule';
		$this->front = Wind::application("WindModule", array('web-apps' => array('WindModule' => array('modules' => array('default' => array('controller-path' => 'data', 
					'controller-suffix' => 'Controller', 
					'error-handler' => 'TEST:data.ErrorControllerTest')))),'router' => array('config' => array('routes' => array('WindRoute' => array(
	            'class'   => 'WIND:router.route.WindRoute',
			    'default' => true,
		   ))))));
		$this->WindModule = new LongController();
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindModule = null;
		parent::tearDown();
	}

	/**
	 * Tests WindModule->__set()
	 */
	public function test__set() {
		$this->WindModule->config = array(1);
		$this->assertArrayEquals(array(1), $this->WindModule->config);
	}


	/**
	 * Tests WindModule->__call()
	 */
	public function test__call() {
		$this->front->run();
		$this->assertTrue(Wind::getApp('WindModule')->_getHandlerAdapter() instanceof WindRouter);
		/*$this->WindModule->setDelayAttributes($attributes);
		$this->assertTrue($this->WindModule->_getShi() instanceof ForWindFactoryTest);
		$this->WindModule->_setShi(new stdClass());
		//call_user_func_array(array($this->WindModule,"_setShi"), array(new stdClass()));
		$this->assertTrue($this->WindModule->_getShi() instanceof stdClass);*/
	}

	/**
	 * Tests WindModule->toArray()
	 */
	public function testToArray() {
		$this->WindModule->setConfig(array('destroy' => 'commit'));
		$this->WindModule->setConfig(array('aaa' => 'aaa'));
		$arr = $this->WindModule->toArray();
		$this->assertEquals($arr['_config'], array(
			'destroy' => 'commit', 
			'aaa' => 'aaa'
			)); 
	}


}

