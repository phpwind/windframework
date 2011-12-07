<?php
/**
 * WindClassProxy test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package base
 */
class WindClassProxyTest extends BaseTestCase {
	
	/**
	 * @var WindClassProxy
	 */
	private $WindClassProxy;
	/**
	 * @var ForWindFactoryTest
	 */
	private $test;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindClassProxy.php';
		require_once 'data\ForWindFactoryTest.php';
		require_once 'data\ForWindClassProxyTest.php';
		$this->test = new ForWindFactoryTest();
		$this->WindClassProxy = new WindClassProxy($this->test);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindClassProxy = null;
		parent::tearDown();
	}

	/**
	 * Tests WindClassProxy->__call()
	 */
	public function test__call() {
		$listener1 = new listener1();
		$this->WindClassProxy->registerEventListener("init", $listener1, WindClassProxy::EVENT_TYPE_METHOD);
		$this->WindClassProxy->init('aaa');
		$this->assertEquals("aaalistener1_preaaa_post", $listener1->a);
		
		$listener2 = new listener2();
		$this->WindClassProxy->registerEventListener("init", $listener2, WindClassProxy::EVENT_TYPE_METHOD);
		$this->WindClassProxy->init('shi', 'long');
		$this->assertEquals('shilistener2_preshi_post', $listener2->a);
		$this->assertEquals('longlistener2_prelong_post', $listener2->b);
		
		try {
			$this->WindClassProxy->registerEventListener("init", $listener2, "init");
		} catch (WindException $e) {
			return;
		}
		$this->fail("RegisterEventListenerTest Error!");
	}

	/**
	 * Tests WindClassProxy->_setClassPath()
	 */
	public function test_setClassPath() {
		$this->WindClassProxy->_setClassPath("TEST:data.ForWindClassProxyTest");
		$this->assertEquals("TEST:data.ForWindClassProxyTest", $this->WindClassProxy->_getClassPath());
		$this->assertEquals("ForWindClassProxyTest", $this->WindClassProxy->_getClassName());
	}
}
