<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-14
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

include ('core/factory/WindClassProxyTest.php');

class AllFactoryTest {
	public static function main() {
		PHPUnit_TestUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllFactoryTest_suite');
		$suite->addTestSuite('WindClassProxyTest');
		return $suite;
	}
}