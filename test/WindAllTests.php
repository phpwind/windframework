<?php
require_once 'BaseTestSetting.php';
require_once R_P . '/test/component/db/WindDBAllTests.php';

class WindAllTests extends PHPUnit_Framework_TestSuite {
    public function __construct() {
    	$this->setName('WindAllTests');
    }
    
    public static function suite() { 
		$suite = new self();
		$suite->addTest(WindDBAllTests::suite()); 
 		return $suite;
    }
}
