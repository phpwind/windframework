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
	public function setUp() {
		parent::setUp();
		require_once ('core/WindSystemConfig.php');
		$this->config = new WindSystemConfig(include 'data/config.php');
	}
	
	public function testGetConfig() {
		$config = $this->config->getConfig('wind');
		$this->assertTrue(is_array($config) && count($config) == 0);
		$config = $this->config->getConfig('rootPath');
		$this->assertTrue($config == '');
	}
	
	public function testGetRootPath() {
		$_SERVER['SCRIPT_FILENAME'] = '';
		$config = $this->config->getRootPath();
		$this->assertTrue($config == '');
		$_SERVER['SCRIPT_FILENAME'] = 'D:/PHPWIND/test.php';
		$this->assertTrue('D:/PHPWIND' == $this->config->getRootPath());
	}
	
	private function checkArray($array, $num, $member = array(), $ifCheck = false) {
		$this->assertTrue(is_array($array));
		$this->assertTrue($num == count($array));
		if (!$member) return;
		foreach ($member as $key => $value) {
			($ifCheck) ? $this->assertTrue(isset($array[$key]) && ($array[$key] == $value)) : $this->assertTrue(isset($array[$value]));
		}
	}
	public function testGetModules() {
		$config = $this->config->getModules();
		$this->checkArray($config, 2, array('default', 'other'));
		$this->checkArray($config['default'], 5, array('path' => 'actionControllers',
											'template' => 'default', 'controllerSuffix' => 'controller',
											'actionSuffix' => 'action', 'method' => 'run'), true);
		$this->checkArray($config['other'], 5, array('path' => 'otherControllers',
											'template' => 'wind', 'controllerSuffix' => 'controller',
											'actionSuffix' => 'action', 'method' => 'run'), true);
	}
	public function testGetTemplate() {
		$config = $this->config->getTemplate();
		$this->checkArray($config, 2, array('default', 'wind'));
		$this->checkArray($config['default'], 7, array('dir' => 'template', 'default' => 'index', 'ext' => 'htm',
					'resolver' => 'default', 'isCache' => '0', 'cacheDir' => 'cache', 'compileDir' => 'compile'), true);
		$this->checkArray($config['wind'], 7, array('dir' => 'template', 'default' => 'index', 'ext' => 'htm', 
					'resolver' => 'default', 'isCache' => '0', 'cacheDir' => 'cache', 'compileDir' => 'compile'), true);
	}
	public function testGetTemplateByName() {
		$config = $this->config->getTemplate('default');
		$this->checkArray($config, 7, array('dir' => 'template', 'default' => 'index', 'ext' => 'htm',
					'resolver' => 'default', 'isCache' => '0', 'cacheDir' => 'cache', 'compileDir' => 'compile'), true);
		$config = $this->config->getTemplate('template');
		$this->checkArray($config, 0);
	}
	public function testGetFilters() {
		$config = $this->config->getFilters();
		$this->checkArray($config, 1, array('WindFormFilter'));
		$this->checkArray($config['WindFormFilter'], 1, array('class' => 'WIND:core.filter.WindFormFilter'), true);
	}
	public function testGetFiltersByName() {
		$config = $this->config->getFilters('WindFormFilter');
		$this->checkArray($config, 1, array('class' => 'WIND:core.filter.WindFormFilter'), true);
		
		$this->checkArray($this->config->getFilters('class'), 0);
	}
	public function testGetViewerResolvers() {
		$config = $this->config->getViewerResolvers();
		$this->checkArray($config['default'], 1, array('class' => 'WIND:core.viewer.WindViewer'), true);
	}
	public function testGetViewerResolversByName() {
		$config = $this->config->getViewerResolvers('default');
		$this->assertEquals('WIND:core.viewer.WindViewer', $config['class']);
		
		$this->checkArray($this->config->getViewerResolvers('other'), 0);
	}
	public function testGetRouter() {
		$config = $this->config->getRouter();
		$this->checkArray($config, 1, array('parser' => 'url'), true);
	}

	public function testGetRouterByName() {
		$this->assertEquals('url', $this->config->getRouter('parser'));
		$this->checkArray($this->config->getRouter('parserTwo'), 0);
	}
	public function testGetRouterParsers() {
		$config = $this->config->getRouterParsers();
		$this->checkArray($config, 1, array('url'));
		$this->checkArray($config['url'], 2, array('rule', 'class'));
		$this->checkArray($config['url']['rule'], 3, array('a' => 'run', 'c' => 'index', 'm' => 'default'), true);
		$this->assertEquals('WIND:core.router.WindUrlBasedRouter', $config['url']['class']);
	}
	public function testGetRouterParsersByName() {
		$config = $this->config->getRouterParsers('url');
		$this->checkArray($config, 2, array('rule', 'class'));
		$this->checkArray($config['rule'], 3, array('a' => 'run', 'c' => 'index', 'm' => 'default'), true);
		$this->assertEquals('WIND:core.router.WindUrlBasedRouter', $config['class']);
		
		$this->checkArray($this->config->getRouterParsers('write'), 0);
	}
	public function testGetApplications() {
		$config = $this->config->getApplications();
		$this->checkArray($config, 2, array('web', 'command'));
		$this->checkArray($config['web'], 1, array('class' => 'WIND:core.WindWebApplication'), true);
		$this->checkArray($config['command'], 1, array('class' => 'WIND:core.WindCommandApplication'), true);
	}
	public function testGetApplicationsByName() {
		$config = $this->config->getApplications('web');
		$this->checkArray($config, 1, array('class' => 'WIND:core.WindWebApplication'), true);

		$this->checkArray($this->config->getApplications('web2.0'), 0);
	}
	public function testGetErrorMessage() {
		$config = $this->config->getErrorMessage();
		$this->checkArray($config['default'], 1, array('class' => 'WIND:core.WindErrorAction'), true);
	}
	public function testGetErrorMessageByName() {
		$errorConfig = $this->config->getErrorMessage('default');
		$this->assertEquals('WIND:core.WindErrorAction', $errorConfig['class']);
		
		$this->checkArray($this->config->getErrorMessage('errorMessage'), 0);
	}
	public function testGetExtensionConfig() {
		$config = $this->config->getExtensionConfig();
		$this->checkArray($config, 2, array('formConfig' => 'WIND:component.form.form_config',
							'dbConfig' => 'WIND:component.form.db_config'), true);
	}
	
	public function testGetExtensionConfigByName() {
		$config = $this->config->getExtensionConfig('formConfig');
		$this->assertEquals('WIND:component.form.form_config', $config);
		$this->checkArray($this->config->getExtensionConfig('componentConfigs'), 0);
	}
}