<?php
/* 框架版本信息 */
define('VERSION', '0.5');
define('PHPVERSION', '5.1.2');
/* 路径相关配置信息  */
!defined('D_S') && define('D_S', DIRECTORY_SEPARATOR);
!defined('WIND_PATH') && define('WIND_PATH', dirname(__FILE__) . D_S);
!defined('COMPILE_PATH') && define('COMPILE_PATH', WIND_PATH . D_S);
!defined('COMPILE_LIBRARY_PATH') && define('COMPILE_LIBRARY_PATH', WIND_PATH . 'wind_basic.php');
/* debug/log */
!defined('IS_DEBUG') && define('IS_DEBUG', 1);
!defined('DEBUG_TIME') && define('DEBUG_TIME', microtime(true));
!defined('LOG_DIR') && define('LOG_DIR', COMPILE_PATH . 'log');
!defined('LOG_WRITE_LEVEL') && define('LOG_WRITE_LEVEL', 2);
/**
 * the last known user to change this file in the repository  <$LastChangedBy: yishuo $>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id: WindBase.php 2017 2011-06-22 03:51:39Z yishuo $
 * @package 
 */
class Wind {
	private static $_extensions = 'php';
	private static $_isAutoLoad = true;
	private static $_logger = null;
	private static $_namespace = array();
	private static $_imports = array();
	private static $_classes = array();
	private static $_includePaths = array();
	private static $_app = array();
	private static $_currentApp = array();

	/**
	 * 加载应用
	 * 
	 * @param string $appName
	 * @param string $config
	 * @throws WindException
	 * @return 
	 */
	public static function run($appName = 'default', $config = '') {
		self::beforRun();
		if (!isset(self::$_app[$appName])) {
			$frontController = new WindFrontController($appName, $config);
			/* 将当前的app压入数组的开始 */
			$_cache = self::getAppName();
			array_unshift(self::$_currentApp, array($appName, $_cache));
			self::$_app[$appName] = $frontController;
		}
		self::getApp()->run();
		self::afterRun();
	}

	/**
	 * 返回当前appName
	 * 
	 * @return string
	 */
	public static function getAppName() {
		if (!isset(self::$_currentApp[0])) return '';
		return self::$_currentApp[0][0];
	}

	/**
	 * 返回当前的app应用
	 * 
	 * @param string $appName
	 * @return WindFrontController
	 */
	public static function getApp() {
		$_appName = self::getAppName();
		if (isset(self::$_app[$_appName]))
			return self::$_app[$_appName];
		else
			throw new WindException('[wind.getApp] get application ' . $_appName . ' fail.', 
				WindException::ERROR_CLASS_NOT_EXIST);
	}

	/**
	 * 开发环境脚本入口
	 */
	public static function runWithCompile($appName = 'default', $config = '') {
		require_once (self::getRealPath('WIND:_compile.compile.php'));
		self::run($appName, $config);
	}

	/**
	 * 加载一个类或者加载一个包
	 * 如果加载的包中有子文件夹不进行循环加载
	 * 参数格式说明：'WIND:core.base.WFrontController'
	 * WIND 注册的应用名称，应用名称与路径信息用‘:’号分隔
	 * core.base.WFrontController 相对的路径信息
	 * 如果不填写应用名称 ，例如‘core.base.WFrontController’，那么加载路径则相对于默认的应用路径
	 *
	 * 加载一个类的参数方式：'WIND:core.base.WFrontController'
	 * 加载一个包的参数方式：'WIND:core.base.*'
	 * 
	 * @param string $filePath | 文件路径信息 或者className
	 * @param boolean $autoIncludes | 是否采用自动加载方式
	 * @param boolean $recursivePackage | 当需要加载的路径为文件夹时是否递归它
	 * @return string|null
	 */
	public static function import($filePath, $recursivePackage = false) {
		if (!$filePath) return false;
		if ($className = self::_isImported($filePath)) return $className;
		if (($pos = strrpos($filePath, '.')) !== false)
			$fileName = substr($filePath, $pos + 1);
		elseif (($pos1 = strrpos($filePath, ':')) !== false)
			$fileName = substr($filePath, $pos1 + 1);
		else
			$fileName = $filePath;
		$isPackage = $fileName === '*';
		if ($isPackage) {
			$filePath = substr($filePath, 0, $pos);
			$dirPath = self::getRealPath($filePath, true);
			if (!$dh = opendir($dirPath)) throw new Exception('the file ' . $dirPath . ' open failed!');
			while (($file = readdir($dh)) !== false) {
				if (is_dir($dirPath . D_S . $file)) {
					if ($recursivePackage && $file !== '.' && $file !== '..' && (strpos($file, '.') !== 0)) {
						$_filePath = $filePath . '.' . $file . '.' . '*';
						self::import($_filePath, $recursivePackage);
					}
				} else {
					if (($pos = strrpos($file, '.')) === false) {
						$fileName = $file;
					} else {
						if (substr($file, $pos + 1) === self::$_extensions) {
							$fileName = substr($file, 0, $pos);
						}
					}
					self::_setImport($fileName, $filePath . '.' . $fileName);
				}
			}
			closedir($dh);
		} else {
			self::_setImport($fileName, $filePath);
		}
		return $fileName;
	}

