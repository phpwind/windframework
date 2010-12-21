<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

include ('core/base/WindModuleTest.php');
include ('core/filter/WindFormFilterTest.php');
include ('core/viewer/WindViewerTest.php');
include ('core/WindActionFormTest.php');
include ('core/WindErrorHandleTest.php');
include ('core/WindErrorMessageTest.php');
include ('core/WindHttpRequestTest.php');
include ('core/WindHttpResponseTest.php');
include ('core/WindLayoutTest.php');
include ('core/WindMessageTest.php');
include ('core/WindViewTest.php');

class AllCoreTest {
	
    public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() { 
	    $suite = new PHPUnit_Framework_TestSuite('AllCoreTest_Suite');
	    $suite->addTestSuite('WindModuleTest');
	    $suite->addTestSuite('WindFormFilterTest');
	    $suite->addTestSuite('WindViewerTest');
	    $suite->addTestSuite('WindActionFormTest');
	    $suite->addTestSuite('WindErrorHandleTest');
	    $suite->addTestSuite('WindErrorMessageTest');
	    $suite->addTestSuite('WindHttpRequestTest');
	    $suite->addTestSuite('WindHttpResponseTest');
	    $suite->addTestSuite('WindLayoutTest');
	    $suite->addTestSuite('WindMessageTest');
	    $suite->addTestSuite('WindViewTest');
	    return $suite;
	}
}
