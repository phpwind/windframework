<?php
/* 框架版本信息 */
define('VERSION', '0.5.0');
define('PHPVERSION', '5.1.2');
/* 路径相关配置信息  */
define('D_S', DIRECTORY_SEPARATOR);
define('WIND_PATH', dirname(__FILE__) . D_S);
!defined('COMPILE_PATH') && define('COMPILE_PATH', WIND_PATH . D_S);

/* debug/log */
!defined('IS_DEBUG') && define('IS_DEBUG', 1);
!defined('LOG_DIR') && define('LOG_DIR', COMPILE_PATH . 'log');
!defined('LOG_WRITE_LEVEL') && define('LOG_WRITE_LEVEL', 0);
define('DEBUG_TIME', microtime(true));

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
	public static function run($appName = 'default', $config = '', $rootPath = '') {
		self::beforRun($appName, $config, $rootPath);
		if (!isset(self::$_app[$appName])) {
			Wind::register(($rootPath ? $rootPath : dirname($_SERVER['SCRIPT_FILENAME'])), $appName, 
				true);
			$factory = new WindFactory(@include (self::getRealPath('WIND:components_config')));
			$config = new WindSystemConfig($config, Wind::getAppName(), $factory);
			$factory->loadClassDefinitions($config->getComponents());
			$application = $factory->getInstance($config->getAppClass('windWebApp'), 
				array($config, $factory));
			if ($application === null) {
				throw new WindException('[wind.run] ' . $config->getAppClass('windWebApp'), 
					WindException::ERROR_CLASS_NOT_EXIST);
			}
			self::$_app[$appName] = $application;
		}
		self::getApp()->run();
		self::afterRun($appName, $config, $rootPath);
	}

	/**
	 * 开发环境脚本入口
	 */
	public static function runWithCompile($appName = 'default', $config = '') {
		require_once (self::getRealPath('WIND:_compile.compile'));
		self::run($appName, $config);
	
	}

	/**
	 * 返回当前appName
	 * 
	 * @return string
	 */
	public static function getAppName() {
		if (empty(self::$_currentApp))
			throw new WindException('Get appName failed.', WindException::ERROR_SYSTEM_ERROR);
		return end(self::$_currentApp);
	}

	/**
	 * 返回当前的app应用
	 * 
	 * @param string $appName
	 * @return WindWebApplication
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
		if (!$filePath)
			return;
		if (isset(self::$_imports[$filePath]))
			return self::$_imports[$filePath];
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
			if (!$dh = opendir($dirPath))
				throw new Exception('the file ' . $dirPath . ' open failed!');
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
		if (!$path)
			return;
		$alias = strtolower($alias);
		if (!empty($alias)) {
			if (!isset(self::$_namespace[$alias]) || $reset)
				self::$_namespace[$alias] = $path;
		}
		if ($includePath) {
			if (empty(self::$_includePaths)) {
				self::$_includePaths = array_unique(explode(PATH_SEPARATOR, get_include_path()));
				if (($pos = array_search('.', self::$_includePaths, true)) !== false)
					unset(self::$_includePaths[$pos]);
			}
			array_unshift(self::$_includePaths, $path);
			if (set_include_path(
				'.' . PATH_SEPARATOR . implode(PATH_SEPARATOR, self::$_includePaths)) === false) {
				throw new Exception('set include path error.');
			}
		}
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
	 * 类文件自动加载方法 callback
	 * @param string $className
	 * @param string $path
	 * @return null
	 */
	public static function autoLoad($className, $path = '') {
		if (isset(self::$_classes[$className]))
			$path = self::$_classes[$className];
		if ($path === '') {
			throw new Exception('auto load ' . $className . ' failed.');
		}
		$path .= '.' . self::$_extensions;
		if ((@include $path) === false) {
			throw new Exception(
				'[wind.Wind.autoLoad] Your requested \'' . $path . '\' was not found on this server.');
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
	 * 解析路径信息，并返回路径的详情
	 * @param string $filePath 路径信息
	 * @param boolean $suffix 是否存在文件后缀true，false，default
	 * @return string|array('isPackage','fileName','extension','realPath')
	 */
	public static function getRealPath($filePath, $suffix = '') {
		if (false !== ($pos = strpos($filePath, ':'))) {
			$namespace = self::getRootPath(substr($filePath, 0, $pos));
			$filePath = substr($filePath, $pos + 1);
		} else
			$namespace = self::getRootPath(self::getAppName());
		if ($suffix === '') {
			$suffix = self::$_extensions;
		} elseif ($suffix === true) {
			if ($pos = strrpos($filePath, '.')) {
				$suffix = substr($filePath, $pos + 1);
				$filePath = substr($filePath, 0, $pos);
			}
		}
		$filePath = str_replace('.', D_S, $filePath);
		$namespace && $filePath = $namespace . D_S . $filePath;
		return $suffix ? $filePath . '.' . $suffix : $filePath;
	}

	/**
	 * 解析路径信息，并返回路径的详情
	 * @param string $filePath 路径信息
	 * @param boolean $info 是否为目录路径
	 * @return string|array('isPackage','fileName','extension','realPath')
	 */
	public static function getRealDir($dirPath) {
		if (false !== ($pos = strpos($dirPath, ':'))) {
			$namespace = self::getRootPath(substr($dirPath, 0, $pos));
			$dirPath = substr($dirPath, $pos + 1);
		} else
			$namespace = self::getRootPath(self::getAppName());
		$namespace && $dirPath = $namespace . D_S . str_replace('.', D_S, $dirPath);
		return $dirPath;
	}

	/**
	 * 初始化框架
	 */
	public static function init() {
		if (IS_DEBUG)
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
	 * @return
	 */
	protected static function beforRun($appName, $config, $rootPath) {
		if (!$appName || in_array($appName, self::$_currentApp))
			throw new WindException('Nested request', WindException::ERROR_SYSTEM_ERROR);
		array_push(self::$_currentApp, $appName);
	}

	/**
	 * @return
	 */
	protected static function afterRun($appName, $config, $rootPath) {
		array_pop(self::$_currentApp);
		if (self::$_logger)
			self::$_logger->flush();
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
			throw new Exception(
				'[wind._checkEnvironment] current php version is lower, php ' . PHPVERSION . ' or later.', 
				E_WARNING);
		}
		function_exists('date_default_timezone_set') && date_default_timezone_set('Etc/GMT+0');
	}

	/**
	 * @param string $className
	 * @param string $classPath
	 * @return 
	 */
	private static function _setImport($className, $classPath) {
		self::$_imports[$classPath] = $className;
		if (!isset(self::$_classes[$className])) {
			$_classPath = self::getRealPath($classPath, false);
			self::$_classes[$className] = $_classPath;
		} else
			$_classPath = self::$_classes[$className];
		if (!self::$_isAutoLoad)
			self::autoLoad($className, $_classPath);
	}

	/**
	 * 注册自动加载回调方法
	 * @return
	 */
	private static function _registerAutoloader() {
		if (!self::$_isAutoLoad)
			return;
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
		self::$_classes = array();
	}
}
Wind::init();

/* 组件定义 */
define('COMPONENT_WEBAPP', 'windWebApp');
define('COMPONENT_ERRORHANDLER', 'errorHandler');
define('COMPONENT_LOGGER', 'windLogger');
define('COMPONENT_FORWARD', 'forward');
define('COMPONENT_ROUTER', 'urlBasedRouter');
define('COMPONENT_URLHELPER', 'urlHelper');
define('COMPONENT_VIEW', 'windView');
define('COMPONENT_VIEWRESOLVER', 'viewResolver');
define('COMPONENT_TEMPLATE', 'template');
define('COMPONENT_ERRORMESSAGE', 'errorMessage');
define('COMPONENT_DB', 'db');
define('COMPONENT_DISPATCHER', 'dispatcher');
define('COMPONENT_CONFIGPARSER', 'configParser');
define('COMPONENT_CACHE', 'windCache');
//TODO 迁移更新框架内部的常量定义到这里  配置/异常类型等 注意区分异常命名空间和类型
//********************约定变量***********************************
