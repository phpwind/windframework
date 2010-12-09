<?php
require_once 'test/BaseTestCase.php';

class viewerSuite extends BaseTestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName('viewerSuite');
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self();
	}
}