	/**
	 * 将路径信息注册到命名空间,该方法不会覆盖已经定义过的命名空间
	 * @param string $path	| 需要注册的路径
	 * @param string $name	| 路径别名
	 * @param boolean $includePath | 是否同时定义includePath
	 * @param boolean $reset | 是否覆盖已经存在的定义，默认false
	 * @return 
	 */
	public static function register($path, $alias = '', $includePath = false, $reset = false) {
		if (!$path) return;
		$alias = strtolower($alias);
		if (!empty($alias)) {
			if (!isset(self::$_namespace[$alias]) || $reset) self::$_namespace[$alias] = $path;
		}
		if ($includePath) {
			if (empty(self::$_includePaths)) {
				self::$_includePaths = array_unique(explode(PATH_SEPARATOR, get_include_path()));
				if (($pos = array_search('.', self::$_includePaths, true)) !== false) unset(self::$_includePaths[$pos]);
			}
			array_unshift(self::$_includePaths, $path);
			if (set_include_path('.' . PATH_SEPARATOR . implode(PATH_SEPARATOR, self::$_includePaths)) === false) {
				throw new Exception('set include path error.');
			}
		}
	}

	/**
	 * 类文件自动加载方法 callback
	 * @param string $className
	 * @param string $path
	 * @return null
	 */
	public static function autoLoad($className, $path = '') {
		if (isset(self::$_classes[$className])) $path = self::$_classes[$className];
		if ($path === '') {
			throw new Exception('auto load ' . $className . ' failed.');
		}
		$path = self::getRealPath($path . '.' . self::$_extensions);
		if ((include $path) === false) {
			throw new Exception('include file ' . $path . ' failed.');
		}
	}

	/**
	 * @param string $key
	 * @return string|array
	 */
	public static function getImports($key = '') {
		return $key ? self::$_imports[$key] : self::$_imports;
	}

	/**
	 * 返回命名空间的路径信息
	 * @param string $namespace
	 * @return string|Ambigous <string, multitype:>
	 */
	public static function getRootPath($namespace = '') {
		if (!$namespace) return '';
		$namespace = strtolower($namespace);
		return isset(self::$_namespace[$namespace]) ? self::$_namespace[$namespace] : '';
	}

	/**
	 * 解析路径信息，并返回路径的详情
	 * @param string $filePath 路径信息
	 * @param boolean $info 是否为目录路径
	 * @return string|array('isPackage','fileName','extension','realPath')
	 */
	public static function getRealPath($filePath, $isDir = false) {
		$namespace = $suffix = '';
		if (!$isDir) {
			$_pos1 = strrpos($filePath, '.');
			$suffix = substr($filePath, $_pos1 + 1);
			$filePath = substr($filePath, 0, $_pos1);
		}
		if (($pos = strpos($filePath, ':')) !== false) {
			$namespace = self::getRootPath(substr($filePath, 0, $pos));
		}
		$filePath = str_replace('.', D_S, $filePath);
		if ($namespace) $filePath = $namespace . D_S . substr($filePath, $pos + 1);
		return $suffix ? $filePath . '.' . $suffix : $filePath;
	}

	/**
	 * 初始化框架
	 */
	public static function init() {
		self::_checkEnvironment();
		self::_setDefaultSystemNamespace();
		self::_registerAutoloader();
		self::_loadBaseLib();
	}

	/**
	 * @param string $message
	 * @param int $level
	 */
	public static function log($message, $level = WindLogger::LEVEL_INFO, $type = 'wind.core') {
		if (IS_DEBUG && $level >= IS_DEBUG && $level != WindLogger::LEVEL_PROFILE) {
			self::getLogger()->log($message, $level, $type);
		}
	}

	/**
	 * @param $token
	 * @param $message
	 * @param $type
	 */
	public static function profileBegin($token, $message = '', $type = 'wind.core') {
		if (IS_DEBUG && WindLogger::LEVEL_PROFILE >= IS_DEBUG) {
			$msg = $token . ':' . $message;
			self::getLogger()->profileBegin($msg, $type);
		}
	}

	/**
	 * @param $token
	 * @param $message
	 * @param $type
	 */
	public static function profileEnd($token, $message = '', $type = 'wend.core') {
		if (IS_DEBUG && WindLogger::LEVEL_PROFILE >= IS_DEBUG) {
			$msg = $token . ':' . $message;
			self::getLogger()->profileEnd($msg, $type);
		}
	}

