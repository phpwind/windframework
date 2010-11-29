<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
require_once 'TestWindSqlBuilder.php';

class WindDBAllTests extends PHPUnit_Framework_TestSuite {
	public function __construct() {
		$this->setName('WindDBAllTests');
	}

    public static function suite() {
		$suite = new self();
		$suite->addTestSuite('TestWindSqlBuilder');
		return $suite;
    }
}
