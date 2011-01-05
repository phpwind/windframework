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
		list($filename, $ext) = (array) $args;
		$this->assertEquals($value, L::getRealPath($filename, $ext));
	}
	
	public function testRegisterWithEmpty() {
		define('R_P', dirname(__FILE__) . D_S);
		L::register(R_P, '', false);
		$this->assertFalse($this->checkIncludePath(R_P));
		L::register(R_P, '');
		$this->assertTrue($this->checkIncludePath(R_P));
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
	public function testGetInstance() {
		L::import("WIND:component.parser.WindIniParser", false);
		$obj = L::getInstance('WindIniParser');
		$this->assertTrue(is_object($obj) && $obj instanceof WindIniParser);
	}
	
	public function testGetImports() {
		$this->assertEquals('WindIniParser', L::getImports('WIND:component.parser.WindIniParser'));
		$this->assertEquals('WindBase', L::getImports('WindBase'));
		$this->assertEquals('WindSystemConfig', L::getImports('WIND:core.WindSystemConfig'));
	}
	
	public function testSetIsAutoLoad() {
		L::setIsAutoLoad(false);
		$this->isTrue('Success');
		L::setIsAutoLoad(true);
	}
	
	public function testPerLoadInjection() {
		L::PerLoadInjection(array('PPPPP.wer' => 'pp'));
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
		$this->assertTrue(W::application('test') instanceof WindFrontController);
		$config = array(
		   'applications' => array( 'web' => array('class' => 'WIND:core.WindWebApplication'),
									'command' => array('class' => 'WIND:core.WindCommandApplication')));
		$this->assertTrue(W::application('test2', $config) instanceof WindFrontController);
	}
}

class CTest extends BaseTestCase {
	public function setUp() {
		parent::setUp();
		C::init(include "data/config.php");
	}
	public function tearDown() {
		parent::tearDown();
	}
	public function testIniWithEmpty() {
		try {
			C::init(array());
		} catch(Exception $e) {
			return;
		}
		$this->fail('Error Exception!');
	}
	private function checkArray($array, $num, $member = array(), $ifCheck = false) {
		$this->assertTrue(is_array($array) && count($array) == $num);
		if (empty($member)) return;
		foreach ($member as $key => $value) {
			($ifCheck) ? $this->assertTrue(isset($array[$key]) && $array[$key] == $value) :
						$this->assertTrue(isset($array[$value]));
		}
	}
	public function getConfig() {
		$this->checkArray(C::getConfig(), 10);
		$this->checkArray(C::getConfig('xxx'), 0);
		$this->checkArray(C::getConfig('applications'), 2);
		$this->checkArray(C::getConfig('applications', 'web'), 1, array('class' => 'WIND:core.WindWebApplication'), true);
		$this->checkArray(C::getConfig('applications', 'wa'), 2);
	}
	
	public function testGetModules() {
		$this->checkArray(C::getModules(), 2);
		$this->checkArray(C::getModules('modules'), 0);
		$config = array(
			'path' => 'actionControllers',
			'template' => 'default',
			'controllerSuffix' => 'controller',
			'actionSuffix' => 'action',
			'method' => 'run',
		);
		$this->checkArray(C::getModules('default'), 5, $config, true);
		$config = array(
			'path' => 'otherControllers',
			'template' => 'wind',
			'controllerSuffix' => 'controller',
			'actionSuffix' => 'action',
			'method' => 'run',
		);
		$this->checkArray(C::getModules('other'), 5, $config, true);
	}
	
	public function testGetTemplate() {
		$this->checkArray(C::getTemplate(), 2);
		$this->checkArray(C::getTemplate('modules'), 0);
		$config = array(
			'dir' => 'template',
			'default' => 'index',
			'ext' => 'htm',
			'resolver' => 'default',
			'isCache' => '0',
			'cacheDir' => 'cache',
			'compileDir' => 'compile',
		);
		$this->checkArray(C::getTemplate('default'), 7, $config, true);
		$config = array(
			'dir' => 'template',
			'default' => 'index',
			'ext' => 'htm',
			'resolver' => 'default',
			'isCache' => '0',
			'cacheDir' => 'cache',
			'compileDir' => 'compile',
		);
		$this->checkArray(C::getTemplate('wind'), 7, $config, true);
	}
	
	public function testGetFilters() {
		$this->checkArray(C::getFilters(), 1);
		$this->checkArray(C::getFilters('modules'), 0);
		$config = array(
			'class' => 'WIND:core.filter.WindFormFilter',
		);
		$this->checkArray(C::getFilters('WindFormFilter'), 1, $config, true);
	}
	
	public function testGetViewerResolvers() {
		$this->checkArray(C::getViewerResolvers(), 1);
		$this->checkArray(C::getViewerResolvers('filters'), 0);
		$config = array(
			'class' => 'WIND:core.viewer.WindViewer',
		);
		$this->checkArray(C::getViewerResolvers('default'), 1, $config, true);
	}
	
	public function testGetRouter() {
		$this->checkArray(C::getRouter(), 1);
		$this->checkArray(C::getRouter('filters'), 0);
		$config = array('parser' => 'url');
		$this->checkArray(C::getRouter(), 1, $config, true);
		$this->assertEquals('url', C::getRouter('parser'));
	}
	
	public function testGetRouterParsers() {
		$this->checkArray(C::getRouterParsers(), 1);
		$this->checkArray(C::getRouterParsers('filters'), 0);
		$config = array(
			'class' => 'WIND:core.router.WindUrlBasedRouter',
		);
		$this->checkArray(C::getRouterParsers('url'), 2, $config, true);
		$config = C::getRouterParsers('url');
		$check = array(
				'a' => 'run',
				'c' => 'index',
				'm' => 'default',
			);
		$this->checkArray($config['rule'], 3, $check, true);
	}
	
	public function testGetApplications() {
		$this->checkArray(C::getApplications(), 2);
		$this->checkArray(C::getApplications('filters'), 0);
		$this->checkArray(C::getApplications(), 2, array('web', 'command'));
	}
	
	public function testGetErrorMessage() {
		$this->checkArray(C::getErrorMessage(), 1);
		$this->checkArray(C::getErrorMessage('filters'), 0);
		$config = array(
			'class' => 'WIND:core.WindErrorAction',
		);
		$this->checkArray(C::getErrorMessage('default'), 1, $config, true);
	}
}
/*

class WindBaseTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() { 
	    $suite = new PHPUnit_Framework_TestSuite('WindBaseTest_Suite');
	    $suite->addTestSuite('WTest');
	    $suite->addTestSuite('LTest');
	    $suite->addTestSuite('CTest');
	    return $suite;
	}
}*/