	/**
	 * 返回系统日志对象
	 * @return WindLogger
	 */
	public static function getLogger() {
		if (self::$_logger === null) {
			self::$_logger = new WindLogger(LOG_DIR, LOG_WRITE_LEVEL);
		}
		return self::$_logger;
	}

	/**
	 * 清理Wind import变量信息
	 * @return
	 */
	public static function clear() {
		self::$_imports = array();
		self::$_classes = array();
	}

	/**
	 * 重置当前应用
	 * 
	 * @return
	 */
	protected static function resetApp() {
		if (!isset(self::$_currentApp[0])) return;
		$_current = self::$_currentApp[0];
		if ($_current[1] === '') return;
		foreach (self::$_currentApp as $key => $value) {
			if ($value[0] == $_current[1]) {
				array_unshift(self::$_currentApp, $value);
				unset(self::$_currentApp[$key]);
			}
		}
	}

	/**
	 * @return
	 */
	protected static function beforRun() {
		self::profileBegin('WINDAPP', 'wind app run time profiles!', 'wind.profile');
		set_error_handler(array(new WindErrorHandler(), 'errorHandle'), error_reporting());
		set_exception_handler(array(new WindErrorHandler(), 'exceptionHandle'));
	}

	/**
	 * @return
	 */
	protected static function afterRun() {
		self::resetApp();
		restore_error_handler();
		restore_exception_handler();
		self::profileEnd('WINDAPP');
		self::getLogger()->flush();
	}

	/**
	 * 系统命名空间注册方法
	 * @return 
	 */
	private static function _setDefaultSystemNamespace() {
		self::register(WIND_PATH, 'WIND');
		self::register(WIND_PATH . 'component' . D_S, 'COM');
	}

	/**
	 * 检查框架运行环境
	 * @return 
	 */
	private static function _checkEnvironment() {
		if (!self::_checkPhpVersion()) throw new Exception('php version is too old, php ' . PHPVERSION . ' or later.', 
			E_WARNING);
		if (!defined('COMPILE_PATH')) throw new Exception('compile path undefined.');
		function_exists('date_default_timezone_set') && date_default_timezone_set('Etc/GMT+0');
	}

	/**
	 * @param string $className
	 * @param string $classPath
	 * @return 
	 */
	private static function _setImport($className, $classPath) {
		if (self::_isImported($className)) return;
		self::$_imports[$classPath] = $className;
		if (self::$_isAutoLoad)
			self::$_classes[$className] = $classPath;
		else
			self::autoLoad($className, $classPath);
	}

	/**
	 * 判断是否类是否已经被加载，如果已经被加载则返回路径信息，如果没有被加载则返回false
	 * @param string $path
	 * @return boolean|string
	 */
	private static function _isImported($param) {
		if (isset(self::$_imports[$param])) return self::$_imports[$param];
		if (in_array($param, self::$_imports)) return $param;
		return false;
	}

	/**
	 * 注册自动加载回调方法
	 * @return
	 */
	private static function _registerAutoloader() {
		if (!self::$_isAutoLoad) return;
		if (function_exists('spl_autoload_register'))
			spl_autoload_register('Wind::autoLoad');
		else
			self::$_isAutoLoad = false;
	}

	/**
	 * 加载核心层库函数
	 * @return 
	 */
	private static function _loadBaseLib() {
		$_core = self::_coreLib();
		foreach ($_core as $key => $value) {
			self::_setImport($key, $value);
		}
	}

	/* private utility */
	private static function _checkPhpVersion() {
		$v1 = $v2 = $v3 = $m1 = $m2 = $m3 = 0;
		$phpversion = phpversion();
		sscanf($phpversion, "%d.%d.%d", $v1, $v2, $v3);
		sscanf(PHPVERSION, "%d.%d.%d", $m1, $m2, $m3);
		if ($v1 > $m1) return true;
		if ($v1 < $m1) return false;
		if ($v2 > $m2) return true;
		if ($v2 < $m2) return false;
		if ($v3 > $m3) return true;
		if ($v3 < $m3) return false;
		return true;
	}

