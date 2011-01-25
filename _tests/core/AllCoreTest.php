<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once ('core/config/AllConfigTest.php');
require_once ('core/exception/AllExceptionTest.php');
require_once ('core/factory/AllFactoryTest.php');

require_once ('core/filter/WindFilterChainTest.php');
require_once ('core/router/AllRouterTest.php');

require_once ('core/viewer/WindViewerTest.php');

require_once ('core/WindBaseTest.php');
require_once ('core/WindErrorHandleTest.php');
require_once ('core/WindErrorMessageTest.php');
require_once ('core/WindForwardTest.php');
require_once ('core/WindHttpRequestTest.php');
require_once ('core/WindHttpResponseTest.php');
require_once ('core/WindLayoutTest.php');
require_once ('core/WindMessageTest.php');
require_once ('core/WindSystemConfigTest.php');
require_once ('core/WindUrlManagerTest.php');
require_once ('core/WindViewTest.php');

class AllCoreTest {
	
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllCoreTest_Suite');
		$suite->addTest(AllConfigTest::suite());
		$suite->addTest(AllExceptionTest::suite());
		$suite->addTest(AllFactoryTest::suite());
		$suite->addTestSuite('WindFilterChainTest');
		$suite->addTest(AllRouterTest::suite());
		
		$suite->addTestSuite('WindViewerTest');
		$suite->addTestSuite('LTest');
		$suite->addTestSuite('WTest');
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
