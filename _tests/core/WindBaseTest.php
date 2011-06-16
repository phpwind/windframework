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
		$this->assertEquals($value, Wind::getRealPath($filename, $ext));
	}

	public function testRegisterWithEmpty() {
		define('P_P', dirname(__FILE__) . D_S);
		Wind::register(P_P, '', false);
		$this->assertFalse($this->checkIncludePath(P_P));
		Wind::register(P_P, '');
		$this->assertTrue($this->checkIncludePath(P_P));
	}

	public function testRegister() {
		define('R_P', dirname(__FILE__) . D_S);
		$this->clearTestIncludePath(R_P);
		Wind::register(R_P, 'R_P', false);
		$this->assertEquals(R_P . 'data' . D_S . 'show.php', Wind::getRealPath('R_P:data.show', 'php'));
		$this->assertFalse($this->checkIncludePath(R_P));
		Wind::register(R_P, 'MyAPP', true);
		$this->assertEquals(R_P . 'data' . D_S . 'show.php', Wind::getRealPath('MyAPP:data.show', 'php'));
		$this->assertTrue($this->checkIncludePath(R_P));
	}

	public function testImport() {
		$this->assertFalse(Wind::import(''));
		$name = Wind::import('WIND:core.WindFrontController');
		$this->assertEquals('WindFrontController', $name);
		$name = Wind::import('WindBase');
		$this->assertEquals('WindBase', $name);
		$name = Wind::import('WIND:WindBase', false);
		$this->assertEquals('WindBase', $name);
	}

	public function testAutoLoadWithErrorClassException() {
		try {
			$name = Wind::import('data/config.php', false);
			$this->assertEquals('WindBase', $name);
		} catch (Exception $e) {
			return;
		}
		$this->fail('Error Exception, in testImportWithException!');
	}

	public function testGetImports() {
		$className = Wind::import('WIND:core.config.WindSystemConfig');
		$this->assertEquals($className, Wind::getImports('WIND:core.config.WindSystemConfig'));
	}

	public function testSetIsAutoLoad() {
		Wind::setIsAutoLoad(false);
		$this->isTrue('Success');
		Wind::setIsAutoLoad(true);
	}

	public function testPerLoadInjection() {
		Wind::PerLoadInjection(array(), array('PPPPP.wer' => 'pp'));
		$this->assertEquals('pp', Wind::getImports('PPPPP.wer'));
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
		return array(array(WIND_PATH . 'core' . D_S . 'WindBase.php', array('WIND:core.WindBase', 'php')), array(WIND_PATH . 'core' . D_S . 'WindBase', 'WIND:core.WindBase'), array(WIND_PATH . 'compile', 'WIND:compile'), array('data', 'data'), array('data' . D_S . 'config', 'data.config'));
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
		$config = include (T_P . '/data/config.php');
		$this->assertTrue(WindBase::run('testApp', $config['wind']) instanceof WindFrontController);
	}

	public function testIsCompile() {
		$this->assertTrue(true);
	}
}

