<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

include('component/parser/WindIniParserTest.php');
include('component/parser/WindPropertiesParserTest.php');
include('component/parser/WindXmlParserTest.php');

class AllParserTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllComponentTest_Suite');
		$suite->addTestSuite('WindIniParserTest');
		$suite->addTestSuite('WindPropertiesParserTest');
		$suite->addTestSuite('WindXmlParserTest');
		return $suite;
	}
}