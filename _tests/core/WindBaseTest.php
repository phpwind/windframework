<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-28
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once ('WindBase.php');

class LTest extends BaseTestCase {
	public function setUp() {
		parent::setUP();
	}
	public function tearDown() {
		parent::tearDown();
	}
	/**
	 * @dataProvider providerRealPath
	 */
	public function testGetRealPath($value, $args) {
		if (is_array($args)) {
			list($filename, $ext) = $args;
		} else {
			$filename = $args;
			$ext = '';
		}		
		$this->assertEquals($value, L::getRealPath($filename, $ext));
	}
	
	public function testRegisterWithEmpty() {
		define('P_P', dirname(__FILE__) . D_S);
		L::register(P_P, '', false);
		$this->assertFalse($this->checkIncludePath(P_P));
		L::register(P_P, '');
		$this->assertTrue($this->checkIncludePath(P_P));
	}
	
	public function testRegister() {
		define('R_P', dirname(__FILE__) . D_S);
		$this->clearTestIncludePath(R_P);
		L::register(R_P, 'R_P', false);
		$this->assertEquals(R_P . 'data' . D_S . 'show.php', L::getRealPath('R_P:data.show', 'php'));
		$this->assertFalse($this->checkIncludePath(R_P));

		L::register(R_P, 'MyAPP', true);
		$this->assertEquals(R_P . 'data' . D_S . 'show.php', L::getRealPath('MyAPP:data.show', 'php'));
		$this->assertTrue($this->checkIncludePath(R_P));
	}
	
	public function testImport() {
		$this->assertFalse(L::import(''));
		$name = L::import('WIND:core.WindFrontController');
		$this->assertEquals('WindFrontController', $name);
		$name = L::import('WindBase');
		$this->assertEquals('WindBase', $name);
		$name = L::import('WIND:WindBase', false);
		$this->assertEquals('WindBase', $name);
	}
	public function testAutoLoadWithErrorClassException() {
		try {
			$name = L::import('data/config.php', false);
			$this->assertEquals('WindBase', $name);
		} catch (Exception $e) {
			return;
		}
		$this->fail('Error Exception, in testImportWithException!');
	}
	
	public function testGetImports() {
		$className = L::import('WIND:core.config.WindSystemConfig');
		$this->assertEquals($className, L::getImports('WIND:core.config.WindSystemConfig'));
	}
	
	public function testSetIsAutoLoad() {
		L::setIsAutoLoad(false);
		$this->isTrue('Success');
		L::setIsAutoLoad(true);
	}
	
	public function testPerLoadInjection() {
		L::PerLoadInjection(array(), array('PPPPP.wer' => 'pp'));
		$this->assertEquals('pp', L::getImports('PPPPP.wer'));
	}
	
	private function clearTestIncludePath($path) {
		$includePaths = array_unique(explode(PATH_SEPARATOR, get_include_path()));
		if (($pos = array_search($path, $includePaths)) !== false) unset($includePaths[$pos]);
		set_include_path('.' . PATH_SEPARATOR . implode(PATH_SEPARATOR, $includePaths));
	}
	
	private function checkIncludePath($path) {
		$includePaths = array_unique(explode(PATH_SEPARATOR, get_include_path()));
		return (array_search($path, $includePaths) !== false);
	}
	public static function providerRealPath() {
		return array(
			array(WIND_PATH . 'core' . D_S . 'WindBase.php', array('WIND:core.WindBase', 'php')), 
			array(WIND_PATH . 'core' . D_S . 'WindBase', 'WIND:core.WindBase'), 
			array(WIND_PATH . 'compile', 'WIND:compile'),
			array('data', 'data'),
			array('data' . D_S . 'config', 'data.config'),
		);
	}
}

class WTest extends BaseTestCase {
	public function setUp() {
		parent::setUP();
	}
	public function tearDown() {
		parent::tearDown();
	}
	public function testApplication() {
		$config = include(T_P . '/data/config.php');
		$this->assertTrue(W::application('testApp', $config['wind']) instanceof WindFrontController);
	}

	public function testIsCompile() {
		$this->assertTrue(W::ifCompile());
	}
}

