<?php
require_once 'base/AllBaseTest.php';
require_once 'web/AllWebTest.php';
class AllTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('WindFramework AllTest');
		$suite->addTest(AllBaseTest::suite());
		$suite->addTest(AllWebTest::suite());
		return $suite;
	}
}