<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-12
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */


require_once('component/utility/WindString.php');

class WindStringGBKTest extends BaseTestCase {
	public function setUp() {
		header('content-type:text/html; charset=gbk');
		parent::setUp();
	}
	public function tearDown() {
		parent::tearDown();
	}
	public static function providerString() {
		return array(
			array('pp', 'ppp', 1, 2),
			array('�Ұ���', '�Ұ��й�!', 0, 3, 'gbk'),
			array('���й�!...', '�Ұ��й�!', 1, 5, 'gbk', true),
			array('���й�!', '�Ұ��й�!', 1, 5, 'gbk'),
		);
	}
	
	public static function providerStringLen() {
		return array(
			array('ppp', 3),
			array('p����', 3),
			array('����', 2),
		);
	}
	
	/**
	 * @dataProvider providerString
	 */
	public function testsubstr($rt, $string, $start, $length, $charset =  WindString::GBK, $falg = false) {
		$this->assertEquals($rt, WindString::substr($string, $start, $length, $charset, $falg));
	}
	
	/**
	 * @dataProvider providerString
	 */
	public function testGbk_substr($rt, $string, $start, $length, $charset = WindString::GBK, $falg = false) {
		$this->assertEquals($rt, WindString::gbk_substr($string, $start, $length, $falg));
	}
	
	
	/**
	 * @dataProvider providerStringLen
	 */
	public function testStrlen($str, $leng) {
		$this->assertEquals($leng, WindString::strlen($str, WindString::GBK));
	}
	
	/**
	 * @dataProvider providerStringLen
	 */
	public function testGbk_strlen($string, $length) {
		$this->assertEquals($length, WindString::gbk_strlen($string));
	}
}