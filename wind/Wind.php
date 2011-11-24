<?php
/* 框架版本信息 */
define('WIND_VERSION', '0.8.8');
/* 路径相关配置信息  */
define('WIND_PATH', dirname(__FILE__) . '/');
/* 二进制:十进制  模式描述
 * 00: 0 关闭
 * 01: 1 window
 * 10: 2 log
 * 11: 3 window|log
 * */
!defined('WIND_DEBUG') && define('WIND_DEBUG', 0);
/**
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-9
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 */
class Wind {
	private static $_extensions = 'php';
	private static $_imports = array();
	private static $_classes = array();
	private static $_isAutoLoad = true;
	private static $_namespace = array();
	private static $_includePaths = array();
	/**
	 * @var WindFrontController
	 */
	private static $_frontController = null;

	/**
	 * @param IWindRequest $request
	 * @param IWindResponse $response
	 * @param WindFactory $factory
	 * @return WindFrontController
	 */
	public static function application($appName = '', $config = array()) {
		if (self::$_frontController === null) {
			self::$_frontController = new WindFrontController($appName, $config);
		}
		return self::$_frontController;
	}

	/**
	 * @see WindFrontController::getAppName()
	 * @return string
	 */
	public static function getAppName() {
		if (self::$_frontController === null) return '';
		return self::$_frontController->getAppName();
	}

	/**
	 * 返回当前的app应用
	 * 
	 * @param string $appName
	 * @see WindFrontController::getApp()
	 * @return WindWebApplication
	 */
	public static function getApp($appName = '') {
		if (self::$_frontController === null) return null;
		return self::$_frontController->getApp($appName);
	}

