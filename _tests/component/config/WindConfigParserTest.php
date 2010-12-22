<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.config.WindConfigParser');
L::import('WIND:core.WindHttpRequest');

class WindConfigParserTest extends BaseTestCase {
	private $parser;
	private $path;
	public function __construct() {
		parent::setUp();
		$this->parser = new WindConfigParser();
		$this->path = dirname(__FILE__);
	}
	public function setUp() {
		$_SERVER['SCRIPT_FILENAME'] = __FILE__;
	}
	
	public function checkArray($array, $num, $memberList = array(), $flag = false) {
		$this->assertTrue(is_array($array));
		$this->assertTrue(count($array) == $num);
		if (!isset($memberList)) return ;
		foreach ($memberList as $key => $value) {
			(!$flag) ? $this->assertTrue(isset($array[$value]))
			         : $this->assertTrue(isset($array[$key]) && $array[$key] == $value);
		}
	}
	
	public function testParserWithXML() {
		$result = $this->parser->parse('XML', $this->path . '/config.xml', true);
		$this->checkArray($result, 10, array('modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 2, array('pp' => 'WIND:core.viewer.WindViewer', 
														'default' => 'WIND:core.viewer.WindViewer'), true);
	}
	public function testParserWithIni() {
		$result = $this->parser->parse('Ini', $this->path . '/config.ini', true);
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1, array('default' => 'WIND:core.viewer.WindViewer'), true);
		$this->assertTrue(strrpos($result['rootPath'], 'phpwind') !== false);
	}
	public function testParserWithProperties() {
		$result = $this->parser->parse('properties', $this->path . '/config.properties', true);
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['error'], 2, array('default' => 'WIND:core.WindErrorAction', 'QQ' => 'WindErrorAction'), true);
		$this->assertTrue($result['extensionConfig']['formConfig'] == 'test:controllers');
	}
	public function testParserWithPHP() {
		$result = $this->parser->parse('PHP', $this->path . '/config.php', true);
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1, array('default' => 'WIND:core.viewer.WindViewer'), true);
		$this->assertEquals('WindPHPWind', $result['applications']['com']['class']);
		$this->assertEquals('controllers.actionForm', $result['extensionConfig']['formConfig']);
	}
	public function testParserWithEmpty() {
		$result = $this->parser->parse('Empty', '', true);
		$this->checkArray($result, 10, array('modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1, array('default' => 'WIND:core.viewer.WindViewer'), true);
	}
}