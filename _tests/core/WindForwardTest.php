<?php

class WindForwardTest extends BaseTestCase {
	private $windForward;
	
	protected function setUp() {
		parent::setUp();
	}
	
	protected function tearDown() {
		parent::tearDown();
	}
	
	public function __construct() {
		require_once 'core/WindForward.php';
		$this->windForward = new WindForward();
	}
}