	/**
	 * 加载一个类或者加载一个包
	 * 如果加载的包中有子文件夹不进行循环加载
	 * 参数格式说明：'WIND:base.WFrontController'
	 * WIND 注册的应用名称，应用名称与路径信息用‘:’号分隔
	 * base.WFrontController 相对的路径信息
	 * 如果不填写应用名称 ，例如‘base.WFrontController’，那么加载路径则相对于默认的应用路径
	 *
	 * 加载一个类的参数方式：'WIND:base.WFrontController'
	 * 加载一个包的参数方式：'WIND:base.*'
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
			$dirPath = self::getRealPath($filePath, false);
			if (!$dh = opendir($dirPath)) throw new Exception('the file ' . $dirPath . ' open failed!');
			while (($file = readdir($dh)) !== false) {
				if (is_dir($dirPath . '/' . $file)) {
					if ($recursivePackage && $file !== '.' && $file !== '..' && (strpos($file, '.') !== 0)) {
						$_filePath = $filePath . '.' . $file . '.' . '*';
						self::import($_filePath, $recursivePackage);
					}
				} else {
					if (($pos = strrpos($file, '.')) === false) {
						$fileName = $file;
					} elseif (substr($file, $pos + 1) === self::$_extensions) {
						$fileName = substr($file, 0, $pos);
					} else
						continue;
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
	 * @return void
	 * @throws Exception 
	 */
	public static function register($path, $alias = '', $includePath = false, $reset = false) {
		if (!$path) return;
		$alias = strtolower($alias);
		if (!empty($alias)) {
			if (!isset(self::$_namespace[$alias]) || $reset) self::$_namespace[$alias] = rtrim($path, '/') . '/';
		}
		if ($includePath) {
			if (empty(self::$_includePaths)) {
				self::$_includePaths = array_unique(explode(PATH_SEPARATOR, get_include_path()));
				if (($pos = array_search('.', self::$_includePaths, true)) !== false) unset(self::$_includePaths[$pos]);
			}
			array_unshift(self::$_includePaths, $path);
			if (set_include_path('.' . PATH_SEPARATOR . implode(PATH_SEPARATOR, self::$_includePaths)) === false) {
				throw new Exception('[wind.register] set include path error.');
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
		if ($path)
			include $path . '.' . self::$_extensions;
		elseif (isset(self::$_classes[$className]))
			include self::$_classes[$className] . '.' . self::$_extensions;
	}

	/**
	 * @param string $key
	 * @return string|array
	 */
	public static function getImports($key = '') {
		return $key ? self::$_imports[$key] : self::$_imports;
	}

	/**
	 * 设置imports信息
	 * @param array $imports
	 */
	public static function setImports($imports) {
		self::$_imports = array_merge(self::$_imports, $imports);
	}

	/**
	 * 解析路径信息，并返回路径的详情
	 * @param string $filePath 路径信息
	 * @param boolean $suffix 是否存在文件后缀true，false，default
	 * @return string|array('isPackage','fileName','extension','realPath')
	 */
	public static function getRealPath($filePath, $suffix = '', $absolut = false) {
		if (false !== strpos($filePath, DIRECTORY_SEPARATOR)) return realpath($filePath);
		if (false !== ($pos = strpos($filePath, ':'))) {
			$namespace = self::getRootPath(substr($filePath, 0, $pos));
			$filePath = substr($filePath, $pos + 1);
		} else
			$namespace = $absolut ? self::getRootPath(self::getAppName()) : '';
		if ($suffix === '') {
			$suffix = self::$_extensions;
		} elseif ($suffix === true && false !== ($pos = strrpos($filePath, '.'))) {
			$suffix = substr($filePath, $pos + 1);
			$filePath = substr($filePath, 0, $pos);
		}
		$filePath = str_replace('.', '/', $filePath);
		$namespace && $filePath = $namespace . $filePath;
		return $suffix ? $filePath . '.' . $suffix : $filePath;
	}

	/**
	 * 解析路径信息，并返回路径的详情
	 * @param string $filePath 路径信息
	 * @param boolean $absolut 是否返回绝对路径
	 * @return string|array('isPackage','fileName','extension','realPath')
	 */
	public static function getRealDir($dirPath, $absolut = false) {
		if (false !== ($pos = strpos($dirPath, ':'))) {
			$namespace = self::getRootPath(substr($dirPath, 0, $pos));
			$dirPath = substr($dirPath, $pos + 1);
		} else
			$namespace = $absolut ? self::getRootPath(self::getAppName()) : '';
		$namespace && $dirPath = $namespace . str_replace('.', '/', $dirPath);
		return $dirPath;
	}

	/**
	 * 初始化框架
	 */
	public static function init() {
		function_exists('date_default_timezone_set') && date_default_timezone_set('Etc/GMT+0');
		self::register(WIND_PATH, 'WIND', true);
		if (!self::$_isAutoLoad) return;
		if (function_exists('spl_autoload_register'))
			spl_autoload_register('Wind::autoLoad');
		else
			self::$_isAutoLoad = false;
		self::_loadBaseLib();
	}

	/**
	 * 清理Wind import变量信息
	 * @return void
	 */
	public static function clear() {
		self::$_imports = array();
		self::$_classes = array();
	}

	/**
	 * @param string $className
	 * @param string $classPath
	 * @return void
	 */
	private static function _setImport($className, $classPath) {
		self::$_imports[$classPath] = $className;
		if (!isset(self::$_classes[$className])) {
			$_classPath = self::getRealPath($classPath, false);
			self::$_classes[$className] = $_classPath;
		} else
			$_classPath = self::$_classes[$className];
		if (!self::$_isAutoLoad) self::autoLoad($className, $_classPath);
	}

	/**
	 * 加载核心层库函数
	 * 
	 * @return void
	 */
	private static function _loadBaseLib() {
		self::$_classes = array(
			
			'IWindApplication' => 'base/IWindApplication', 
			'IWindFactory' => 'base/IWindFactory', 
			'WindActionException' => 'base/WindActionException', 
			'WindClassProxy' => 'base/WindClassProxy', 
			'WindEnableValidateModule' => 'base/WindEnableValidateModule', 
			'WindErrorMessage' => 'base/WindErrorMessage', 
			'WindException' => 'base/WindException', 
			'WindFactory' => 'base/WindFactory', 
			'WindFinalException' => 'base/WindFinalException', 
			'WindForwardException' => 'base/WindForwardException', 
			'WindHelper' => 'base/WindHelper', 
			'WindModule' => 'base/WindModule', 
			'WindActionFilter' => 'filter/WindActionFilter', 
			'WindHandlerInterceptor' => 'filter/WindHandlerInterceptor', 
			'WindHandlerInterceptorChain' => 'filter/WindHandlerInterceptorChain', 
			'WindFormFilter' => 'web/filter/WindFormFilter', 
			'WindController' => 'web/WindController', 
			'WindDispatcher' => 'web/WindDispatcher', 
			'WindErrorHandler' => 'web/WindErrorHandler', 
			'WindForward' => 'web/WindForward', 
			'WindFrontController' => 'web/WindFrontController', 
			'WindSimpleController' => 'web/WindSimpleController', 
			'WindUrlHelper' => 'web/WindUrlHelper', 
			'WindWebApplication' => 'web/WindWebApplication', 
			'AbstractWindRouter' => 'router/AbstractWindRouter', 
			'AbstractWindRoute' => 'router/route/AbstractWindRoute', 
			'WindRewriteRoute' => 'router/route/WindRewriteRoute', 
			'WindRouter' => 'router/WindRouter', 
			'IWindRequest' => 'http/request/IWindRequest', 
			'WindHttpRequest' => 'http/request/WindHttpRequest', 
			'IWindResponse' => 'http/response/IWindResponse', 
			'WindHttpResponse' => 'http/response/WindHttpResponse', 
			'WindArray' => 'utility/WindArray', 
			'WindConvert' => 'utility/WindConvert', 
			'WindCookie' => 'utility/WindCookie', 
			'WindDate' => 'utility/WindDate', 
			'WindFile' => 'utility/WindFile', 
			'WindGeneralDate' => 'utility/WindGeneralDate', 
			'WindImage' => 'utility/WindImage', 
			'WindJson' => 'utility/WindJson', 
			'WindPack' => 'utility/WindPack', 
			'WindSecurity' => 'utility/WindSecurity', 
			'WindString' => 'utility/WindString', 
			'WindUtility' => 'utility/WindUtility', 
			'WindValidator' => 'utility/WindValidator');
	}
}

Wind::init();