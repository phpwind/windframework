<?php
(!class_exists('PHPUnit_Framework_TestCase')) && include 'PHPUnit/Framework/TestCase.php';
define('T_P', dirname(__FILE__));
define('IS_DEBUG', true);
/* 缓存文件路径 */
define('COMPILE_PATH', T_P . '/data/compile/');
include 'WindBase.php';
abstract class BaseTestCase extends PHPUnit_Framework_TestCase {
	protected function setUp() {
		parent::setUp();
		W::init();
		C::init(include 'data/config.php');
	}
	
	protected function tearDown() {
		parent::tearDown();
		spl_autoload_unregister('L::autoLoad');
	}
}