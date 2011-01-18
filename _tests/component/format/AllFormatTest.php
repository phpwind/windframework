<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once ('component/format/WindDateTest.php');
require_once ('component/format/WindStringGBKTest.php');
require_once ('component/format/WindStringUTF8Test.php');

class AllFormatTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllFormatTest_Suite');
		$suite->addTestSuite('WindDateTest');
		$suite->addTestSuite('WindStringGBKTest');
		$suite->addTestSuite('WindStringUTF8Test');
		return $suite;
	}
}