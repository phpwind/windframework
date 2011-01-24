<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-24
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once ('core/config/parser/WindConfigParserTest.php');

class AllConfigTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllConfigTest_Suite');
		$suite->addTestSuite('WindConfigParserTest');
		return $suite;
	}
}