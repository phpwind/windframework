<?php

require_once 'core/WindView.php';

class WindViewTest extends BaseTestCase {
	private $templateConfig = 'default';
	
	function testCreateViewerResolver() {
		$windView = $this->createWindView();
		$viewResolver = $windView->createViewerResolver();
		$this->assertEquals('WindViewer', get_class($viewResolver), 'test create viewer resolver failed.');
	}
	
	public function testInitViewWithForward() {
		$windView = $this->createWindView();
		$this->assertEquals($windView->templateDefault, 'index');
		$this->assertEquals($windView->initViewWithForward($this->createWindForward())->templateDefault, 'testIndex');
	}
	
	/**
	 * @dataProvider providerConfig
	 */
	public function testCreateWindView($configName, $config) {
		$windView = $this->createWindView($configName, $config);
		$this->assertEquals($windView->templateDir, 'template');
		$this->assertEquals($windView->templateExt, 'htm');
		$this->assertEquals($windView->templateDefault, 'index');
		$this->assertEquals($windView->templateCacheDir, 'cache');
		$this->assertEquals($windView->templateCompileDir, 'compile');
	}
	
	/**
	 * @dataProvider providerErrorConfig
	 */
	public function testCreateWindViewWithErrorConfig($configName, $config) {
		try {
			$windView = $this->createWindView($configName, $config);
		} catch (Exception $exception) {
			$this->assertEquals(get_class($exception), 'WindException');
		}
	}
	
	private function createWindForward() {
		$forward = new WindForward();
		$forward->setTemplateName('testIndex');
		return $forward;
	}
	
	private function createWindView($configName = '', $config = array()) {
		if (!empty($config)) C::init($config);
		return new WindView($configName);
	}
	
	public function providerErrorConfig() {
		$configs = array();
		$configs[] = array('', array());
		$configs[] = array('wind', 
			array(
				'templates' => array(
					'default' => array('dir' => 'template', 'default' => 'index', 'ext' => 'htm', 
						'resolver' => 'default', 'isCache' => '0', 'cacheDir' => 'cache', 
						'compileDir' => 'compile'), 
					'wind1' => array('dir' => 'template', 'default' => 'index', 'ext' => 'htm', 
						'resolver' => 'default', 'isCache' => '0', 'cacheDir' => 'cache', 
						'compileDir' => 'compile')), 
				'viewerResolvers' => array('default' => 'WIND:core.viewer.WindViewer')));
		return $configs;
	}
	
	public function providerConfig() {
		$configs = array();
		$configs[] = array('wind', 
			array(
				'templates' => array(
					'default' => array('dir' => 'template', 'default' => 'index', 'ext' => 'htm', 
						'resolver' => 'default', 'isCache' => '0', 'cacheDir' => 'cache', 
						'compileDir' => 'compile'), 
					'wind' => array('dir' => 'template', 'default' => 'index', 'ext' => 'htm', 
						'resolver' => 'default', 'isCache' => '0', 'cacheDir' => 'cache', 
						'compileDir' => 'compile')), 
				'viewerResolvers' => array('default' => 'WIND:core.viewer.WindViewer')));
		$configs[] = array('default', 
			array(
				'templates' => array(
					'default' => array('dir' => 'template', 'default' => 'index', 'ext' => 'htm', 
						'resolver' => 'default', 'isCache' => '0', 'cacheDir' => 'cache', 
						'compileDir' => 'compile'), 
					'wind' => array('dir' => 'template', 'default' => 'index', 'ext' => 'htm', 
						'resolver' => 'default', 'isCache' => '0', 'cacheDir' => 'cache', 
						'compileDir' => 'compile')), 
				'viewerResolvers' => array('default' => 'WIND:core.viewer.WindViewer')));
		return $configs;
	}
	
	protected function setUp() {
		parent::setUp();
	}
	
	protected function tearDown() {
		parent::tearDown();
	}
}

