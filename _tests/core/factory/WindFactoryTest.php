<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-14
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindFactoryTest extends BaseTestCase {
	private $factory = null;
	public function setUp() {
		parent::setUp();
		require_once('core/factory/WindFactory.php');
		$this->factory = new WindFactory($this->getTestData());
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	public function testNoClassCreate() {
		try{
			$this->factory->createInstance('WindCoreTest', 'hahah');
		}catch(Exception $e) {
			//$this->assertTrue($e instanceof WindException);
			return;
		}
		$this->fail('Exception Error!');
	}
	
	public function testCreateClassProxyInstance() {
		
	}
	
	private function getTestData() {
		return array(
		   'ppp' => array(
	            'name' => 'pppp',
				'path' => 'WIND:core.WindView',
		   ),
        );
	}
}