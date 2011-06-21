<?php
/* 框架版本信息 */
define('VERSION', '0.5');
define('PHPVERSION', '5.1.2');
!defined('IS_DEBUG') && define('IS_DEBUG', true);
/* 路径相关配置信息  */
!defined('D_S') && define('D_S', DIRECTORY_SEPARATOR);
!defined('WIND_PATH') && define('WIND_PATH', dirname(__FILE__) . D_S);
!defined('COMPILE_PATH') && define('COMPILE_PATH', WIND_PATH . 'compile' . D_S);
!defined('COMPILE_LIBRARY_PATH') && define('COMPILE_LIBRARY_PATH', WIND_PATH . 'windlit.php');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindBase {
	private static $_namespace = array();
	private static $_imports = array();
	private static $_classes = array();
	private static $_instances = array();
	private static $_extensions = 'php';
	private static $_includePaths = array();
	private static $_isAutoLoad = true;

	/**
	 * 加载应用
	 * @param string $appName
	 * @param string $config
	 * @throws WindException
	 * @return 
	 */
	public static function run($appName = '', $config = '') {
		$frontController = new WindFrontController($appName, $config);
		$frontController->run();
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
		$alias = strtolower(trim($alias));
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
		$filePath = trim($filePath, ' ');
		$namespace = $suffix = '';
		if (!$isDir) {
			$_pos1 = strrpos($filePath, '.');
			$suffix = trim(substr($filePath, $_pos1 + 1), '.');
			$filePath = substr($filePath, 0, $_pos1);
		}
		if (($pos = strpos($filePath, ':')) !== false) {
			$namespace = self::getRootPath(substr($filePath, 0, $pos));
		}
		$filePath = str_replace('.', D_S, $filePath);
		if ($namespace) $filePath = rtrim($namespace, D_S) . D_S . substr($filePath, $pos + 1);
		return $suffix ? $filePath . '.' . $suffix : $filePath;
	}

	/**
	 * 将核心库文件打包
	 * @throws Exception
	 * @return
	 */
	public static function perLoadCoreLibrary($libPath) {
		self::import('COM:utility.WindPack');
		$pack = new WindPack();
		$fileList = array();
		foreach (self::$_imports as $key => $value) {
			$_key = self::getRealPath($key . '.' . self::$_extensions);
			$fileList[$_key] = array(
				$key, 
				$value);
		}
		$pack->packFromFileList($fileList, $libPath, WindPack::STRIP_PHP, true);
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
		if (!self::_checkPhpVersion()) throw new Exception('php version is too old, php ' . PHPVERSION . ' or later.', E_WARNING);
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
			'AbstractWindServer' => 'WIND:core.AbstractWindServer', 
			'IWindConfigParser' => 'WIND:core.config.parser.IWindConfigParser', 
			'WindConfigParser' => 'WIND:core.config.parser.WindConfigParser', 
			'WindConfig' => 'WIND:core.config.WindConfig', 
			'WindSystemConfig' => 'WIND:core.config.WindSystemConfig', 
			'AbstractWindDao' => 'COM:dao.AbstractWindDao', 
			'AbstractWindDaoFactory' => 'COM:dao.AbstractWindDaoFactory', 
			'WindConnectionManagerBasedDbTemplate' => 'WIND:core.dao.dbtemplate.WindConnectionManagerBasedDbTemplate', 
			'WindSimpleDbTemplate' => 'WIND:core.dao.dbtemplate.WindSimpleDbTemplate', 
			'IWindDbTemplate' => 'WIND:core.dao.IWindDbTemplate', 
			'WindDaoCacheListener' => 'WIND:core.dao.listener.WindDaoCacheListener', 
			'WindActionException' => 'WIND:core.exception.WindActionException', 
			'WindCacheException' => 'WIND:core.exception.WindCacheException', 
			'WindDaoException' => 'WIND:core.exception.WindDaoException', 
			'WindException' => 'WIND:core.exception.WindException', 
			'WindFinalException' => 'WIND:core.exception.WindFinalException', 
			'WindSqlException' => 'WIND:core.exception.WindSqlException', 
			'WindViewException' => 'WIND:core.exception.WindViewException', 
			'IWindFactory' => 'WIND:core.factory.IWindFactory', 
			'IWindClassProxy' => 'WIND:core.factory.proxy.IWindClassProxy', 
			'WindClassProxy' => 'WIND:core.factory.proxy.WindClassProxy', 
			'WindClassDefinition' => 'WIND:core.factory.WindClassDefinition', 
			'WindComponentDefinition' => 'WIND:core.factory.WindComponentDefinition', 
			'WindComponentFactory' => 'WIND:core.factory.WindComponentFactory', 
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
			'AbstractWindTemplateCompiler' => 'WIND:core.viewer.AbstractWindTemplateCompiler', 
			'AbstractWindViewTemplate' => 'WIND:core.viewer.AbstractWindViewTemplate', 
			'WindTemplateCompilerAction' => 'WIND:core.viewer.compiler.WindTemplateCompilerAction', 
			'WindTemplateCompilerComponent' => 'WIND:core.viewer.compiler.WindTemplateCompilerComponent', 
			'WindTemplateCompilerEcho' => 'WIND:core.viewer.compiler.WindTemplateCompilerEcho', 
			'WindTemplateCompilerInternal' => 'WIND:core.viewer.compiler.WindTemplateCompilerInternal', 
			'WindTemplateCompilerPage' => 'WIND:core.viewer.compiler.WindTemplateCompilerPage', 
			'WindTemplateCompilerScript' => 'WIND:core.viewer.compiler.WindTemplateCompilerScript', 
			'WindTemplateCompilerTemplate' => 'WIND:core.viewer.compiler.WindTemplateCompilerTemplate', 
			'WindViewTemplate' => 'WIND:core.viewer.compiler.WindViewTemplate', 
			'IWindViewerResolver' => 'WIND:core.viewer.IWindViewerResolver', 
			'WindViewCacheListener' => 'WIND:core.viewer.listener.WindViewCacheListener', 
			'WindLayout' => 'WIND:core.viewer.WindLayout', 
			'WindView' => 'WIND:core.viewer.WindView', 
			'WindViewerResolver' => 'WIND:core.viewer.WindViewerResolver', 
			'WindLoggerFilter' => 'WIND:core.web.filter.WindLoggerFilter', 
			'WindUrlFilter' => 'WIND:core.web.filter.WindUrlFilter', 
			'IWindApplication' => 'WIND:core.web.IWindApplication', 
			'IWindErrorMessage' => 'WIND:core.web.IWindErrorMessage', 
			'WindFormListener' => 'WIND:core.web.listener.WindFormListener', 
			'WindLoggerListener' => 'WIND:core.web.listener.WindLoggerListener', 
			'WindValidateListener' => 'WIND:core.web.listener.WindValidateListener', 
			'WindAction' => 'WIND:core.web.WindAction', 
			'WindController' => 'WIND:core.web.WindController', 
			'WindDispatcher' => 'WIND:core.web.WindDispatcher', 
			'WindErrorHandler' => 'WIND:core.web.WindErrorHandler', 
			'WindErrorMessage' => 'WIND:core.web.WindErrorMessage', 
			'WindFormController' => 'WIND:core.web.WindFormController', 
			'WindForward' => 'WIND:core.web.WindForward', 
			'WindFrontController' => 'WIND:core.web.WindFrontController', 
			'WindUrlHelper' => 'WIND:core.web.WindUrlHelper', 
			'WindWebApplication' => 'WIND:core.web.WindWebApplication', 
			'WindComponentModule' => 'WIND:core.WindComponentModule', 
			'WindEnableValidateModule' => 'WIND:core.WindEnableValidateModule', 
			'WindHelper' => 'WIND:core.WindHelper', 
			'WindModule' => 'WIND:core.WindModule');
	}
}
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
//TODO 迁移更新框架内部的常量定义到这里  配置/异常类型等 注意区分异常命名空间和类型
//********************约定变量***********************************
define('WIND_M_ERROR', 'windError');
define('WIND_CONFIG_CACHE', 'wind_components_config');
//**********配置*******通用常量定义***************************************
define('WIND_CONFIG_CONFIG', 'config');
define('WIND_CONFIG_CLASS', 'class');
define('WIND_CONFIG_CLASSPATH', 'path');
define('WIND_CONFIG_RESOURCE', 'resource');
define('WIND_CONFIG_VALUE', 'value');