<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindComponentFactoryTest extends BaseTestCase {
	private $factory = null;
	public function setUp() {
		parent::setUp();
		require_once ('core/factory/WindComponentFactory.php');
		$this->factory = new WindComponentFactory($this->getTestData());
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testGetInstance() {
		$this->assertTrue($this->factory->getInstance('ppp', 'default') instanceof WindView);
	}
	
	public function testNoClassCreate() {
		try {
			$this->factory->createInstance('CoreTest', 'hahah');
		} catch (Exception $e) {
			return;
		}
		$this->fail('Exception Error!');
	}
	
	public function testCreateInstance() {
		$this->assertTrue($this->factory->createInstance('WindWebApplication') instanceof WindWebApplication);
	}
	
	public function testGetClassDefinitionByAlias() {
		$this->assertTrue($this->factory->getClassDefinitionByAlias('ppp') instanceof WindComponentDefinition);
	}
	
	public function testAddClassDefinitions() {
		$p = new WindComponentDefinition(array('name' => 'kkk', 'path' => 'WIND:core.WindView'));
		$this->factory->addClassDefinitions($p);
		$this->assertTrue($this->factory->getClassDefinitionByAlias('kkk') instanceof WindComponentDefinition);
		
		$p = new WindComponentDefinition(array('name' => 'ooo', 'path' => 'WIND:core.WindView'));
		$this->factory->addClassDefinitions(array($p));
		$this->assertTrue($this->factory->getClassDefinitionByAlias('ooo') instanceof WindComponentDefinition);
	}
	
	private function getTestData() {
		return array(
		   'ppp' => array(
	            'name' => 'pppp',
				'path' => 'WIND:core.WindView',
				'resource' = ''
		   ),
        )
		;
	}
}