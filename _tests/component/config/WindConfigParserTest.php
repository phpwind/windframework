<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
include (dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestCase.php');
L::import('WIND:component.config.WindConfigParser');
L::import('WIND:core.WindHttpRequest');

class WindConfigParserTest extends BaseTestCase {
	private $parser;
	public function __construct() {
		$this->parser = new WindConfigParser();
	}
	public function setUp() {
		$_SERVER['SCRIPT_FILENAME'] = R_P . '/test/component/config/WindConfigParserTest.php';
		$this->parser->setConfigName('test');
	}
	
	public function checkArray($array, $num, $memberList) {
		$this->assertTrue(is_array($array));
		$this->assertTrue(count($array) == $num);
		if (!isset($memberList)) return ;
		foreach ($memberList as $value) {
			$this->assertTrue(isset($array[$value]));
		}
	}
	
	public function testParserWithNoneApp() {
		$result = $this->parser->parser(WindHttpRequest::getInstance());
		$this->checkArray($result, 13, array('app', 'modules', 'filters', 'templates', 'info', 'books'));
	}
	
	public function testParserWithApp() {
		$this->parser->setConfigName('config');
		$result = $this->parser->parser(WindHttpRequest::getInstance());
		$this->checkArray($result, 9, array('app', 'modules', 'filters', 'templates', 'router'));
	}
}