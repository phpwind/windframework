<?php
(!class_exists('PHPUnit_Framework_TestCase')) && include 'PHPUnit/Framework/TestCase.php';
define('T_P', dirname(__FILE__));
define('IS_DEBUG', true);
/* 缓存文件路径 */
define('COMPILE_PATH', T_P . '/data/compile/');
include 'WindBase.php';
L::register(T_P, 'TEST');
abstract class BaseTestCase extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		parent::setUp();
		W::initWindFramework();
	}

	protected function tearDown() {
		parent::tearDown();
		spl_autoload_unregister('L::autoLoad');
	}

	protected function assertArrayEquals($array1, $array2) {
		(!is_array($array1)) && $this->fail("Error type for arg1");
		$this->assertTrue(is_array($array2) && (count($array1) == count($array2)));
		foreach ($array1 as $key => $value) {
			$this->assertTrue(isset($array2[$key]));
			if (is_array($value)) {
				$this->assertArrayEquals($value, $array2[$key]);
			} elseif (is_object($value)) {
				$this->assertEquals(get_class($value), get_class($array2[$key]));
			} else {
				$this->assertEquals($value, $array2[$key]);
			}
		}
	}
}