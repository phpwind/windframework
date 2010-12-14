<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once (R_P . '/test/component/form/WindFormFilterTest.php');

class AllFormTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestCase('AllFormTest_Suite');
		$suite->addTestSuite('WindActionFormTest');
		$suite->addTestSuite('WindFormFilterTest');
		return $suite;
	}
}