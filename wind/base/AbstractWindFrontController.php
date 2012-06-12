<?php
/**
 * 前端控制器定义
 * 
 * 初始化系统信息,初始化请求对象、组件工厂、应用实例对象等。加载系统配置、组件配置，并进行解析。
 * @author Qiong Wu <papa0924@gmail.com> 2011-12-27
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $$Id$$
 * @package base
 */
abstract class AbstractWindFrontController {
	/**
	 * request类型定义
	 * 
	 * @var string
	 */
	protected $_request = null;
	/**
	 * 组件工程实例对象
	 * 
	 * @var WindFactory
	 */
	protected $_factory = null;
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
	 * @var WindWebApplication
	 */
	private $_app = null;
	/**
	 * @var WindHandlerInterceptorChain
	 */
	private $_chain = null;
	/**
	 * @var AbstractWindCache
	 */
	private $_cache = null;
	private $_cached = false;
	protected $_errPage = 'error';

	/**
	 * @param string $appName 默认app名称
	 * @param Array|string $config 应用配置信息,支持为空或多应用配置
	 */
	public function __construct($appName, $config) {
		set_error_handler(array($this, '_errorHandle'), error_reporting());
		set_exception_handler(array($this, '_exceptionHandle'));
		$appName && $this->_appName = $appName;
		$this->_config = $config;
	}

	/**
	 * 创建并返回应用对象实例
	 *
	 * @return WindWebApplication
	 */
	abstract protected function _createApplication();

	/**
	 * 预加载系统文件,返回预加载系统文件数据
	 * 
	 * 预加载系统文件格式如下，键值为类名=>值为类的includePath，可以是相对的（如果includePath中已经包含了该地址）
	 * 也可以是绝对地址，但不能是wind的命名空间形式的地址<pre>
	 * return array(
	 * 		'WindController' => 'web/WindController', 
	 *		'WindDispatcher' => 'web/WindDispatcher'
	 * </pre>
	 * @return void
	 * @return array
	 */
	abstract protected function _loadBaseLib();

	/**
	 * 返回组建定义信息
	 * 
	 * 组件的配置标签说明：
	 * name: 		组件的名字，唯一用于在应用中获取对应组件的对象实例
	 * path: 		该组件的实现
	 * scope: 		组件对象的范围： {singleton: 单例; application: 整个应用； prototype: 当前使用}
	 * initMethod: 在应用对象生成时执行的方法
	 * destroy： 	在应用结束的时候执行的操作
	 * proxy：	 	组件是否用代理的方式调用
	 *
	 * constructor-args：构造方法的参数
	 *	constructor-arg：
	 *		name：参数的位置,起始位置从0开始，第一个参数为0，第二个参数为1
	 *		参数的值的表示方式有一下几种：
	 *		ref: 该属性是一个对象，ref的值对应着组件的名字
	 *		value: 一个字串值
	 *		path: path指向的类的实例将会被创建传递给该属性
	 *		
	 * properties: 属性的配置，表现为组件中的类属性
	 *	property: 
	 *		name:属性名称
	 *		属性值的表示方式有以下几种：
	 *		ref: 该属性是一个对象，ref的值对应着组件的名字，表现为在组件中获取方式为“_get+属性名()”称来获取
	 *		value: 一个字串值
	 *		path: path指向的类的实例将会被创建传递给该属性
	 * 
	 *
	 * config： 组件的配置-该值对应的配置会通过setConfig接口传递给组件；
	 *	resource: 指定一个外部地址，将会去包含该文件
	 * 
	 * @return array()
	 */
	abstract protected function _components();

	/**
	 * 创建并返回应用实例
	 * 
	 * @deprecated
	 * @return WindWebApplication
	 */
	public function createApplication() {
		if ($this->_app === null) {
			$application = $this->_createApplication();
			/* @var $application WindWebApplication */
			if (!empty($this->_config['web-apps'][$this->_appName])) {
				if ($this->_appName !== 'default' && isset($this->_config['web-apps']['default']) && !isset(
					$this->_config['web-apps'][$this->_appName]['_merged'])) {
					$this->_config['web-apps'][$this->_appName] = WindUtility::mergeArray(
						$this->_config['web-apps']['default'], $this->_config['web-apps'][$this->_appName]);
					$this->_config['web-apps'][$this->_appName]['_merged'] = true;
				}
				$application->setConfig($this->_config['web-apps'][$this->_appName]);
			}
			$this->_app = $application;
		}
		return $this->_app;
	}

