<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-14
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

include ('core/factory/WindClassProxyTest.php');
include ('core/factory/WindClassDefinitionTest.php');
include ('core/factory/WindComponentFactoryTest.php');
include ('core/factory/WindFactoryTest.php');

class AllFactoryTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('WindFramework AllFactoryTest');
		$suite->addTestSuite('WindClassProxyTest');
		$suite->addTestSuite('WindClassDefinitionTest');
		$suite->addTestSuite('WindComponentFactoryTest');
		$suite->addTestSuite('WindFactoryTest');
		return $suite;
	}
}