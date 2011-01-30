<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-24
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * WindSystemConfig单元测试
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindSystemConfigTest extends BaseTestCase {
	private $config;
	private $testConfig;
	public function setUp() {
		parent::setUp();
		require_once ('core/config/WindSystemConfig.php');
		require_once ('core/config/parser/WindConfigParser.php');
		L::register(T_P, 'TEST');
		$this->testConfig = include 'data/config.php';
		$this->config = new WindSystemConfig($this->testConfig['wind'], new WindConfigParser(), 'testApp');
	}
	
	public function testInitConfig() {
		try {
			$this->config->initConfig('');
		} catch (Exception $e) {
			$this->assertTrue(is_file(COMPILE_PATH . 'testApp_config.php'));
			return;
		}
	}
	
	public function testGetConfig() {
		$this->assertArrayEquals($this->getComponentConfig(), $this->config->getConfig('classes'));
	}
	
	private function getComponentConfig() {
		return	array(
			'windWebApp' => array(
				'path' => 'WIND:core.web.WindWebApplication',
				'scope' => 'request',
				'proxy' => 'true',
			),
			'windLogger' => array(
				'path' => 'WIND:component.log.WindLogger',
				'scope' => 'request',
				'config' => array(
					'path' => '',
				),
			),
			'urlBasedRouter' => array(
				'path' => 'WIND:core.router.WindUrlBasedRouter',
				'scope' => 'application',
				'proxy' => 'true',
				'config' => array(
					'resource' => 'WIND:urlRouter_config',
					'suffix' => 'xml',
				),
			),
			'viewResolver' => array(
				'path' => 'WIND:core.viewer.WindViewer',
				'scope' => 'request',
			),
			'db' => array(
				'path' => 'WIND:component.db.WindConnectionManager',
				'scope' => 'singleton',
			),
		);
	}
	public function testGetConfigParser() {
		$this->assertTrue($this->config->getConfigParser() instanceof WindConfigParser);
	}
	
	public function testGetCacheName() {
		$this->assertEquals('testApp_config', $this->config->getCacheName());
	}
	
	public function testGetAppend() {
		$this->assertFalse($this->config->getAppend());
		$this->config->setAppend('php');
		$this->assertEquals('php', $this->config->getAppend());
	}
	
	public function testGetAppName() {
		$this->assertEquals('testApp', $this->config->getAppName());
	}
	
	public function testGetAppClass() {
		$this->assertEquals('windWebApp', $this->config->getAppClass());
	}
	public function testGetRootPath() {
		$_SERVER['SCRIPT_FILENAME'] = '';
		$this->assertEquals('', $this->config->getRootPath());
	}
	
	public function testGetFactory() {
		$config = $this->config->getFactory();
		$righ = array('class-definition' => 'components',
					'class' => 'WIND:core.factory.WindComponentFactory');
		$this->assertArrayEquals($righ, $config);
		$this->assertEquals('WIND:core.factory.WindComponentFactory', $this->config->getFactory('class'));
	}
	
	public function testGetFilters() {
		$filter = array('class' => 'WIND:core.filter.WindFilterChain',
					'filter1' => array('class' => 'WIND:core.web.filter.WindLoggerFilter'),);
		$this->assertArrayEquals($filter, $this->config->getFilters());
		$this->assertEquals('WIND:core.filter.WindFilterChain', $this->config->getFilters('class'));
	}
	
	public function testGetRouter() {
		$router = array('class' => 'urlBasedRouter',);
		$this->assertArrayEquals($router, $this->config->getRouter());
		$this->assertEquals('urlBasedRouter', $this->config->getRouter('class'));
	}

	public function testGetModules() {
		$modules = array('default' => array(
							'path' => 'actionControllers',
							'default' => array(
								'path' => 'template',
								'ext' => 'htm',
								'view-resolver' => array(
									'class' => 'WIND:core.viewer.WindViewer',
									'is-cache' => 'false',
									'cache-dir' => 'cache',
									'compile-dir' => 'compile',
								),
							),
						),
					);
		$this->assertArrayEquals($modules, $this->config->getModules());
		$this->assertEquals($modules['default'], $this->config->getModules('default'));
	}
	
	public function testGetTemplate() {
		throw new PHPUnit_Framework_IncompleteTestError('no complete');
	}
	
	public function testGetViewerResolvers() {
		throw new PHPUnit_Framework_IncompleteTestError('no complete');
	}
	public function testGetApplications() {
		throw new PHPUnit_Framework_IncompleteTestError('no complete');
	}
	public function testGetErrorMessage() {
		throw new PHPUnit_Framework_IncompleteTestError('no complete');
	}
}