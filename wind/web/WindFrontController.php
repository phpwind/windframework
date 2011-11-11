<?php
/**
 * 应用前端控制器
 * 
 * 应用前端控制器，负责根据应用配置启动应用，多应用管理，多应用的配置管理等.
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: WindFrontController.php 2966 2011-10-14 06:41:59Z yishuo $
 * @package wind
 */
class WindFrontController {
	const DF_COMPONENT_CONFIG = 'WIND:components_config.php';
	
	/**
	 * @var WindHttpRequest
	 */
	protected $request = null;
	/**
	 * @var WindHttpResponse
	 */
	protected $response = null;
	/**
	 * @var WindFactory
	 */
	protected $factory = null;
	
	protected $_config = array();
	protected $_app = array();
	protected $_currentApp = array();
	protected $_currentAppName = '';

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WindFactory $factory
	 */
	public function __construct($appName, $config) {
		$this->initApplication($appName, $config);
	}

	/**
	 * 创建并执行当前应用,单应用访问入口
	 */
	public function run() {
		/* @var $router WindRouter */
		$router = $this->factory->getInstance('router');
		$router->route($this->request, $this->response);
		$application = $this->createApplication();
		$this->beforRun();
		$application->run(true);
		$this->afterRun();
	}

	/** 
	 * 创建并执行当前应用
	 * 
	 * @param string $appName
	 * @param string|array $config
	 * @return void
	 */
	public function multiRun() {
		/* @var $router WindRouter */
		$router = $this->factory->getInstance('router');
		empty($this->_config['defaultApp']) || $router->setDefaultApp($this->_config['defaultApp']);
		$router->route($this->request, $this->response);
		$router->getApp() && $this->_currentAppName = $router->getApp();
		if (in_array($this->_currentAppName, $this->_currentApp)) {
			throw new WindException('[wind.beforRun] Nested request', WindException::ERROR_SYSTEM_ERROR);
		}
		array_push($this->_currentApp, $this->_currentAppName);
		$application = $this->createApplication();
		$this->beforRun();
		$application->run(true);
		$this->afterRun();
		array_pop($this->_currentApp);
		$this->_currentAppName = end($this->_currentApp);
	}

	/**
	 * 创建应用,根据应用名称创建应用
	 *
	 * @param appName
	 * @param config
	 * @return WindWebApplication
	 */
	public function createApplication() {
		$appName = $this->_currentAppName;
		if (!isset($this->_app[$appName])) {
			$config = $this->getConfig($appName);
			if (!empty($config['components'])) {
				unset($config['components']['router']);
				$this->factory->loadClassDefinitions($config['components']);
			}
			$application = $this->factory->getInstance('windApplication', 
				array($this->request, $this->response, $this->factory));
			$application->setConfig($config);
			$application->setDelayAttributes(array('handlerAdapter' => array('ref' => 'router')));
			$this->request = $this->response = $this->factory = null;
			$this->_app[$appName] = $application;
		}
		return $this->_app[$appName];
	}

	/**
	 * 注册组件对象
	 * 
	 * @param object $componentInstance
	 * @param string $componentName
	 * @param string $scope 默认值为 'application'
	 */
	public function registeComponent($componentInstance, $componentName, $scope = 'application') {
		switch ($componentName) {
			case 'request':
				$this->request = $componentInstance;
				break;
			case 'response':
				$this->response = $componentInstance;
				break;
			default:
				$this->factory->registInstance($componentInstance, $componentName, $scope);
				break;
		}
	}

	/**
	 * 设置应用配置
	 *
	 * @param string $appName
	 * @param array $config
	 */
	public function setConfig($appName, $config) {
		if (!isset($this->_config['web-apps'][$appName])) {
			$this->_config['web-apps'][$appName] = $config;
		}
	}

	/**
	 * 获取appName对应的配置信息,当appName为空时,则返回当前app配置
	 * 
	 * @param string $appName
	 */
	public function getConfig($appName = '') {
		$appName || $appName = $this->_currentAppName;
		return isset($this->_config['web-apps'][$appName]) ? $this->_config['web-apps'][$appName] : array();
	}

	/**
	 * 返回当前的app应用
	 * 
	 * @param string $appName
	 * @return WindWebApplication
	 */
	public function getApp($appName = '') {
		$appName || $appName = $this->_currentAppName;
		if (isset($this->_app[$appName])) return $this->_app[$appName];
		return null;
	}

	/**
	 * 获得当前App名称
	 *
	 * @return string
	 * @throws WindException
	 */
	public function getAppName() {
		if (!$this->_currentAppName) {
			throw new WindException('[WindFrontController.getAppName] get appName failed', 
				WindException::ERROR_SYSTEM_ERROR);
		}
		return $this->_currentAppName;
	}

	/**
	 * @param string $appName 默认appName
	 * @param array|string $config 默认配置
	 * @return void
	 */
	private function initApplication($appName, $config) {
		$this->_currentAppName = $appName;
		$this->request = new WindHttpRequest();
		$this->response = $this->request->getResponse();
		$this->factory || $this->factory = new WindFactory(@include (Wind::getRealPath(self::DF_COMPONENT_CONFIG, true)));
		$config && $this->initConfig($config, $this->factory);
		$this->_config['defaultApp'] = $appName;
		empty($this->_config['router']) || $this->factory->loadClassDefinitions(
			array('router' => $this->_config['router']));
	}

	/**
	 * @return void
	 */
	private function afterRun() {
		restore_error_handler();
		restore_exception_handler();
		$this->getApp()->getResponse()->sendResponse();
		$this->getApp()->getWindFactory()->executeDestroyMethod();
	}

	/**
	 * application run 的前置操作,重置当前环境为当前应用信息
	 * 
	 * @param appName
	 * @return void
	 */
	private function beforRun() {
		set_error_handler('WindHelper::errorHandle');
		set_exception_handler('WindHelper::exceptionHandle');
	}

	/**
	 * 解析应用配置
	 *
	 * @param array|string $config
	 * @param WindFactory $factory
	 */
	private function initConfig($config, $factory) {
		is_array($config) || $config = $factory->getInstance('configParser')->parse($config);
		foreach ($config['web-apps'] as $key => $value) {
			if (isset($this->_config['web-apps'][$key])) continue;
			$rootPath = empty($value['root-path']) ? dirname($_SERVER['SCRIPT_FILENAME']) : Wind::getRealPath(
				$value['root-path'], false);
			Wind::register($rootPath, $key, true);
			if ('default' !== $key && !empty($config['web-apps']['default'])) {
				$value = WindUtility::mergeArray($config['web-apps']['default'], $value);
			}
			$this->setConfig($key, $value);
		}
		$this->_config['router'] = isset($config['router']) ? $config['router'] : array();
	}
}

?>