<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
include ('component/db/AllDBTest.php');
include ('component/config/WindConfigParserTest.php');
include ('component/form/WindActionFormTest.php');
include ('component/form/WindFormFilterTest.php');
include ('component/format/AllFormatTest.php');
include ('component/log/AllLogTest.php');
include ('component/mail/AllMailTest.php');
include ('component/parser/AllParserTest.php');
include ('component/security/WindSecurityTest.php');
include ('component/validator/WindValidatorTest.php');
include('component/WindPackTest.php');

class AllComponentTest {
	
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllComponentTest_Suite');
		$suite->addTestSuite('WindConfigParserTest');
		$suite->addTest(ALLDBTest::suite());
		$suite->addTestSuite('WindActionFormTest');
		$suite->addTestSuite('WindFormFilterTest');
		$suite->addTest(ALLFormatTest::suite());
		$suite->addTest(AllLogTest::suite());
		$suite->addTest(AllMailTest::suite());
		$suite->addTest(AllParserTest::suite());
		
		$suite->addTestSuite('WindSecurityTest');
		$suite->addTestSuite('WindValidatorTest');
		$suite->addTestSuite('WindPackTest');
		return $suite;
	}
}
