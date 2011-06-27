<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-14
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindClassDefinitionTest extends BaseTestCase {
	private $proxy = null;
	public function setUp() {
		parent::setUp();
		Wind::register(T_P, 'TEST');
		require_once ('core/factory/WindClassDefinition.php');
		require_once ('core/factory/WindFactory.php');
		$this->proxy = new WindClassDefinition($this->getTestData());
	}
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testGetInstanceByFactoryMethod() {
		$factory = new WindFactory(array('xxx' => $this->getTestData()));
		$this->assertTrue($this->proxy->getInstance($factory) instanceof ForWindClassDefinition);
	}
	
	public function testGetInstanceByErrorFactoryMethod() {
		$args = $this->getTestData();
		$args['factory-method'] = 'hahah';
		$proxy = new WindClassDefinition($args);
		$factory = new WindFactory(array('xxx' => $args));
		try {
			$proxy->getInstance($factory);
		} catch (Exception $e) {
			$this->assertEquals('WindException', get_class($e));
			return;
		}
		$this->fail('error');
	}
	
	public function testGetInstanceBySingleton() {
		$args = $this->getTestData();
		$args['factory-method'] = '';
		$proxy = new WindClassDefinition($args);
		$factory = new WindFactory(array('xxx' => $args));
		$this->assertTrue($proxy->getInstance($factory) instanceof ForWindClassDefinition);
	}
	
	public function testGetInstanceByPrototype() {
		$args = $this->getTestData();
		$args['factory-method'] = '';
		$args['scope'] = 'prototype';
		$proxy = new WindClassDefinition($args);
		$factory = new WindFactory(array('xxx' => $args));
		$this->assertTrue($proxy->getInstance($factory) instanceof ForWindClassDefinition);
	}
	
	public function testGetInstanceByApplication() {
		$args = $this->getTestData();
		$args['factory-method'] = '';
		$args['scope'] = 'application';
		$proxy = new WindClassDefinition($args);
		$factory = new WindFactory(array('xxx' => $args));
		$this->assertNull($proxy->getInstance($factory));
		require_once ('core/web/WindWebApplication.php');
		$factory->application = new WindWebApplication();
		$this->assertTrue($proxy->getInstance($factory) instanceof ForWindClassDefinition);
	}
	
	public function testGetInstanceByRequest() {
		$args = $this->getTestData();
		$args['factory-method'] = '';
		$args['scope'] = 'request';
		$proxy = new WindClassDefinition($args);
		$factory = new WindFactory(array('xxx' => $args));
		$this->assertNull($proxy->getInstance($factory));
		require_once ('core/request/WindHttpRequest.php');
		$factory->request = new WindHttpRequest();
		$this->assertTrue($proxy->getInstance($factory) instanceof ForWindClassDefinition);
		$this->assertTrue($proxy->getInstance($factory) instanceof ForWindClassDefinition);
	}
	
	public function testGetInstanceByOther() {
		$args = $this->getTestData();
		$args['factory-method'] = '';
		$args['scopt'] = 'other';
		$proxy = new WindClassDefinition($args);
		$factory = new WindFactory(array('xxx' => $args));
		$this->assertTrue($proxy->getInstance($factory) instanceof ForWindClassDefinition);
	}
	
	public function testGetInstanceByErrorInitMethod() {
		$args = $this->getTestData();
		$args['factory-method'] = '';
		$args['init-method'] = 'test';
		$proxy = new WindClassDefinition($args);
		$factory = new WindFactory(array('xxx' => $args));
		try {
			$proxy->getInstance($factory);
		} catch (Exception $e) {
			$this->assertEquals('WindException', get_class($e));
			return;
		}
		$this->fail('error');
	}
	
	public function testGetClassName() {
		$this->assertEquals('ForWindClassDefinition', $this->proxy->getClassName());
	}
	
	public function testGetAlias() {
		$this->assertEquals('xxx', $this->proxy->getAlias());
	}
	
	public function testGetPath() {
		$this->assertEquals('TEST:data.ForWindClassDefinition', $this->proxy->getPath());
	}
	
	public function testGetScope() {
		$this->assertEquals('singleton', $this->proxy->getScope());
	}
	
	public function testGetConstructArgs() {
		$this->assertTrue(is_array($this->proxy->getConstructArgs()));
	}
	
	public function testGetProperties() {
		$this->assertArrayEquals(array('name' => array('value' => 'xxx'), 'key' => array('value' => 'key')), $this->proxy->getPropertys());
	}
	
	public function testGetClassDefinition() {
		$this->assertArrayEquals($this->getTestData(), $this->proxy->getClassDefinition());
	}
	
	private function getTestData() {
		return array('name' => 'xxx', 'path' => 'TEST:data.ForWindClassDefinition', 'factory-method' => 'factory', 
			'init-method' => 'init', 'scope' => 'singleton', 
			'properties' => array('name' => array('value' => 'xxx'), 'key' => array('value' => 'key')), 
			'constructor-arg' => array(), 'import' => array('resource' => ''));
	}
}