<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */


class AllCoreTest {
	
    public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() { 
	    $suite = new PHPUnit_Framework_TestSuite('AllCoreTest_Suite');
	    return $suite;
	}
}
