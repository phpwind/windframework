<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

include 'BaseTestCase.php';
include R_P . '/test/component/AllComponentTest.php';
include R_P . '/test/core/AllCoreTest.php';

class AllTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite() {
		return self::registerTest();
	}

	private static function registerTest() {
		$suite = new PHPUnit_Framework_TestSuite('AllTest_Suite');
		$suite->addTest(AllComponentTest::suite());
		$suite->addTest(ALLCoreTest::suite());
 		return $suite;
	}
}