	/**
	 * 创建并执行当前应用,单应用访问入口
	 */
	public function run() {
		$this->initConfig();
		
		$this->_appName || $this->_appName = 'default';
		/* @var $router WindRouter */
		$router = $this->getFactory()->getInstance('router');
		$router->route($this->getRequest());
		$this->_run();
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
			case 'windCache':
				$this->_cache = $componentInstance;
				$this->_cache->setKeyPrefix($this->_appName . '_system_');
				$this->getFactory()->registInstance($this->_cache, $componentName, 'application');
				break;
			case 'request':
				$this->_request = $componentInstance;
				break;
			default:
				$this->getFactory()->registInstance($componentInstance, $componentName, $scope);
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
	public function getApp() {
		return $this->_app;
	}

	/**
	 * @return WindHttpRequest
	 */
	public function getRequest() {
		if ($this->_request === null) {
			$this->_request = WindFactory::createInstance('WindHttpRequest');
		}
		return $this->_request;
	}

	/**
	 * @return WindFactory
	 */
	public function getFactory() {
		if ($this->_factory === null) {
			if ($this->_cache !== null && ($classes = $this->_cache->get('classes'))) {
				$imports = $this->_cache->get('imports');
				$classes && Wind::$_classes += $classes;
				$imports && Wind::$_imports += $imports;
				$this->_factory = $this->_cache->get('factory');
				$this->_config = $this->_cache->get('config');
				$this->_cached = true;
			}
			if (!$this->_factory) {
				$this->_loadBaseLib();
				$this->_factory = new WindFactory($this->_components());
			}
		}
		return $this->_factory;
	}

	/**
	 * 异常处理句柄
	 *
	 * @param Exception $exception
	 */
	public function _exceptionHandle($exception) {
		restore_error_handler();
		restore_exception_handler();
		$trace = $exception->getTrace();
		if (@$trace[0]['file'] == '') {
			unset($trace[0]);
			$trace = array_values($trace);
		}
		$file = @$trace[0]['file'];
		$line = @$trace[0]['line'];
		$this->showErrorMessage($exception->getMessage(), $file, $line, $trace, $exception->getCode());
	}

	/**
	 * 错误处理句柄
	 *
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	public function _errorHandle($errno, $errstr, $errfile, $errline) {
		restore_error_handler();
		restore_exception_handler();
		$trace = debug_backtrace();
		unset($trace[0]["function"], $trace[0]["args"]);
		$this->showErrorMessage($this->_friendlyErrorType($errno) . ': ' . $errstr, $errfile, $errline, $trace, $errno);
	}

	/**
	 * 错误处理
	 * 
	 * @param string $message
	 * @param string $file 异常文件
	 * @param int $line 错误发生的行
	 * @param array $trace
	 * @param int $errorcode 错误代码
	 * @throws WindFinalException
	 */
	protected function showErrorMessage($message, $file, $line, $trace, $errorcode) {
		if (WIND_DEBUG & 2) {
			$log = $message . "\r\n" . $file . ":" . $line . "\r\n";
			list($fileLines, $trace) = WindUtility::crash($file, $line, $trace);
			foreach ($trace as $key => $value) {
				$log .= $value . "\r\n";
			}
			Wind::getApp()->getComponent('windLogger')->error($log, 'error', true);
		}
	}

	/**
	 * 创建并运行当前应用
	 * 
	 * 配合过滤链策略部署,可以通过{@see AbstractWindFrontController::registeFilter}
	 * 方法注册过滤器,当应用被执行时会判断当前时候有初始化过滤链对象,并选择是否是通过过滤链方式执行应用
	 * @return void
	 */
	protected function _run() {
		$application = $this->createApplication();
		if ($this->_chain !== null) {
			$this->_chain->setCallBack(array($application, 'run'), array(true));
			$this->_chain->getHandler()->handle($this);
		} else
			$application->run($application->getConfig('filters'));
		restore_error_handler();
		restore_exception_handler();
		$this->_app->getResponse()->sendResponse();
		$this->_app->getWindFactory()->executeDestroyMethod();
		if ($this->_cache !== null && $this->_cached === false) {
			$this->_cache->set('factory', $this->_factory);
			$this->_cache->set('classes', Wind::$_classes);
			$this->_cache->set('imports', Wind::$_imports);
			$this->_cache->set('config', $this->_config);
		}
	}

	/**
	 * 初始化配置信息
	 *
	 * @param array $config
	 */
	protected function initConfig() {
		if (!$this->_config) return;
		if (is_string($this->_config)) {
			$this->_config = $this->getFactory()->getInstance('configParser')->parse($this->_config);
		}
		if (isset($this->_config['isclosed']) && $this->_config['isclosed']) {
			if ($this->_config['isclosed-tpl'])
				$this->_errPage = $this->_config['isclosed-tpl'];
			else
				$this->_errPage = 'close';
			throw new Exception('Sorry, Site has been closed!');
		}
		if (!empty($this->_config['components'])) {
			if (!empty($this->_config['components']['resource'])) {
				$this->_config['components'] += $this->getFactory()->getInstance('configParser')->parse(
					Wind::getRealPath($this->_config['components']['resource'], true, true));
			}
			$this->getFactory()->loadClassDefinitions($this->_config['components']);
			unset($this->_config['components']);
		}
		if (empty($this->_config['web-apps'])) return;
		foreach ($this->_config['web-apps'] as $key => $value) {
			$rootPath = empty($value['root-path']) ? dirname($_SERVER['SCRIPT_FILENAME']) : Wind::getRealPath(
				$value['root-path'], false);
			Wind::register($rootPath, $key, true);
		}
	}

	/**
	 * 返回友好的错误类型名
	 *
	 * @param int $type
	 * @return string|unknown
	 */
	private function _friendlyErrorType($type) {
		switch ($type) {
			case E_ERROR:
				return 'E_ERROR';
			case E_WARNING:
				return 'E_WARNING';
			case E_PARSE:
				return 'E_PARSE';
			case E_NOTICE:
				return 'E_NOTICE';
			case E_CORE_ERROR:
				return 'E_CORE_ERROR';
			case E_CORE_WARNING:
				return 'E_CORE_WARNING';
			case E_CORE_ERROR:
				return 'E_COMPILE_ERROR';
			case E_CORE_WARNING:
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR:
				return 'E_USER_ERROR';
			case E_USER_WARNING:
				return 'E_USER_WARNING';
			case E_USER_NOTICE:
				return 'E_USER_NOTICE';
			case E_STRICT:
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR:
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED:
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED:
				return 'E_USER_DEPRECATED';
		}
		return $type;
	}
}
?>