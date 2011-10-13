<?php

require_once 'base/WindModuleTest.php';
require_once 'base/WindActionExceptionTest.php';
require_once 'base/WindErrorMessageTest.php';
require_once 'base/WindForwardExceptionTest.php';
require_once 'base/WindExceptionTest.php';
require_once 'base/WindFactoryTest.php';
require_once 'base/WindClassProxyTest.php';
require_once 'base/WindHelperTest.php';
class AllBaseTest extends PHPUnit_Framework_TestSuite {

	public static function main() {
		PHPUnit_TestUI_TestRunner::run(self::suite());
	}

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('WindFramework AllBaseTest');
		$suite->addTestSuite('WindModuleTest');
		$suite->addTestSuite('WindActionExceptionTest');
		$suite->addTestSuite('WindErrorMessageTest');
		$suite->addTestSuite('WindForwardExceptionTest');
		$suite->addTestSuite('WindExceptionTest');
		$suite->addTestSuite('WindFactoryTest');
		$suite->addTestSuite('WindClassProxyTest');
		$suite->addTestSuite('WindHelperTest');
		return $suite;
	}
}

