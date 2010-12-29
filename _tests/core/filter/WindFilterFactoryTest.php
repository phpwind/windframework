<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindFilterFactoryTest extends BaseTestCase {
	public function setUp() {
		parent::setUp();
		require_once('core/filter/WindFilterFactory.php');	
	}
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testGetFactory() {
		$p = WindFilterFactory::getFactory();
		$this->assertTrue($p instanceof WindFilterFactory);
	}
	
	public function show($name = '') {
		echo 'hello' . $name;
	}
	public function testCreate() {
		$filters = array(
			'WindFormFilter' => array(
				'class' => 'WIND:component.form.WindFormFilter',
			),
			'TestFilter' => array(
				'class' => 'core.filter.WindFilterFactoryTest',
		    ),
		);
		$filter = WindFilterFactory::getFactory()->create($filters);
		$this->assertTrue($filter instanceof WindFilter);
		$filter = WindFilterFactory::getFactory()->createFilter();
		$this->assertTrue(is_null($filter));
	}
	public function testAddFilter() {
		WindFilterFactory::getFactory()->addFilter('WindFormFilter', 'WIND:component.form.WindFormFilter');
		$filter = WindFilterFactory::getFactory()->createFilter();
		$this->assertTrue($filter instanceof WindFormFilter);
		WindFilterFactory::getFactory()->addFilter('WindFilterFactoryTest', 'core.filter.WindFilterFactoryTest', 'WindFilterFactoryTest');
		$filter = WindFilterFactory::getFactory()->createFilter();
		$this->assertTrue(is_null($filter));
		WindFilterFactory::getFactory()->addFilter('WindFormFilter', 'WIND:component.form.WindFormFilter');
		$filter = WindFilterFactory::getFactory()->createFilter();
		$this->assertTrue($filter instanceof WindFormFilter);
	}
	public function testDeleteFilter() {
		WindFilterFactory::getFactory()->deleteFilter('WindFormFilter');
		WindFilterFactory::getFactory()->deleteFilter('WindFilterFactoryTest');
		$filter = WindFilterFactory::getFactory()->createFilter();
		$this->assertTrue(is_null($filter));
		WindFilterFactory::getFactory()->addFilter('WindFormFilter', 'WIND:component.form.WindFormFilter');
		$filter = WindFilterFactory::getFactory()->createFilter();
		$this->assertTrue($filter instanceof WindFormFilter);
	}
	
	public function testGetState() {
		$this->assertFalse(WindFilterFactory::getFactory()->getState());
		WindFilterFactory::getFactory()->setExecute(array($this, 'show'));
		WindFilterFactory::getFactory()->execute();
		$this->assertTrue(WindFilterFactory::getFactory()->getState());
	}
	public function testExecute() {
		WindFilterFactory::getFactory()->setExecute(array($this, 'show'));
		ob_start();
		WindFilterFactory::getFactory()->execute();
		$result = ob_get_clean();
		$this->assertEquals('hello', $result);
		
		WindFilterFactory::getFactory()->setExecute(array($this, 'show'), 'phpwind');
		ob_start();
		WindFilterFactory::getFactory()->execute();
		$result = ob_get_clean();
		$this->assertEquals('hellophpwind', $result);
	}
}