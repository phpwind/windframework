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
	
	private $front;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindFactory.php';
		require_once 'data\ForWindFactoryTest.php';
		$_SERVER['REQUEST_URI'] = '?test/long/default/WindFactory';
		$this->front = Wind::application("WindFactory", array('web-apps' => array('WindFactory' => array('modules' => array('default' => array('controller-path' => 'data', 
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
		parent::tearDown();
		
	}

	/**
	 * Tests WindFactory->__construct()
	 */
	public function test__construct() {
		$this->front->run();
		$windFactory = Wind::getApp('WindFactory')->getWindFactory();
		$this->assertEquals("WindFactory", get_class($windFactory));
	}

	/**
	 * Tests WindFactory->getInstance()
	 */
	public function testGetInstance() {
		$this->assertTrue(Wind::getApp('WindFactory')->getComponent("forward") instanceof WindForward);
		try {
			Wind::getApp('WindFactory')->getComponent("notExistCom");
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
			Wind::getApp('WindFactory')->getWindFactory()->createInstance("notExistCom");
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
		$this->assertFalse(Wind::getApp('WindFactory')->getWindFactory()->checkAlias("test"));
		Wind::getApp('WindFactory')->getWindFactory()->registInstance($testObject, "test","singleton");
		$this->assertTrue(Wind::getApp('WindFactory')->getWindFactory()->getInstance("test") instanceof ForWindFactoryTest);
		$this->assertTrue(Wind::getApp('WindFactory')->getWindFactory()->checkAlias("test"));
	}

	/**
	 * Tests WindFactory->addClassDefinitions()
	 */
	public function testAddClassDefinitions() {
		Wind::getApp('WindFactory')->getWindFactory()->addClassDefinitions("aaa", $this->getTestData());
		$aaa = Wind::getApp('WindFactory')->getWindFactory()->getInstance("aaa");
		$this->assertEquals("ForWindFactoryTest", get_class($aaa));
		try {
			Wind::getApp('WindFactory')->getWindFactory()->executeDestroyMethod();
		} catch (Exception $e) {
			return;
		}
		$this->fail("ExecuteDestroyMethodTest Error!");
	}

	/**
	 * Tests WindFactory->loadClassDefinitions()
	 */
	public function testLoadClassDefinitions() {
		Wind::getApp('WindFactory')->getWindFactory()->loadClassDefinitions(array("bbb" => $this->getTestData(), "windSession" => $this->getTestData()));
		$this->assertEquals("ForWindFactoryTest", get_class(Wind::getApp('WindFactory')->getWindFactory()->getInstance("bbb")));
		$this->assertEquals("ForWindFactoryTest", get_class(Wind::getApp('WindFactory')->getWindFactory()->getInstance("windSession")));
	}

	public function testSetProxyForClass(){
		$this->assertTrue(Wind::getApp('WindFactory')->getWindFactory()->getInstance("forward") instanceof WindForward);
		Wind::getApp('WindFactory')->getWindFactory()->loadClassDefinitions(array("forward" => array('path' => 'WIND:web.WindForward','proxy' => true)),false);
		$this->assertTrue(Wind::getApp('WindFactory')->getWindFactory()->getInstance("forward") instanceof WindClassProxy);
	}
	
	/**
	 * @dataProvider dataForBulidProperties
	 */
	public function testBuildProperties($config){
		Wind::getApp('WindFactory')->getWindFactory()->addClassDefinitions("xxx", $config);
		$object = Wind::getApp('WindFactory')->getWindFactory()->getInstance("xxx");
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
		Wind::getApp('WindFactory')->getWindFactory()->addClassDefinitions("shilong", array('config' => array('resource' => 'TEST:data.testComponentConfig.php'), 'path' => 'WIND:log.WindLogger'));
		$logger = Wind::getApp('WindFactory')->getWindFactory()->getInstance("shilong");
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


