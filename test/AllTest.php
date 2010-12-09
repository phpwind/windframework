<?php
require_once 'BaseTestCase.php';
require_once R_P . '/test/component/AllComponentTest.php';
require_once R_P . '/test/core/AllCoreTest.php';

class AllTest extends BaseTestSuite {
    public function __construct() {
    	$this->setName('AllTest');
    }
    
    public static function suite() { 
		$suite = new self();
		$suite->addTest(AllComponentTest::suite()); 
		$suite->addTest(AllCoreTest::suite()); 
 		return $suite;
    }
}
