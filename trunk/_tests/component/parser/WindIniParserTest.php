<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once('component/parser/WindIniParser.php');

/**
 * WindIniParser单元测试
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindIniParserTest extends BaseTestCase {
	private $parser;
	private $path;
	public function __construct() {
		$this->parser = new WindIniParser();
		$this->path = dirname(dirname(dirname(__FILE__)));
	}
	
	private function checkArray($array, $num, $member = array(), $flag = false) {
		$this->assertTrue(is_array($array));
		$this->assertEquals($num, count($array));
		if (!$member) return;
		if (!$flag) {
			foreach ((array)$member as $key) {
				$this->assertTrue(isset($array[$key]));
			} 
		} else {
			foreach ((array)$member as $key => $value) {
				$this->assertTrue(isset($array[$key]));
				$this->assertTrue($array[$key] == $value);
			} 
		}
		
	}
	public function testParser() {
		$data = $this->parser->parse($this->path . '/data/WindIniParserTest.ini');
		$this->checkArray($data, 6, array('people', 'xxx', 'xjx', 'student'));
		$this->assertTrue($data['name'] == 'test');
		$this->checkArray($data['people'], 3, array('name', 'sex', 'job'));
		$this->checkArray($data['xxx'], 3, array('name', 'school', 'sex'));
		$this->checkArray($data['xjx'], 3, array('address' => 'china', 'sex' => '0', 'job' => 'java'), true);
		$this->checkArray($data['student'], 4, array('name', 'school', 'home'));
		$this->checkArray($data['student']['home'], 1, array('address'));
		$this->checkArray($data['student']['home']['address'], 3, array('country' => 'china', 
													'city' => 'jinh', 'town' => 'changs'), true);
	}
	
	public function testParserWithNull() {
		$data = $this->parser->parse('');
		$this->checkArray($data, 0);
	}
	
	public function testBuildWithEmpty() {
		$p = '';
		$this->assertEquals('', $this->parser->buildData($p));
		//$this->checkArray($this->parser->buildData(array()), 0);
	}
	public function testBuildWithObject() {
		$data = $this->parser->buildData($this->parser);
		$this->assertTrue(is_object($data));
	}
	
	public function testToArrayWithEmpty() {
		$value = $this->parser->toArray('', '');
		$this->assertTrue(is_array($value) && (count($value) === 0));
	}
	
	public function testToArrayWithFirstValue() {
		$value = $this->parser->toArray('key', '');
		$this->assertTrue(is_array($value) && (count($value) === 1) && empty($value['key']));
	}
	
	public function testToArrayWithSecondValue() {
		$value = $this->parser->toArray('', 'key');
		$this->assertTrue(is_array($value) && (count($value) === 1) && ($value[''] == 'key'));
	}
	
	public function testToArray() {
		$value = $this->parser->toArray('key1.key2.key3.key4', 'value');
		$this->assertTrue(is_array($value) && ($value['key1']['key2']['key3']['key4'] === 'value'));
	}
	
	public function testFormatDataFromStringWithString() {
		$data = array();
		$value = $this->parser->formatDataFromString('key', 'value', $data);
		$this->assertTrue(is_array($value) && (count($value) == 1) && ($value['key'] == 'value'));
	}
	public function testFormatDataFromStringWithNoData() {
		$data = array();
		$value = $this->parser->formatDataFromString('key1.key2.key3', 'value', $data);
		$this->assertTrue(is_array($value) && (count($value) == 1) && ($value['key1']['key2']['key3'] == 'value'));
	}
	public function testFormatDataFromStringWithSameKeyData() {
		$data = array('key1' => 'xxx');
		$value = $this->parser->formatDataFromString('key1.key2.key3', 'value', $data);
		$this->assertTrue(is_array($value) && (count($value) == 1) && ($value['key1']['key2']['key3'] == 'value'));
	}
	public function testFormatDataFromString() {
		$data = array('name' => 'xxx');
		$value = $this->parser->formatDataFromString('key1.key2.key3', 'value', $data);
		$this->assertTrue(is_array($value) && (count($value) == 2));
		$this->assertTrue($value['key1']['key2']['key3'] == 'value');
		$this->assertTrue($value['name'] == 'xxx');
	}
	public function testFormatDataArray() {
		$data = array('name' => 'xxx');
		$param = array('key1.key2' => 'value1', 'key2.key3' => 'value2', 'key3' => 'value3');
		$value = $this->parser->formatDataArray($param, $data);
		$this->assertTrue(is_array($value) && (count($value) == 4));
		$this->assertTrue($value['key1']['key2'] == 'value1');
		$this->assertTrue($value['name'] == 'xxx');
		$this->assertTrue($value['key2']['key3'] == 'value2');
		$this->assertTrue($value['key3'] == 'value3');
	}
	public function testFormatDataArrayWithEmpty() {
		$data = array();
		$param = array();
		$value = $this->parser->formatDataArray($param, $data);
		$this->assertTrue(is_array($value) && (count($value) == 0));
	}
}