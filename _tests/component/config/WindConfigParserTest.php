<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindConfigParserTest extends BaseTestCase {
	private $parser;
	private $path;
	public function setUp() {
		parent::setUp();
		require_once('component/config/WindConfigParser.php');
		require_once('core/WindHttpRequest.php');
		$this->parser = new WindConfigParser();
		$this->path = dirname(__FILE__);
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
		$this->checkArray($result['viewerResolvers'], 2, array('pp', 'default'));
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['pp']['class']);
	}
	public function testParserWithIni() {
		$result = $this->parser->parse('Ini', $this->path . '/config.ini', true);
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1);
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['default']['class']);
		$this->assertTrue(strrpos($result['rootPath'], 'phpwind') !== false);
	}
	public function testParserWithProperties() {
		$result = $this->parser->parse('properties', $this->path . '/config.properties', true);
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['error'], 2, array('default', 'QQ'));
		$this->assertEquals('WIND:core.WindErrorAction', $result['error']['default']['class']);
		$this->assertEquals('WindErrorAction', $result['error']['QQ']['class']);
		$this->assertTrue($result['extensionConfig']['formConfig'] == 'test:controllers');
	}
	public function testParserWithPHP() {
		$result = $this->parser->parse('PHP', $this->path . '/config.php', true);
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1);
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['default']['class']);
		$this->assertEquals('controllers.actionForm', $result['extensionConfig']['formConfig']);
	}
	public function testParserWithEmpty() {
		$result = $this->parser->parse('Empty', '', true);
		$this->checkArray($result, 10, array('modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1, array('default'));
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['default']['class']);
	}
}