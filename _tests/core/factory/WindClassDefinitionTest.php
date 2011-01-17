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
		require_once ('core/factory/WindClassDefinition.php');
		require_once ('core/factory/WindFactory.php');
		$this->proxy = new WindClassDefinition($this->getTestData());
	}
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testGetInstanceBySingleton() {
		$factory = new WindFactory(array('xxx' => $this->getTestData()));
		$this->assertTrue($this->proxy->getInstance($factory) instanceof WindMessage);
	}
	
	public function testGetInstanceByPrototype() {
		$args = $this->getTestData();
		$args['scope'] = 'prototype';
		$proxy = new WindClassDefinition($args);
		$factory = new WindFactory(array('xxx' => $this->getTestData()));
		$this->assertTrue($proxy->getInstance($factory) instanceof WindMessage);
	}
	
	public function testGetInstanceByOther() {
		$args = $this->getTestData();
		$args['scope'] = 'other';
		$proxy = new WindClassDefinition($args);
		$factory = new WindFactory(array('xxx' => $this->getTestData()));
		$this->assertNull($proxy->getInstance($factory));
	}
	
	public function testGetClassName() {
		$this->assertEquals('WindMessage', $this->proxy->getClassName());
	}
	
	public function testGetAlias() {
		$this->assertEquals('xxx', $this->proxy->getAlias());
	}
	
	public function testGetPath() {
		$this->assertEquals('WIND:WindMessage', $this->proxy->getPath());
	}
	
	public function testGetScope() {
		$this->assertEquals('singleton', $this->proxy->getScope());
	}
	
	public function testGetConstructArgs() {
		$this->assertTrue(is_array($this->proxy->getConstructArgs()));
	}
	
	public function testGetProperties() {
		$this->assertArrayEquals(array('name' => array('value' => 'xxx',), 'key' => array('value' => 'key')),$this->proxy->getPropertys());
	}
	
	private function getTestData() {
		return array('name' => 'xxx', 'path' => 'WIND:WindMessage', 'factory-method' => 'factory', 
			'init-method' => 'new', 'scope' => 'singleton', 
			'properties' => array('name' => array('value' => 'xxx'), 
				'key' => array('value' => 'key')), 'constructor-arg' => array(), 
			'import' => array('resource' => ''));
	}
}