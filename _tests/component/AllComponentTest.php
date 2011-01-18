<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
//require_once ('component/config/WindConfigParserTest.php');
require_once ('component/db/AllDBTest.php');
require_once ('component/form/WindActionFormTest.php');
require_once ('component/form/WindFormFilterTest.php');
require_once ('component/format/AllFormatTest.php');
require_once ('component/log/AllLogTest.php');
require_once ('component/mail/AllMailTest.php');
require_once ('component/parser/AllParserTest.php');
require_once ('component/security/WindSecurityTest.php');
require_once ('component/validator/WindValidatorTest.php');
require_once('component/WindPackTest.php');

class AllComponentTest {
	
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllComponentTest_Suite');
		//$suite->addTestSuite('WindConfigParserTest');
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
