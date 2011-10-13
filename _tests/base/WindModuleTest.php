<?php

/**
 * WindModule test case.
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
		require_once 'data\ForWindFactoryTest.php';
		require_once 'data\LongController.php';
		Wind::application("test","data/config.php");
		$this->WindModule = new LongController();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindModule = null;
		Wind::resetApp();
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
		$this->WindModule->setConfig(Wind::getRealPath("TEST:data.db_config","php",true));
		$arr = $this->WindModule->toArray();
		$this->assertEquals($arr['_config'], array(
			'destroy' => 'commit', 
			'aaa' => 'aaa',
			'charset' => 'utf8',
			'dsn' => 'mysql:host=localhost;dbname=p9',
			'user' => 'root',
			'pwd' => 'phpwind.net')); 
	}


}

