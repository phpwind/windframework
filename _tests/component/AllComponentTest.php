<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
include ('component/db/AllDBTest.php');
include ('component/config/WindConfigParserTest.php');
include ('component/parser/WindIniParserTest.php');

class AllComponentTest {

	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllComponentTest_Suite');
		$suite->addTest(ALLDBTest::suite());
		$suite->addTestSuite('WindConfigParserTest');
		$suite->addTestSuite('WindIniParserTest');
		return $suite;
	}
}
