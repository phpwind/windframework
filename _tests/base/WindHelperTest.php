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
		//Unable to test
	}

	/**
	 * Tests WindHelper::exceptionHandle()
	 */
	public function testExceptionHandle() {
		//Unable to Test
	}
	
	/**
	 * @dataProvider dataForGetErrorName
	 */
	public function testGetErrorName($errorNumber, $expected){
		$this->assertEquals($expected, ForWindHelper::getErrorName($errorNumber));
	}
	
	public function dataForGetErrorName(){
		$args = array();
		$args[] = array(E_ALL, "E_ALL");
		$args[] = array(E_STRICT, "E_STRICT");
		$args[] = array(3, "E_UNKNOWN");
		return $args;
	}

}

class ForWindHelper extends WindHelper{
	public static function getErrorName($errorNumber) {
		return parent::getErrorName($errorNumber);
	}
}

