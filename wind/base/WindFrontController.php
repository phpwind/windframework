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
class WindFrontController {
	/**
	 * @var string
	 */
	protected $componentConfig = 'WIND:components_config.php';
	/**
	 * @var WindHttpRequest
	 */
	protected $request = null;
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
	 * 当前app名称
	 *
	 * @var string
	 */
	protected $_appName;
	/**
	 * 应用对象数组
	 *
	 * @var array
	 */
	protected $_app = array();
	/**
	 * 当前应用app名称数组
	 * 
	 * @var array
	 */
	private $_appActiveQueue = array();
	/**
	 * @var WindHandlerInterceptorChain
	 */
	private $_chain = null;

	/**
	 * @param string $appName 默认app名称
	 * @param Array|string $config 应用配置信息,支持为空或多应用配置
	 */
	public function __construct($appName, $config) {
		$appName && $this->_appName = $appName;
		$this->factory = new WindFactory(@include (Wind::getRealPath($this->componentConfig, true)));
		if ($config) {
			is_string($config) && $config = $this->factory->getInstance('configParser')->parse($config);
			if (isset($config['isclosed']) && $config['isclosed']) {
				WindHelper::triggerError('Sorry, Site has been closed!', 
					(!empty($config['isclosed-tpl']) ? $config['isclosed-tpl'] : ''));
			}
			if (!empty($config['components'])) {
				if (!empty($config['components']['resource'])) {
					$config['components'] = $this->windFactory->getInstance('configParser')->parse(
						Wind::getRealPath($config['components']['resource'], true, true));
				}
				$this->factory->loadClassDefinitions($config['components']);
			}
			$this->initConfig($config);
		}
	}

	/**
	 * 初始化配置信息
	 * 
	 * @param array $config
	 */
	protected function initConfig($config) {
		foreach ($config['web-apps'] as $key => $value) {
			$rootPath = empty($value['root-path']) ? dirname($_SERVER['SCRIPT_FILENAME']) : Wind::getRealPath(
				$value['root-path'], false);
			Wind::register($rootPath, $key, true);
			$this->_config[$key] = $value;
		}
	}

	/**
	 * 创建并执行当前应用,单应用访问入口
	 */
	public function run() {
		$this->_appName || $this->_appName = 'default';
		$this->request || $this->request = new WindHttpRequest();
		/* @var $router WindRouter */
		$router = $this->factory->getInstance('router');
		$router->route($this->request);
		$this->_run($this->createApplication());
	}

	/**
	 * 创建并返回应用
	 * 
	 * @return WindWebApplication
	 */
	public function createApplication() {
		if (!isset($this->_app[$this->_appName])) {
			/* @var $application WindWebApplication */
			$application = $this->factory->getInstance('windApplication', 
				array($this->request, new WindHttpResponse(), $this->factory));
			if (!empty($this->_config[$this->_appName])) {
				$application->setConfig($this->_config[$this->_appName]);
			}
			$this->_app[$this->_appName] = $application;
		}
		return $this->_app[$this->_appName];
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
			default:
				$this->factory->registInstance($componentInstance, $componentName, $scope);
				break;
		}
	}

	/**
	 * 返回当前app应用名称
	 * 
	 * @return string
	 */
	public function getAppName() {
		return $this->_appName;
	}

	/**
	 * 返回当前的app应用
	 * 
	 * @param string $appName
	 * @return WindWebApplication
	 */
	public function getApp($appName = '') {
		$appName || $appName = $this->_appName;
		if (isset($this->_app[$appName])) return $this->_app[$appName];
		return null;
	}

	/**
	 * @return void
	 */
	protected function afterRun() {
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
	protected function beforRun() {
		set_error_handler('WindHelper::errorHandle');
		set_exception_handler('WindHelper::exceptionHandle');
	}

	/**
	 * 创建并运行当前应用
	 * 
	 * 配合过滤链策略部署,可以通过{@see AbstractWindFrontController::registeFilter}
	 * 方法注册过滤器,当应用被执行时会判断当前时候有初始化过滤链对象,并选择是否是通过过滤链方式执行应用
	 * @param WindWebApplication $application
	 * @return void
	 */
	protected function _run($application) {
		array_push($this->_appActiveQueue, $this->_appName);
		$this->beforRun();
		if ($this->_chain !== null) {
			$this->_chain->setCallBack(array($application, 'run'), array());
			$this->_chain->getHandler()->handle();
		} else
			$application->run();
		$this->afterRun();
		array_pop($this->_appActiveQueue);
		$this->_appName = end($this->_appActiveQueue);
	}
}
?>