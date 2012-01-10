<?php
/**
 * 前端控制器定义
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-12-27
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package base
 */
abstract class AbstractWindFrontController {
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
	/**
	 * 应用配置
	 * 
	 * @var array
	 */
	protected $_config = array();
	/**
	 * 应用对象数组
	 *
	 * @var array
	 */
	private $_app = array();
	/**
	 * 当前应用app名称数组
	 *
	 * @var array
	 */
	private $_currentApp = array();
	/**
	 * 当前app名称
	 *
	 * @var string
	 */
	private $_currentAppName = 'default';
	/**
	 * @var WindHandlerInterceptorChain
	 */
	private $_chain = null;

	/**
	 * @param string $appName 默认app名称
	 * @param Array|string $config 应用配置信息,支持为空或多应用配置
	 */
	public function __construct($appName, $config) {
		$this->initApplication($appName, $config);
		if (isset($this->_config['isclosed']) && $this->_config['isclosed']) {
			WindHelper::triggerError('Sorry, Site has been closed!', 
				(!empty($this->_config['isclosed-tpl']) ? $this->_config['isclosed-tpl'] : ''));
		}
	}

	/**
	 * @param string $appName 默认appName
	 * @param array|string $config 默认配置
	 * @return void
	 */
	abstract protected function initApplication($appName, $config);

	/**
	 * 创建并执行当前应用,单应用访问入口
	 */
	public function run() {
		if (!empty($this->_config['defaultApp'])) {
			$this->_currentAppName = $this->_config['defaultApp'];
		}
		/* @var $router WindRouter */
		$router = $this->factory->getInstance('router');
		$router->route($this->request, $this->response);
		$this->_run();
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
		if (!empty($this->_config['defaultApp'])) {
			$router->setDefaultApp($this->_config['defaultApp']);
			$router->setApp($this->_config['defaultApp']);
		}
		$router->route($this->request, $this->response);
		$router->getApp() && $this->_currentAppName = $router->getApp();
		if (in_array($this->_currentAppName, $this->_currentApp)) {
			throw new WindException('[wind.beforRun] Nested request', WindException::ERROR_SYSTEM_ERROR);
		}
		array_push($this->_currentApp, $this->_currentAppName);
		$this->_run();
		array_pop($this->_currentApp);
		$this->_currentAppName = end($this->_currentApp);
	}

	/**
	 * 注册过滤器,监听Application Run
	 *
	 * @param WindHandlerInterceptor $filter
	 */
	public function registeFilter($filter) {
		if (!$filter instanceof WindHandlerInterceptor) return;
		if ($this->_chain === null) {
			Wind::import("WIND:filter.WindHandlerInterceptorChain");
			$this->_chain = new WindHandlerInterceptorChain();
		}
		$this->_chain->addInterceptors($filter);
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
			$application = $this->factory->getInstance('windApplication', 
				array($this->request, $this->response, $this->factory));
			$application->setConfig($this->getAppConfig($appName));
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
	 * 获取appName对应的配置信息,当appName为空时,则返回当前app配置
	 * 
	 * @param string $appName
	 */
	public function getAppConfig($appName = '') {
		$appName || $appName = $this->_currentAppName;
		$_config = array();
		if (isset($this->_config['web-apps'][$appName])) {
			if ($appName === 'default' || !isset($this->_config['web-apps']['default']))
				$_config = $this->_config['web-apps'][$appName];
			else {
				$_config = WindUtility::mergeArray($this->_config['web-apps']['default'], 
					$this->_config['web-apps'][$appName]);
			}
		}
		return $_config;
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
	 * 创建并运行当前应用
	 * 
	 * 配合过滤链策略部署,可以通过{@see AbstractWindFrontController::registeFilter}
	 * 方法注册过滤器,当应用被执行时会判断当前时候有初始化过滤链对象,并选择是否是通过过滤链方式执行应用
	 * @return void
	 */
	private function _run() {
		$application = $this->createApplication();
		$this->beforRun();
		if ($this->_chain !== null) {
			$this->_chain->setCallBack(array($application, 'run'), array());
			$this->_chain->getHandler()->handle();
		} else
			$application->run();
		$this->afterRun();
	}
}
?>