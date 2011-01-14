<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-14
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

include ('core/router/WindRouterFactoryTest.php');
include ('core/router/WindUrlBaseRouterTest.php');

class AllRouterTest {
	public static function main() {
		PHPUnit_TestUI_TestRunner::run(self::suite());
	}
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllRouterTest');
		$suite->addTestSuite('WindRouterFactoryTest');
		$suite->addTestSuite('WindUrlBaseRouterTest');
		return $suite;
	}
}