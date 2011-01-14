<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

include ('core/base/WindModuleTest.php');
include ('core/config/WindConfigParserTest.php');
include ('core/exception/AllExceptionTest.php');
include ('core/factory/AllFactoryTest.php');

include ('core/filter/WindFilterFactoryTest.php');
include ('core/router/AllRouterTest.php');

include ('core/viewer/WindViewerTest.php');

include ('core/WindBaseTest.php');
include ('core/WindErrorHandleTest.php');
include ('core/WindErrorMessageTest.php');
include ('core/WindForwardTest.php');
include ('core/WindHttpRequestTest.php');
include ('core/WindHttpResponseTest.php');
include ('core/WindLayoutTest.php');
include ('core/WindMessageTest.php');
include ('core/WindSystemConfigTest.php');
include ('core/WindUrlManagerTest.php');
include ('core/WindViewTest.php');

class AllCoreTest {
	
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllCoreTest_Suite');
		$suite->addTestSuite('WindModuleTest');
		$suite->addTestSuite('WindConfigParserTest');
		$suite->addTest(AllExceptionTest::suite());
		$suite->addTest(AllFactoryTest::suite());
		$suite->addTestSuite('WindFilterFactoryTest');
		$suite->addTest(AllRouterTest::suite());
		
		$suite->addTestSuite('WindViewerTest');
		$suite->addTestSuite('WindBaseTest');
		$suite->addTestSuite('WindErrorHandleTest');
		$suite->addTestSuite('WindErrorMessageTest');
		$suite->addTestSuite('WindForwardTest');
		$suite->addTestSuite('WindHttpRequestTest');
		$suite->addTestSuite('WindHttpResponseTest');
		$suite->addTestSuite('WindMessageTest');
		$suite->addTestSuite('WindSystemConfigTest');
		$suite->addTestSuite('WindUrlManagerTest');
		$suite->addTestSuite('WindViewTest');
		return $suite;
	}
}
