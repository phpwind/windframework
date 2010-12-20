<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.parser.WindIniParser');

class WindIniParserTest extends BaseTestCase {
	private $parser;
	private $filename;
	public function __construct() {
		$this->parser = new WindIniParser();
		$this->filename = R_P . '/test/component/parser/test.ini';
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
		$data = $this->parser->parse($this->filename);
		$this->checkArray($data, 4, array('people', 'xxx', 'xjx', 'student'));
		$this->checkArray($data['people'], 3, array('name', 'sex', 'job'));
		$this->checkArray($data['xxx'], 3, array('name', 'school', 'sex'));
		$this->checkArray($data['xjx'], 3, array('address' => 'china', 'sex' => '0', 'job' => 'java'), true);
		$this->checkArray($data['student'], 3, array('name', 'school', 'home'));
	}
	public function testParserWithNull() {
		$data = $this->parser->parse('');
		$this->checkArray($data, 0);
	}
	
	public function testParserWithThree() {
		$data = $this->parser->parse($this->filename);
		$this->checkArray($data['student'], 3, array('name' => 'xxx', 'school' => 'zhejiang'), true);
		$this->checkArray($data['student']['home'], 1, array('address'));
		$this->checkArray($data['student']['home']['address'], 3, array('country' => 'china', 
													'city' => 'jinh', 'town' => 'changs'), true);
	}
	public function testBuildWithEmpty() {
		$p = '';
		$this->assertEquals('', $this->parser->buildData($p));
		//$this->checkArray($this->parser->buildData(array()), 0);
	}
	public function testBuildWithObject() {
		$data = $this->parser->buildData($this);
		$this->assertTrue(is_object($data));
	}
}