<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

include (R_P . '/test/core/base/WindModuleTest.php');
include (R_P . '/test/core/base/WindFormFilterTest.php');
include (R_P . '/test/core/WindActionFormTest.php');
include (R_P . '/test/core/WindErrorMessageTest.php');
include (R_P . '/test/core/WindMessageTest.php');

class AllCoreTest {
	
    public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() { 
	    $suite = new PHPUnit_Framework_TestSuite('AllCoreTest_Suite');
	    $suite->addTestSuite('WindModuleTest');
	    $suite->addTestSuite('WindFormFilterTest');
	    $suite->addTestSuite('WindActionFormTest');
	    $suite->addTestSuite('WindErrorMessageTest');
	    $suite->addTestSuite('WindMessageTest');
	    return $suite;
	}
}
