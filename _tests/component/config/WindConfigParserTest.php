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
	public function __construct() {
		parent::setUp();
		$this->parser = new WindConfigParser();
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
		$result = $this->parser->parser('XML', 'component/config/config.xml');
		var_dump($result);
		$this->checkArray($result, 9, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers'));
		$this->checkArray($result['viewerResolvers'], 2, array('pp' => 'WIND:core.viewer.WindViewer', 
														'default' => 'WIND:core.viewer.WindViewer'), true);
		$this->assertTrue(strrpos($result['rootPath'], '_tests') !== false);
	}
	public function testParserWithIni() {
		$result = $this->parser->parser('Ini', 'component/config/config.ini');
		$this->checkArray($result, 9, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers'));
		$this->checkArray($result['viewerResolvers'], 1, array('default' => 'WIND:core.viewer.WindViewer'), true);
		$this->assertTrue(strrpos($result['rootPath'], 'phpwind') !== false);
	}
	public function testParserWithProperties() {
		$result = $this->parser->parser('properties', 'component/config/config.properties');
		$this->checkArray($result, 9, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers'));
		$this->checkArray($result['error'], 2, array('default' => 'WIND:core.WindErrorAction', 'QQ' => 'WindErrorAction'), true);
	}
	public function testParserWithPHP() {
		$result = $this->parser->parser('PHP', 'component/config/config.php');
		$this->checkArray($result, 9, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers'));
		$this->checkArray($result['viewerResolvers'], 1, array('default' => 'WIND:core.viewer.WindViewer'), true);
		$this->assertEquals('WindPHPWind', $result['applications']['com']['class']);
	}
	public function testParserWithEmpty() {
		$result = $this->parser->parser('Empty');
		$this->checkArray($result, 9, array('rootPath', 'modules', 'filters', 'templates', 
					'error', 'applications', 'viewerResolvers', 'router', 'routerParsers'));
		$this->checkArray($result['viewerResolvers'], 1, array('default' => 'WIND:core.viewer.WindViewer'), true);
		$this->assertTrue(strrpos($result['rootPath'], '_tests') !== false);
	}
}