<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once (R_P . '/test/component/message/WindMessageTest.php');
require_once (R_P . '/test/component/message/WindErrorMessageTest.php');

class AllMessageTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllMessageTest_Suite');
		$suite->addTestSuite('WindMessageTest');
		$suite->addTestSuite('WindErrorMessageTest');
		return $suite;
	}
}