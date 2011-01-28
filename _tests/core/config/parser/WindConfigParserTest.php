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
		require_once('core/config/parser/WindConfigParser.php');
		require_once('core/request/WindHttpRequest.php');
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


	public function testParserConfigWithErrorFile() {
		try {
			$result = $this->parser->parseConfig(T_P . '/data/phpwind.xml', 'phpwind');
		} catch(Exception $e) {
			$this->assertTrue($e instanceof WindException);
			return;
		}
		$this->fail('File Error no exists');
	}
	
	public function testParserConfigWithErrorConfig() {
		try {
			$result = $this->parser->parseConfig(T_P . '/data/formConfig.dat', 'testF');
		} catch(Exception $e) {
			$this->assertTrue($e instanceof WindException);
			return;
		}
		$this->fail('Config Error Init type');
	}
	
	public function testParserConfigWithXML() {
		$result = $this->parser->parseConfig(T_P . '/data/test_config.xml', 'XML');
		$this->checkArray($result, 4, array('imports', 'web-apps', 'filters', 'viewerResolvers'));
		$this->assertTrue(isset($result['viewerResolvers']['pp']));
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['pp']['class']);
	}
	public function testParserConfigWithIni() {
		$result = $this->parser->parseConfig(T_P . '/data/test_config.ini', 'Ini');
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1);
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['default']['class']);
		$this->assertTrue(strrpos($result['rootPath'], 'phpwind') !== false);
	}
	public function testParserConfigWithProperties() {
		$result = $this->parser->parseConfig(T_P . '/data/test_config.properties', 'properties');
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['error'], 2, array('default', 'QQ'));
		$this->assertEquals('WIND:core.WindErrorAction', $result['error']['default']['class']);
		$this->assertEquals('WindErrorAction', $result['error']['QQ']['class']);
		$this->assertTrue($result['extensionConfig']['formConfig'] == 'test:controllers');
	}
	public function testParserConfigWithPHP() {
		$result = $this->parser->parseConfig(T_P . '/data/test_config.php', 'PHP');
		$this->checkArray($result, 10, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers', 'extensionConfig'));
		$this->checkArray($result['viewerResolvers'], 1);
		$this->assertEquals('WIND:core.viewer.WindViewer', $result['viewerResolvers']['default']['class']);
		$this->assertEquals('controllers.actionForm', $result['extensionConfig']['formConfig']);
	}

	public function testParserConfigWithEmptyALL() {
		$result = $this->parser->parseConfig('', '');
		$this->checkArray($result, 2, array('imports', 'web-apps'));
	}

	public function testParserConfigWithEmpty() {
		$result = $this->parser->parseConfig('', 'Empty');
		$this->checkArray($result, 2, array('imports', 'web-apps'));
	}
	
	public function testParserWithFileException() {
		try	{
		  $result = $this->parser->parse('');
		} catch(Exception $e) {
			$this->assertTrue($e instanceof WindException);
			return;
		}
		$this->fail('No File Exception Error');
	}
	
	/*public function testParserWithErrorAppendFileException() {
		try	{
		  $result = $this->parser->parse(T_P . '/data/formConfig.xml', 'file', 'hahah');
		} catch(Exception $e) {
			$this->assertTrue($e instanceof WindException);
			return;
		}
		$this->fail('Error');
	}*/
	
	public function testParserWithNoAlias() {
		$result = $this->parser->parse(T_P . '/data/formConfig.xml');
		$this->checkArray($result, 2, array('formName', 'default'));
		$this->checkArray($result['default'], 2, array('moduleName' => 'default', 'path' => 'TEST:data'), true);
	}
	
	public function testParserWithAppend() {
		$result = $this->parser->parse(T_P . '/data/formConfig.xml', 'testF', 'empty');
		$this->checkArray($result, 2, array('formName', 'default'));
		$this->checkArray($result['default'], 2, array('moduleName' => 'default', 'path' => 'TEST:data'), true);
	}

	public function testParserWithHasAppend() {
		$result = $this->parser->parse(T_P . '/data/formConfig.xml', 'testF', 'empty');
		$this->checkArray($result, 2, array('formName', 'default'));
		$this->checkArray($result['default'], 2, array('moduleName' => 'default', 'path' => 'TEST:data'), true);
	}
	
	public function testParserWithNoAppend() {
		$result = $this->parser->parse(T_P . '/data/formConfig.xml', 'testF');
		$this->checkArray($result, 2, array('formName', 'default'));
		$this->checkArray($result['default'], 2, array('moduleName' => 'default', 'path' => 'TEST:data'), true);
	}
}