	/**
	 * 核心库文件
	 * @return
	 */
	private static function _coreLib() {
		return array(
			'WindLogger' => 'COM:log.WindLogger',
			'IWindConfigParser' => 'WIND:core.config.parser.IWindConfigParser',
			'WindConfigParser' => 'WIND:core.config.parser.WindConfigParser',
			'WindConfig' => 'WIND:core.config.WindConfig',
			'WindSystemConfig' => 'WIND:core.config.WindSystemConfig',
			'WindActionException' => 'WIND:core.exception.WindActionException',
			'WindException' => 'WIND:core.exception.WindException',
			'WindFinalException' => 'WIND:core.exception.WindFinalException',
			'IWindFactory' => 'WIND:core.factory.IWindFactory',
			'IWindClassProxy' => 'WIND:core.factory.proxy.IWindClassProxy',
			'WindClassProxy' => 'WIND:core.factory.proxy.WindClassProxy',
			'WindClassDefinition' => 'WIND:core.factory.WindClassDefinition',
			'WindFactory' => 'WIND:core.factory.WindFactory',
			'WindFilter' => 'WIND:core.filter.WindFilter',
			'WindFilterChain' => 'WIND:core.filter.WindFilterChain',
			'WindHandlerInterceptor' => 'WIND:core.filter.WindHandlerInterceptor',
			'WindHandlerInterceptorChain' => 'WIND:core.filter.WindHandlerInterceptorChain',
			'IWindRequest' => 'WIND:core.request.IWindRequest',
			'WindHttpRequest' => 'WIND:core.request.WindHttpRequest',
			'IWindResponse' => 'WIND:core.response.IWindResponse',
			'WindHttpResponse' => 'WIND:core.response.WindHttpResponse',
			'AbstractWindRouter' => 'WIND:core.router.AbstractWindRouter',
			'WindUrlBasedRouter' => 'WIND:core.router.WindUrlBasedRouter',
			'IWindController' => 'WIND:core.web.controller.IWindController',
			'WindController' => 'WIND:core.web.controller.WindController',
			'WindSimpleController' => 'WIND:core.web.controller.WindSimpleController',
			'WindLoggerFilter' => 'WIND:core.web.filter.WindLoggerFilter',
			'WindUrlFilter' => 'WIND:core.web.filter.WindUrlFilter',
			'IWindApplication' => 'WIND:core.web.IWindApplication',
			'IWindErrorMessage' => 'WIND:core.web.IWindErrorMessage',
			'WindFormListener' => 'WIND:core.web.listener.WindFormListener',
			'WindLoggerListener' => 'WIND:core.web.listener.WindLoggerListener',
			'WindValidateListener' => 'WIND:core.web.listener.WindValidateListener',
			'WindDispatcher' => 'WIND:core.web.WindDispatcher',
			'WindErrorHandler' => 'WIND:core.web.WindErrorHandler',
			'WindErrorMessage' => 'WIND:core.web.WindErrorMessage',
			'WindForward' => 'WIND:core.web.WindForward',
			'WindFrontController' => 'WIND:core.web.WindFrontController',
			'WindUrlHelper' => 'WIND:core.web.WindUrlHelper',
			'WindWebApplication' => 'WIND:core.web.WindWebApplication',
			'WindEnableValidateModule' => 'WIND:core.WindEnableValidateModule',
			'WindHelper' => 'WIND:core.WindHelper',
			'WindModule' => 'WIND:core.WindModule');
	}
}
Wind::init();
/* 组件定义 */
!defined('COMPONENT_WEBAPP') && define('COMPONENT_WEBAPP', 'windWebApp');
!defined('COMPONENT_ERRORHANDLER') && define('COMPONENT_ERRORHANDLER', 'errorHandler');
!defined('COMPONENT_LOGGER') && define('COMPONENT_LOGGER', 'windLogger');
!defined('COMPONENT_FORWARD') && define('COMPONENT_FORWARD', 'forward');
!defined('COMPONENT_ROUTER') && define('COMPONENT_ROUTER', 'urlBasedRouter');
!defined('COMPONENT_URLHELPER') && define('COMPONENT_URLHELPER', 'urlHelper');
!defined('COMPONENT_VIEW') && define('COMPONENT_VIEW', 'windView');
!defined('COMPONENT_VIEWRESOLVER') && define('COMPONENT_VIEWRESOLVER', 'viewResolver');
!defined('COMPONENT_TEMPLATE') && define('COMPONENT_TEMPLATE', 'template');
!defined('COMPONENT_ERRORMESSAGE') && define('COMPONENT_ERRORMESSAGE', 'errorMessage');
!defined('COMPONENT_DB') && define('COMPONENT_DB', 'db');
!defined('COMPONENT_DISPATCHER') && define('COMPONENT_DISPATCHER', 'dispatcher');
//TODO 迁移更新框架内部的常量定义到这里  配置/异常类型等 注意区分异常命名空间和类型
//********************约定变量***********************************
define('WIND_M_ERROR', 'windError');
define('WIND_CONFIG_CACHE', '_wind_config');
//**********配置*******通用常量定义***************************************
define('WIND_CONFIG_CONFIG', 'config');
define('WIND_CONFIG_CLASS', 'class');
define('WIND_CONFIG_CLASSPATH', 'path');
define('WIND_CONFIG_RESOURCE', 'resource');
define('WIND_CONFIG_VALUE', 'value');