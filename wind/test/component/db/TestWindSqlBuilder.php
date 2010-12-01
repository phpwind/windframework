<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestSetting.php');
require_once(R_P . '/component/db/base/WindSqlBuilder.php');


class TestWindSqlBuilder extends PHPUnit_Framework_TestCase {
	public function testgetInsertSql() {
		
	}
	public function testgetUpdateSql() {
		
	}
	public function testgetDeleteSql() {
		
	}
	public function testgetSelectSql() {
		
	}
	public function testbuildSingleData() {
		
	}
	public function testbuildMultiData() {
		
	}
	/**
	 * 
	 */
	public function testgetDimension() {
		#验证空数组，期望得到0，结果正确
		$testVar1 = array();
		$this->assertEquals(0, WindSqlBuilder::getDimension($testVar1));
		#验证一维数组
		$testVar2 = array('xxx', 'xx1');
		$this->assertEquals(1, WindSqlBuilder::getDimension($testVar2));
		#验证第一个元素是数组，第二个元素不是数组
		$testVar3 = array(array('xxx'), 'xxx');
		$this->assertEquals(2, WindSqlBuilder::getDimension($testVar3));
		#验证第一个元素不是数组，第二个元素是数组
		$testVar4 = array('xxx', array('xxx'));
		$this->assertEquals(1, WindSqlBuilder::getDimension($testVar4));
		#验证二维数组
		$testVar5 = array(array(111), array('xxx'));
		$this->assertEquals(2, WindSqlBuilder::getDimension($testVar5));
		#验证字符串
		$testVar6 = 'xxx';
		$this->assertEquals(0, WindSqlBuilder::getDimension($testVar6));
		#验证对象
		$testVar7 = new self();
		$this->assertEquals(0, WindSqlBuilder::getDimension($testVar7));
	}
	public function testsqlFillSpace() {
		#验证字符串
		$testVar1 = 'xxx';
		$this->assertEquals(' xxx ', WindSqlBuilder::sqlFillSpace($testVar1));
		#验证数组----验证失败----方法没有判断过类型---是否需要
		$testVar2 = array('xxx');
		$this->assertEquals($testVar2, WindSqlBuilder::sqlFillSpace($testVar2));
		#验证对象----验证失败----方法没有判断过类型---是否需要
		$testVar3 = new self();
		$this->assertEquals($testVar2, WindSqlBuilder::sqlFillSpace($testVar3));
	}
}
