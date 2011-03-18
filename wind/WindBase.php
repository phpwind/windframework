<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

!defined('VERSION') && define('VERSION', '0.3');
!defined('IS_DEBUG') && define('IS_DEBUG', true);

/* 路径相关配置信息  */
!defined('D_S') && define('D_S', DIRECTORY_SEPARATOR);
!defined('WIND_PATH') && define('WIND_PATH', dirname(__FILE__) . D_S);
!defined('COMPILE_PATH') && define('COMPILE_PATH', WIND_PATH . 'compile' . D_S);
!defined('COMPILE_LIBRARY_PATH') && define('COMPILE_LIBRARY_PATH', COMPILE_PATH . 'wind_v.' . VERSION . '.php');

/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 */
class W {

	/**
	 * 加载应用
	 * 
	 * @param string $appName
	 * @param string $config
	 * @throws WindException
	 * @return WindFrontController
	 */
	static public function application($appName = '', $config = '') {
		self::initWindFramework();
		L::import('WIND:core.web.WindFrontController');
		return new WindFrontController($appName, $config);
	}

	/**
	 * 是否支持预编译
	 * 
	 * @return string
	 */
	public static function ifCompile() {
		return defined('COMPILE_PATH') ? true : false;
	}

	/**
	 * 初始化Wind框架
	 * 环境检查
	 * 系统信息注册
	 * 加载基础Lib库
	 */
	public static function initWindFramework() {
		self::checkEnvironment();
		self::systemRegister();
		self::loadBaseLib();
	}

	/**
	 * 环境检查
	 */
	private static function checkEnvironment() {
		function_exists('date_default_timezone_set') && date_default_timezone_set('Etc/GMT+0');
	}

	/**
	 * 注册自动加载器
	 * 系统信息注册
	 */
	private static function systemRegister() {
		L::registerAutoloader();
		L::register(WIND_PATH, 'WIND');
		L::register(WIND_PATH . 'component' . D_S, 'COM');
	}

	/**
	 * 自动加载框架底层类库
	 * 包括基础的抽象类和接口
	 */
	private static function loadBaseLib() {
		if (!IS_DEBUG && is_file(COMPILE_LIBRARY_PATH)) {
			return include COMPILE_LIBRARY_PATH;
		} else
			L::loadCoreLibrary();
	}

	/**
	 * 多应用注册
	 */
	static private function registerApplications() {
		$appConfigPath = WIND_PATH . '/app_config.php';
		if (file_exists($appConfigPath)) {
			$appConfig = include $appConfigPath;
			foreach ($appConfig as $appName => $appConfig) {
				L::register($appConfig['rootPath'], $appName);
			}
		}
	}

