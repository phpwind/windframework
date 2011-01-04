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

	public function testParserConfigWithErrorConfig() {
		try {
			$result = $this->parser->parseConfig('testF', T_P . '/data/formConfig.dat');
		} catch(Exception $e) {
			$this->assertTrue($e instanceof WindException);
		}
	}
	public function testParserConfigWithXML() {
		$result = $this->parser->parseConfig('XML', T_P . '/data/test_config.xml');
		$this->checkArray($result, 10, array('modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 2, array('pp', 'default'));
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['pp']['class']);
	}
	public function testParserConfigWithIni() {
		$result = $this->parser->parseConfig('Ini', T_P . '/data/test_config.ini');
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1);
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['default']['class']);
		$this->assertTrue(strrpos($result['rootPath'], 'phpwind') !== false);
	}
	public function testParserConfigWithProperties() {
		$result = $this->parser->parseConfig('properties', T_P . '/data/test_config.properties');
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['error'], 2, array('default', 'QQ'));
		$this->assertEquals('WIND:core.WindErrorAction', $result['error']['default']['class']);
		$this->assertEquals('WindErrorAction', $result['error']['QQ']['class']);
		$this->assertTrue($result['extensionConfig']['formConfig'] == 'test:controllers');
	}
	public function testParserConfigWithPHP() {
		$result = $this->parser->parseConfig('PHP', T_P . '/data/test_config.php');
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1);
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['default']['class']);
		$this->assertEquals('controllers.actionForm', $result['extensionConfig']['formConfig']);
	}
	public function testParserConfigWithEmpty() {
		$result = $this->parser->parseConfig('Empty', '');
		$this->checkArray($result, 10, array('modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1, array('default'));
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['default']['class']);
	}
	public function testParserWithAliasException() {
		try	{
		  $result = $this->parser->parse('', '', '');
		} catch(Exception $e) {
			$this->assertTrue($e instanceof WindException);
		}
	}
	public function testParserWithFileException() {
		try	{
		  $result = $this->parser->parse('testF', '');
		} catch(Exception $e) {
			$this->assertTrue($e instanceof WindException);
		}
	}
	public function testParserWithAppend() {
		$result = $this->parser->parse('testF', T_P . '/data/formConfig.xml', 'empty');
		$this->checkArray($result, 2, array('formName', 'default'));
		$this->checkArray($result['default'], 2, array('moduleName' => 'default', 'path' => 'TEST:data'), true);
	}

	public function testParserWithHasAppend() {
		$result = $this->parser->parse('testF', T_P . '/data/formConfig.xml', 'empty');
		$this->checkArray($result, 2, array('formName', 'default'));
		$this->checkArray($result['default'], 2, array('moduleName' => 'default', 'path' => 'TEST:data'), true);
	}
	
	public function testParserWithNoAppend() {
		$result = $this->parser->parse('testF', T_P . '/data/formConfig.xml');
		$this->checkArray($result, 2, array('formName', 'default'));
		$this->checkArray($result['default'], 2, array('moduleName' => 'default', 'path' => 'TEST:data'), true);
	}
}