<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
require_once 'WindSqlBuilderTest.php';

class AllDBTest extends BaseTestSuite {
	public function __construct() {
		$this->setName('AllDBTest');
	}

    public static function suite() {
		$suite = new self();
		$suite->addTestSuite('WindSqlBuilderTest');
		return $suite;
    }
}
