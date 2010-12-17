<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-14
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
include (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestCase.php');
L::import('WIND:component.config.WindXMLConfig');

class WindXMLConfigTest extends BaseTestCase {
	private $xml;
	private $nodeList;
	private $fileName;
	public function __construct() {
		$this->xml = new WindXMLConfig();
		$this->nodeList = array('info', 'books', 'school', 'jobs');
		$this->fileName = R_P . '/test/component/config/test.xml';
	}
	public function setUp() {
		$this->xml->loadFile($this->fileName);
	}
	public function tearDown() {}
	public function testLoadFileWithTrue() {
		$this->xml->loadFile($this->fileName);
		$this->isTrue('True');
	}
	public function testLoadFileWithFalse() {
		try {
			$this->xml->loadFile('');
		} catch (Exception $e) {
			$this->isFalse('False');
		}
	}
	public function resultValidator($array, $num, $memberList) {
		$this->assertTrue(is_array($array));
		$this->assertTrue(count($array) == $num);
		if (!isset($memberList)) return ;
		foreach ($memberList as $value) {
			$this->assertTrue(isset($array[$value]));
		}
	}
	public function testParse() {
		$this->resultValidator($this->xml->parser(), 4, $this->nodeList);
	}
	public function testParseWithFileName() {
		$this->resultValidator($this->xml->parser($this->filename), 4, $this->nodeList);
	}
}