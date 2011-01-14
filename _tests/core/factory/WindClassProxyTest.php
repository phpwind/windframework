<?php
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindClassProxyTest extends BaseTestCase {

	public function testCreatInstance() {
		list($instance) = $this->createInstance('Persion');
		$this->assertEquals(get_class($instance->getInstance()), 'Persion');
		$this->assertEquals($instance->getReflection()->getName(), 'Persion');
	}

	public function testProxy() {
		list($instance, $listener) = $this->createInstance('Persion');
		$result = $instance->testPersion('a1', 'b1');
		$this->assertEquals($instance->arg1, 'a1');
		$this->assertEquals($instance->arg2, 'b1');
		$this->assertEquals($result, 'a1');
		
		$listener1 = new Listener1();
		$instance->registerEventListener('testPersion', $listener, WindClassProxy::EVENT_TYPE_METHOD);
		$instance->registerEventListener('testPersion', $listener1, WindClassProxy::EVENT_TYPE_METHOD);
		$instance->name = 'wuqiong';
		$this->assertEquals($listener1->arg1, null);
		$this->assertEquals($listener1->arg2, null);
		
		$result = $instance->testPersion('a', 'b');
		$this->assertEquals($result, 'a');
		$this->assertEquals($listener1->arg1, 'a_pre_a_post');
		$this->assertEquals($listener1->arg2, 'b_pre_b_post');
	}

	public function testGetter() {
		list($instance, $listener) = $this->createInstance('Persion');
		$instance->name = 'wuqiong01';
		$this->assertEquals('wuqiong01', $instance->getInstance()->name);
		
		$instance->registerEventListener('name', $listener, WindClassProxy::EVENT_TYPE_GETTER);
		$instance->name = 'wuqiong';
		$_value = $instance->name;
		$this->assertEquals($_value, 'wuqiong');
		$this->assertEquals($listener->testB, 'wuqiong_postHandle');
	}

	public function testSetter() {
		list($instance, $listener) = $this->createInstance('Persion');
		$instance->name = 'wuqiong01';
		$this->assertEquals('wuqiong01', $instance->getInstance()->name);
		
		$instance->registerEventListener('name', $listener, WindClassProxy::EVENT_TYPE_SETTER);
		$instance->name = 'wuqiong';
		$this->assertEquals($listener->test, 'wuqiong_preHandle');
		$this->assertEquals($listener->testB, 'wuqiong_postHandle');
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $className
	 * @param array $args
	 * @return WindClassProxy
	 */
	private function createInstance($className, $args = array()) {
		$instance = new WindClassProxy($className, $args);
		$listener = new Listener();
		return array($instance, $listener);
	}

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'core/factory/WindClassProxy.php';
		require_once 'data/ForWindClassProxy.php';
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		
		parent::tearDown();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct() {

	}

}