	/**
	 * 解析ControllerPath
	 * 返回解析后的controller信息，controller，module，app
	 * 
	 * @param string $controllerPath
	 * @return array
	 */
	static public function resolveController($controllerPath) {
		$_m = $_c = '';
		if (!$controllerPath) return array(
			$_c, 
			$_m);
		if (false !== ($pos = strrpos($controllerPath, '.'))) {
			$_m = substr($controllerPath, 0, $pos);
			$_c = substr($controllerPath, $pos + 1);
		} else {
			$_c = $controllerPath;
		}
		return array(
			$_c, 
			$_m);
	}

}

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class L {

	private static $_namespace = array();

	private static $_imports = array();

	private static $_classes = array();

	private static $_instances = array();

	private static $_extensions = 'php';

	private static $_includePaths = array();

	private static $_isAutoLoad = true;

	/**
	 * 将路径信息注册到命名空间
	 * 
	 * @param string $name
	 * @param string $path
	 */
	static public function register($path, $name = '', $includePath = true) {
		if ($name !== '') {
			$name = strtolower($name);
			if (!isset(self::$_namespace[$name]) && $path) {
				self::$_namespace[$name] = $path;
			}
		}
		if ($includePath) self::setIncludePath($path);
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
	 * @param string $filePath //文件路径信息 或者className
	 * @param boolean $autoIncludes //是否采用自动加载方式
	 * @author Qiong Wu
	 * @return string|null
	 */
	static public function import($filePath, $autoInclude = true, $recursivePackage = false) {
		if (!$filePath) return false;
		if ($className = self::isImported($filePath)) return $className;
		
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
						self::import($_filePath, $autoInclude, $recursivePackage);
					}
				} else {
					if (($pos = strrpos($file, '.')) === false) {
						$fileName = $file;
					} else {
						if (substr($file, $pos + 1) === self::$_extensions) {
							$fileName = substr($file, 0, $pos);
						}
					}
					self::setImport($fileName, $filePath . '.' . $fileName, $autoInclude);
				}
			}
			closedir($dh);
		} else {
			self::setImport($fileName, $filePath, $autoInclude);
		}
		return $fileName;
	}

	/**
	 * 类文件加载
	 * 
	 * @param string $className
	 * @param string $path
	 * @return null
	 */
	static public function autoLoad($className, $path = '') {
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
	 * 注册自动加载回调方法
	 * 
	 * @return
	 */
	public static function registerAutoloader() {
		if (!self::$_isAutoLoad) return;
		if (function_exists('spl_autoload_register')) {
			spl_autoload_register('L::autoLoad');
		} else
			self::setIsAutoLoad(false);
	}

	/**
	 * 解析路径信息，并返回路径的详情
	 * 
	 * @param string $filePath 路径信息
	 * @param boolean $info 是否为目录路径
	 * @return string|array('isPackage','fileName','extension','realPath')
	 */
	static public function getRealPath($filePath, $isDir = false) {
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
	 * 加载框架核心库文件
	 * 通过重定义这里预加载的类可以更改打包类库的大小
	 * 
	 * @return null
	 */
	static public function loadCoreLibrary() {
		L::import('WIND:core.exception.*');
		if (!IS_DEBUG && W::ifCompile()) {
			self::import('WIND:core.AbstractWindServer');
			self::import('WIND:core.WindComponentModule');
			self::import('WIND:core.WindEnableValidateModule');
			self::import('WIND:core.WindModule');
			self::import('WIND:core.config.*', true, true);
			self::import('WIND:core.factory.*', true, true);
			self::import('WIND:core.request.*');
			self::import('WIND:core.response.*');
			self::import('WIND:core.router.*');
			self::import('WIND:core.web.IWindApplication');
			self::import('WIND:core.web.WindAction');
			self::import('WIND:core.web.WindController');
			self::import('WIND:core.web.WindDispatcher');
			self::import('WIND:core.web.WindForward');
			self::import('WIND:core.web.WindFrontController');
			self::import('WIND:core.web.WindWebApplication');
			self::perLoadCoreLibrary();
		}
	}

	/**
	 * 预加载处理回调处理，注入内容到打包文件头部
	 * 
	 * @param array $classes 
	 * @return string
	 */
	static public function perLoadInjection($packList = array(), $classes = array()) {
		if (!empty($classes)) {
			foreach ($classes as $key => $value) {
				if (!self::isImported($key)) self::$_imports[$key] = $value;
			}
		} else {
			$imports = array();
			foreach ($packList as $key => $value) {
				$imports[$value[0]] = $value[1];
			}
			L::import('WIND:component.utility.WindString');
			return "L::perLoadInjection(array()," . WindString::varToString($imports) . ");";
		}
	}

	static private function isImported($path) {
		if (key_exists($path, self::$_imports)) return self::$_imports[$path];
		if (in_array($path, self::$_imports)) return $path;
		return false;
	}

	static public function getImports($key = '') {
		return $key ? self::$_imports[$key] : self::$_imports;
	}

	static private function getRootPath($namespace = '') {
		if (!$namespace) return '';
		$namespace = strtolower($namespace);
		return isset(self::$_namespace[$namespace]) ? self::$_namespace[$namespace] : '';
	}

	static private function setImport($className, $classPath, $autoInclude) {
		if (self::isImported($className)) return;
		self::$_imports[$classPath] = $className;
		if (self::$_isAutoLoad && $autoInclude)
			self::$_classes[$className] = $classPath;
		else
			self::autoLoad($className, $classPath);
	}

	static private function setIncludePath($path) {
		if (empty(self::$_includePaths)) {
			self::$_includePaths = array_unique(explode(PATH_SEPARATOR, get_include_path()));
			if (($pos = array_search('.', self::$_includePaths, true)) !== false) unset(self::$_includePaths[$pos]);
		}
		array_unshift(self::$_includePaths, $path);
		if (set_include_path('.' . PATH_SEPARATOR . implode(PATH_SEPARATOR, self::$_includePaths)) === false) {
			throw new Exception('set include path error.');
		}
	}

	/**
	 * 预加载核心类库
	 */
	static private function perLoadCoreLibrary() {
		$imports = L::getImports();
		self::import('COM:utility.WindPack');
		$pack = new WindPack();
		$pack->setContentInjectionCallBack(array(
			'L', 
			'perLoadInjection'));
		$fileList = array();
		foreach ($imports as $key => $value) {
			$_key = self::getRealPath($key . '.' . self::$_extensions);
			$fileList[$_key] = array(
				$key, 
				$value);
		}
		$pack->packFromFileList($fileList, COMPILE_LIBRARY_PATH, WindPack::STRIP_PHP, true);
	}

	/**
	 * @param $isAutoLoad the $isAutoLoad to set
	 * @author Qiong Wu
	 */
	public static function setIsAutoLoad($isAutoLoad) {
		L::$_isAutoLoad = $isAutoLoad;
	}

}