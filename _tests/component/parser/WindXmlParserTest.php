<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

require_once('component/parser/WindXmlParser.php');

/**
 * WindXMLParser单元测试
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindXmlParserTest extends BaseTestCase {
	private $parser;
	private $path;
	public function __construct() {
		$this->parser = new WindXmlParser('UTF-8');
		$this->path = dirname(__FILE__);
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
	public function testParse() {
		$data = $this->parser->parse($this->path . '/test.xml');
		$this->checkArray($data, 3, array('php', 'java', 'book'));
		$this->checkArray($data['php'], 2, array('price' => '20', 'pages' => 200), true);
		$this->checkArray($data['java'], 3, array('name' => 'C#', 'price' => 30, 'pages' => '150'), true);
		$this->checkArray($data['book'], 4, array('name' => 'C++', 'price' => 0.0002, 'isSaled' => 'true', 'author' => 'windFramework'), true);
	}
	public function testParseWithEmpty() {
		$this->assertFalse($this->parser->parse(''));
		$this->assertFalse($this->parser->parse(array()));
	}
	
	public function testBuildDataWithError() {
		$tests = array('key' => 'value');
		$data = $this->parser->buildData($tests, 'key');
		$this->assertTrue(is_array($data) && count($data) == 0);
	}
}