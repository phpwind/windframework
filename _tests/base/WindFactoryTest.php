<?php
/**
 * WindFactory test case.
 */
class WindFactoryTest extends BaseTestCase {
	
	/**
	 * @var WindFactory
	 */
	private $WindFactory;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindFactory.php';
		require_once 'data\ForWindFactoryTest.php';
		Wind::application("FactoryTest");
		$this->WindFactory = Wind::getApp()->getWindFactory();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindFactory = null;
		Wind::resetApp();
		parent::tearDown();
		
	}

	/**
	 * Tests WindFactory->__construct()
	 */
	public function test__construct() {
		$this->assertEquals("WindFactory", get_class($this->WindFactory));
	}

	/**
	 * Tests WindFactory->getInstance()
	 */
	public function testGetInstance() {
		$this->assertTrue($this->WindFactory->getInstance("forward") instanceof WindForward);
		try {
			$this->WindFactory->getInstance("notExistCom");
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
			$this->WindFactory->createInstance("notExistCom");
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
		$this->assertFalse($this->WindFactory->checkAlias("test"));
		$this->WindFactory->registInstance($testObject, "test","singleton");
		$this->assertTrue($this->WindFactory->getInstance("test") instanceof ForWindFactoryTest);
		$this->assertTrue($this->WindFactory->checkAlias("test"));
	}

	/**
	 * Tests WindFactory->addClassDefinitions()
	 */
	public function testAddClassDefinitions() {
		$this->WindFactory->addClassDefinitions("aaa", $this->getTestData());
		$aaa = $this->WindFactory->getInstance("aaa");
		$this->assertEquals("ForWindFactoryTest", get_class($aaa));
		try {
			$this->WindFactory->executeDestroyMethod();
		} catch (Exception $e) {
			return;
		}
		$this->fail("ExecuteDestroyMethodTest Error!");
	}

	/**
	 * Tests WindFactory->loadClassDefinitions()
	 */
	public function testLoadClassDefinitions() {
		$this->WindFactory->loadClassDefinitions(array("bbb" => $this->getTestData(), "windSession" => $this->getTestData()));
		$this->assertEquals("ForWindFactoryTest", get_class($this->WindFactory->getInstance("bbb")));
		$this->assertEquals("ForWindFactoryTest", get_class($this->WindFactory->getInstance("windSession")));
	}

	public function testSetProxyForClass(){
		$this->assertTrue($this->WindFactory->getInstance("forward") instanceof WindForward);
		$this->WindFactory->loadClassDefinitions(array("forward" => array('path' => 'WIND:web.WindForward','proxy' => true)),false);
		$this->assertTrue($this->WindFactory->getInstance("forward") instanceof WindClassProxy);
	}
	
	/**
	 * @dataProvider dataForBulidProperties
	 */
	public function testBuildProperties($config){
		$this->WindFactory->addClassDefinitions("xxx", $config);
		$object = $this->WindFactory->getInstance("xxx");
		$this->assertEquals("WindConnection", get_class($object->param));
		$this->assertTrue("WindLogger" == get_class($object->session));
	}
	
	public function dataForBulidProperties(){
		$args = array();
		$args[] = array(array(
			'path' => 'TEST:data.ForWindFactoryTest',
			'properties' => array('param' => array('path' => 'WIND:db.WindConnection'),
								  'session' => array('ref' => 'windLogger'),
								  'delay' => false),
		));
		return $args;
	}
	
	public function testResolveConfig(){
		$this->WindFactory->addClassDefinitions("shilong", array('config' => array('resource' => 'TEST:data.testComponentConfig.php'), 'path' => 'WIND:log.WindLogger'));
		$logger = $this->WindFactory->getInstance("shilong");
		$this->assertEquals("logByShiLong", $logger->getConfig("logName"));
	}
	
	
	private function getTestData(){
		return array(
			'path' => 'TEST:data.ForWindFactoryTest', 
			'destroy' => 'clear',
			'constructor-args' => array(
				'0' => array('value' => 2), 
				'1' => array('path' => 'WIND:web.WindForward'),
				'2' => array('ref' => 'errorMessage')),
			'initMethod' => 'init',
		);
	}

}


