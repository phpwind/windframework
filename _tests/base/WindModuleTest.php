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
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindModule.php';
		require_once 'data\TestFrontController.php';
		require_once 'data\ForWindFactoryTest.php';
		require_once 'data\LongController.php';
		Wind::application()->createApplication();
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
	 * @param unknown_type $attributes
	 * @dataProvider dataFor__call
	 */
	public function test__call($attributes) {
		$this->WindModule->setDelayAttributes($attributes);
		$this->assertTrue($this->WindModule->_getShi() instanceof ForWindFactoryTest);
		$this->WindModule->_setShi(new stdClass());
		//call_user_func_array(array($this->WindModule,"_setShi"), array(new stdClass()));
		$this->assertTrue($this->WindModule->_getShi() instanceof stdClass);
	}
	
	public function dataFor__call(){
		require_once 'data\ForWindFactoryTest.php';
		$args = array();
		$args[] = array(array('shi' => array('path' => 'TEST:data.ForWindFactoryTest')));
		$args[] = array(array('shi' => array('value' => new ForWindFactoryTest())));
		return $args;
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

