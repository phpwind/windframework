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
	
	public function testParse() {
		$data = $this->parser->parse($this->path . '/data/WindXmlParserTest.xml');
		$this->checkArray($data, 5, array('tag', 'php', 'java', 'book', 'C++'));
		$this->checkArray($data['php'], 2, array('price' => '20', 'pages' => 200), true);
		$this->checkArray($data['java'], 3, array('name' => 'C#', 'price' => 30), true);
		$this->checkArray($data['java']['pages'], 2, array('150', 'pp' => 'ddd'), true);
		$this->checkArray($data['C++'], 5, array('author' => 'ddd', 'name' => 'C++', 'price' => 0.0002, 'isSaled' => 'true'), true);
		$this->checkArray($data['C++']['list'], 3);
		$this->checkArray($data['book'], 5);
		$this->checkArray($data['tag'], 2);
	}
	
	public function testWindConfigParse() {
		$data = $this->parser->parse(WIND_PATH . 'wind_config.xml');
	}
	
	public function testTestParse() {
		$data = $this->parser->parse($this->path . '/data/test.xml');
	}
	
	public function testParseWithEmpty() {
		$tmp = $this->parser->parse('');
		$this->assertTrue(is_array($tmp) && (count($tmp) == 0));
	}
	
	public function testGetChildsWithError() {
		$tests = array('key' => 'value');
		$data = $this->parser->getChilds($tests, 'key');
		$this->assertTrue(is_array($data) && count($data) == 0);
	}
}