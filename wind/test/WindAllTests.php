<?php
require_once 'BaseTestSetting.php';
require_once R_P . '/Test/base/WindBaseAllTests.php';
require_once R_P . '/Test/core/WindCoreAllTests.php';

class WindAllTests extends PHPUnit_Framework_TestSuite {
    public function __construct() {
    	$this->setName('WindAllTests');
    }
    
    public static function suite() { 
		$suite = new self();
		$suite->addTest(WindBaseAllTests::suite()); 
		$suite->addTest(WindCoreAllTests::suite()); 
 		return $suite;
    }
}
