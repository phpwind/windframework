<?php
require_once 'BaseTestCase.php';
require_once R_P . '/test/component/db/WindDBAllTests.php';

class AllTest extends PHPUnit_Framework_TestSuite {
	public function __construct() {
		$this->setName('AllTest');
	}
	
	public static function suite() {
		$suite = new self();
		$suite->addTest(WindDBAllTests::suite());
		return $suite;
	}
}
