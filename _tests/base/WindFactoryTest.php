<?php
/**
 * WindFactory test case.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package base
 */
class WindFactoryTest extends BaseTestCase {
	
	protected $factory;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindFactory.php';
		require_once 'data\ForWindFactoryTest.php';
		Wind::application()->createApplication();
		$this->factory || $this->factory = Wind::getApp()->getWindFactory();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown();
		$this->factory = null;
	}

	/**
	 * Tests WindFactory->__construct()
	 */
	public function test__construct() {
		$windFactory = $this->factory;
		$this->assertEquals("WindFactory", get_class($windFactory));
	}

	/**
	 * Tests WindFactory->getInstance()
	 */
	public function testGetInstance() {
		$this->assertTrue(Wind::getApp()->getComponent("forward") instanceof WindForward);
		try {
			Wind::getApp()->getComponent("notExistCom");
		} catch (WindException $e) {
			return;
		}
		$this->fail("GetInstance Test Error");
	}

	/**
	 * Tests WindFactory::createInstance()
	 */
	public function testCreateInstance() {
		try {
			$this->factory->createInstance("notExistCom");
		} catch (WindException $e) {
			return;
		}
		$this->fail("CreateInstance Error");
	}

	/**
	 * Tests WindFactory->registInstance()
	 */
	public function testRegistInstance() {
		$testObject = new ForWindFactoryTest();
		$this->assertFalse($this->factory->checkAlias("test"));
		$this->factory->registInstance($testObject, "test", "singleton");
		$this->assertTrue($this->factory->getInstance("test") instanceof ForWindFactoryTest);
		$this->assertTrue($this->factory->checkAlias("test"));
	}

	/**
	 * Tests WindFactory->addClassDefinitions()
	 */
	public function testAddClassDefinitions() {
		$this->factory->addClassDefinitions("aaa", $this->getTestData());
		$aaa = $this->factory->getInstance("aaa");
		$this->assertEquals("ForWindFactoryTest", get_class($aaa));
		try {
			$this->factory->executeDestroyMethod();
		} catch (Exception $e) {
			return;
		}
		$this->fail("ExecuteDestroyMethodTest Error!");
	}

	/**
	 * Tests WindFactory->loadClassDefinitions()
	 */
	public function testLoadClassDefinitions() {
		$this->factory->loadClassDefinitions(
			array("bbb" => $this->getTestData(), "windSession" => $this->getTestData()));
		$this->assertEquals("ForWindFactoryTest", get_class($this->factory->getInstance("bbb")));
		$this->assertEquals("ForWindFactoryTest", 
			get_class($this->factory->getInstance("windSession")));
	}

	public function testSetProxyForClass() {
		$this->assertTrue($this->factory->getInstance("forward") instanceof WindForward);
		$this->factory->loadClassDefinitions(
			array("forward" => array('path' => 'WIND:web.WindForward', 'proxy' => true)), false);
		$this->assertTrue($this->factory->getInstance("forward") instanceof WindClassProxy);
	}

	/**
	 * @dataProvider dataForBulidProperties
	 */
	public function testBuildProperties($config) {
		$this->factory->addClassDefinitions("xxx", $config);
		$object = $this->factory->getInstance("xxx");
		$this->assertEquals("WindConnection", get_class($object->param));
		$this->assertTrue("WindLogger" == get_class($object->session));
	}

	public function dataForBulidProperties() {
		$args = array();
		$args[] = array(
			array(
				'path' => 'TEST:data.ForWindFactoryTest', 
				'properties' => array(
					'param' => array('path' => 'WIND:db.WindConnection'), 
					'session' => array('ref' => 'windLogger'), 
					'delay' => false)));
		return $args;
	}

	public function testResolveConfig() {
		$this->factory->addClassDefinitions("shilong", 
			array(
				'config' => array('resource' => 'TEST:data.testComponentConfig.php'), 
				'path' => 'WIND:log.WindLogger'));
		$logger = $this->factory->getInstance("shilong");
		$this->assertEquals("logByShiLong", $logger->getConfig("logName"));
	}

	private function getTestData() {
		return array(
			'path' => 'TEST:data.ForWindFactoryTest', 
			'destroy' => 'clear', 
			'constructor-args' => array(
				'0' => array('value' => 2), 
				'1' => array('path' => 'WIND:web.WindForward'), 
				'2' => array('ref' => 'errorMessage')), 
			'initMethod' => 'init');
	}

}