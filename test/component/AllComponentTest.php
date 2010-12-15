<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
include (R_P . '/test/component/db/AllDBTest.php');
include (R_P . '/test/component/config/WindConfigParserTest.php');
include (R_P . '/test/component/config/WindXMLConfigTest.php');

class AllComponentTest {

	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllComponentTest_Suite');
		$suite->addTest(ALLDBTest::suite());
		$suite->addTestSuite('WindXMLConfigTest');
		$suite->addTestSuite('WindConfigParserTest');
		return $suite;
	}
}
