<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once('component/format/WindString.php');

class WindStringUTF8Test extends BaseTestCase {
	public function setUp() {
		parent::setUp();
	}
	public function tearDown() {
		parent::tearDown();
	}
	public static function providerString() {
		return array(
			array('pp', 'ppp', 1, 2),
			array('我爱中', '我爱中国!', 0, 3, 'UTF8'),
			array('爱中国!...', '我爱中国!', 1, 5, 'UTF8', true),
			array('爱中国!', '我爱中国!', 1, 5, 'UTF8'),
		);
	}
	
	public static function providerStringLen() {
		return array(
			array('ppp', 3),
			array('p国中', 3),
			array('万岁', 2),
		);
	}
	
	/**
	 * @dataProvider providerString
	 */
	public function testsubstr($rt, $string, $start, $length, $charset = 'UTF8', $falg = false) {
		$this->assertEquals($rt, WindString::substr($string, $start, $length, $charset, $falg));
	}
	
	/**
	 * @dataProvider providerString
	 */
	public function testUtf8_substr($rt, $string, $start, $length, $charset = 'UTF8', $falg = false) {
		$this->assertEquals($rt, WindString::utf8_substr($string, $start, $length, $falg));
	}
	
	/**
	 * @dataProvider providerStringLen
	 */
	public function testStrlen($str, $leng) {
		$this->assertEquals($leng, WindString::strlen($str, 'UTF-8'));
	}
	
	/**
	 * @dataProvider providerStringLen
	 */
	public function testUtf8_strlen($string, $length) {
		$this->assertEquals($length, WindString::utf8_strlen($string));
	}
	
	public static function exportData() {
		return array(
			array("i'am phpwind\\", "'i\'am phpwind\\\\'"),
			array(true, 'true'),
			array(false, 'false'),
			array(NULL, 'NULL'),
			array(12345.22, "'12345.22'"),
			array('12342.44', "'12342.44'"),
			array($this, 'NULL'),
		);
	}
	/**
	 * @dataProvider exportData
	 */
	public function testVarExportForString($source, $result) {
		$this->assertEquals($result, WindString::varExport($source));
	}
	
	public function testVarExportForArray() {
		$arr = array('key' => 'value', 'name' => 'phpwind');
		$string  = "array(\r\n\t'key' => 'value,\r\n\t'name' => 'phpwind',\r\n)";
		WindString::varExport($arr);
	//	$this->assertEquals($string, WindString::varExport($arr));//text比较
	}
}