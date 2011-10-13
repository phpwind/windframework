<?php
/**
 * WindHelper test case.
 */

class WindHelperTest extends BaseTestCase {
	
	protected function setUp(){
		parent::setUp();
		require_once 'base\WindHelper.php';
		require_once 'base\WindException.php';
		Wind::application("WindHelperTest");
	}
	
	protected function tearDown(){
		Wind::resetApp();
		parent::tearDown();
	}
	
	/**
	 * Tests WindHelper::errorHandle()
	 */
	public function testErrorHandle() {
		//set_error_handler("WindHelper::errorHandle");
		//$str = '111';
		//$a = unserialize($str);
	}

	/**
	 * Tests WindHelper::exceptionHandle()
	 */
	public function testExceptionHandle() {
		
	}

}

