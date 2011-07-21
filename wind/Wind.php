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
		require_once (self::getRealPath('WIND:_compile.compile'));
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
		if (!$filePath) return;
		if (isset(self::$_imports[$filePath])) return self::$_imports[$filePath];
		if (($pos = strrpos($filePath, '.')) !== false)
			$fileName = substr($filePath, $pos + 1);
		elseif (($pos1 = strrpos($filePath, ':')) !== false)
			$fileName = substr($filePath, $pos1 + 1);
		else
			$fileName = $filePath;
		$isPackage = $fileName === '*';
		if ($isPackage) {
			$filePath = substr($filePath, 0, $pos);
			$dirPath = self::getRealDir($filePath);
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
		$path = self::getRealPath($path);
		if ((include $path) === false) {
			throw new Exception('[wind.Wind.autoLoad] auto load class ' . $className . ' failed.');
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
	public static function getRootPath($namespace) {
		$namespace = strtolower($namespace);
		return isset(self::$_namespace[$namespace]) ? self::$_namespace[$namespace] : '';
	}

	/**
	 * 解析路径信息，并返回路径的详情
	 * @param string $filePath 路径信息
	 * @param boolean $info 是否为目录路径
	 * @return string|array('isPackage','fileName','extension','realPath')
	 */
	public static function getRealPath($filePath, $suffix = '') {
		if (($pos = strpos($filePath, ':')) !== false) {
			$namespace = self::getRootPath(substr($filePath, 0, $pos));
			if (!$namespace) return $filePath;
			$filePath = $namespace . D_S . str_replace('.', D_S, substr($filePath, $pos + 1));
		}
		!$suffix && $suffix = self::$_extensions;
		return $filePath . '.' . $suffix;
	}

	/**
	 * 解析路径信息，并返回路径的详情
	 * @param string $filePath 路径信息
	 * @param boolean $info 是否为目录路径
	 * @return string|array('isPackage','fileName','extension','realPath')
	 */
	public static function getRealDir($dirPath) {
		if (($pos = strpos($dirPath, ':')) === false) return $dirPath;
		$namespace = self::getRootPath(substr($dirPath, 0, $pos));
		if (!$namespace) return $dirPath;
		$dirPath = str_replace('.', D_S, substr($dirPath, $pos + 1));
		return $namespace . D_S . $dirPath;
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
		self::register(WIND_PATH, 'WIND', true);
		self::register(WIND_PATH . 'component', 'COM', true);
	}

	/**
	 * 检查框架运行环境
	 * @return 
	 */
	private static function _checkEnvironment() {
		if (version_compare(PHP_VERSION, PHPVERSION) === -1) {
			throw new Exception('[wind._checkEnvironment] php version is lower, php ' . PHPVERSION . ' or later.', 
				E_WARNING);
		}
		if (!defined('COMPILE_PATH')) throw new Exception('compile path undefined.');
		function_exists('date_default_timezone_set') && date_default_timezone_set('Etc/GMT+0');
	}

	/**
	 * @param string $className
	 * @param string $classPath
	 * @return 
	 */
	private static function _setImport($className, $classPath) {
		self::$_imports[$classPath] = $className;
		if (self::$_isAutoLoad)
			self::$_classes[$className] = $classPath;
		else
			self::autoLoad($className, $classPath);
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
	 * 
	 * @return 
	 */
	private static function _loadBaseLib() {
		self::$_classes = array('WindLogger' => 'log/WindLogger', 
			'IWindConfigParser' => 'core/config/parser/IWindConfigParser', 
			'WindConfigParser' => 'core/config/parser/WindConfigParser', 'WindConfig' => 'core/config/WindConfig', 
			'WindSystemConfig' => 'core/config/WindSystemConfig', 
			'WindActionException' => 'core/exception/WindActionException', 
			'WindException' => 'core/exception/WindException', 
			'WindFinalException' => 'core/exception/WindFinalException', 'IWindFactory' => 'core/factory/IWindFactory', 
			'IWindClassProxy' => 'core/factory/proxy/IWindClassProxy', 
			'WindClassProxy' => 'core/factory/proxy/WindClassProxy', 
			'WindClassDefinition' => 'core/factory/WindClassDefinition', 'WindFactory' => 'core/factory/WindFactory', 
			'WindFilter' => 'core/filter/WindFilter', 'WindFilterChain' => 'core/filter/WindFilterChain', 
			'WindHandlerInterceptor' => 'core/filter/WindHandlerInterceptor', 
			'WindHandlerInterceptorChain' => 'core/filter/WindHandlerInterceptorChain', 
			'IWindRequest' => 'core/request/IWindRequest', 'WindHttpRequest' => 'core/request/WindHttpRequest', 
			'IWindResponse' => 'core/response/IWindResponse', 'WindHttpResponse' => 'core/response/WindHttpResponse', 
			'AbstractWindRouter' => 'core/router/AbstractWindRouter', 
			'WindUrlBasedRouter' => 'core/router/WindUrlBasedRouter', 
			'IWindController' => 'core/web/controller/IWindController', 
			'WindController' => 'core/web/controller/WindController', 
			'WindSimpleController' => 'core/web/controller/WindSimpleController', 
			'WindLoggerFilter' => 'core/web/filter/WindLoggerFilter', 'WindUrlFilter' => 'core/web/filter/WindUrlFilter', 
			'IWindApplication' => 'core/web/IWindApplication', 'IWindErrorMessage' => 'core/web/IWindErrorMessage', 
			'WindFormListener' => 'core/web/listener/WindFormListener', 
			'WindLoggerListener' => 'core/web/listener/WindLoggerListener', 
			'WindValidateListener' => 'core/web/listener/WindValidateListener', 
			'WindDispatcher' => 'core/web/WindDispatcher', 'WindErrorHandler' => 'core/web/WindErrorHandler', 
			'WindErrorMessage' => 'core/web/WindErrorMessage', 'WindForward' => 'core/web/WindForward', 
			'WindFrontController' => 'core/web/WindFrontController', 'WindUrlHelper' => 'core/web/WindUrlHelper', 
			'WindWebApplication' => 'core/web/WindWebApplication', 
			'WindEnableValidateModule' => 'core/WindEnableValidateModule', 'WindHelper' => 'core/WindHelper', 
			'WindModule' => 'core/WindModule');
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