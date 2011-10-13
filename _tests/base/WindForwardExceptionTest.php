<?php
/**
 * WindForwardException test case.
 */
class WindForwardExceptionTest extends BaseTestCase {

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindForwardException.php';
		require_once 'web\WindForward.php';
	}

	/**
	 * Tests WindForwardException->__construct()
	 */
	public function test__construct() {
		$f1 = new WindForward();
		$f1->setIsReAction(true);
		$f2 = new WindForward();
		$f2->setIsReAction(false);
		try {
			throw new WindForwardException($f1);
		} catch (Exception $e) {
			$this->assertEquals("WindForwardException", get_class($e));
			$this->assertEquals($f1, $e->getForward());
			$e->setForward($f2);
			$this->assertEquals($f2, $e->getForward());
			$this->assertNotEquals($f1, $e->getForward());
		}
	}


}

