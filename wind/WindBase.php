<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
!defined('VERSION') && define('VERSION', '0.1');
!defined('IS_DEBUG') && define('IS_DEBUG', true);

/* 路径相关配置信息  */
!defined('D_S') && define('D_S', DIRECTORY_SEPARATOR);
!defined('WIND_PATH') && define('WIND_PATH', dirname(__FILE__) . D_S);
!defined('COMPILE_PATH') && define('COMPILE_PATH', WIND_PATH . 'compile' . D_S);
!defined('COMPILE_IMPORT_PATH') && define('COMPILE_IMPORT_PATH', COMPILE_PATH . 'wind_v.' . VERSION . '.php');

/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 */
class W {
	
	static public function init() {
		self::checkEnvironment();
		self::systemRegister();
		self::loadBaseLib();
		self::initErrorHandle();
	}
	
	/**
	 * 初始化框架上下文
	 * 1. 策略加载框架必须的基础类库
	 */
	static public function application($currentName, $config = '') {
		self::init();
		$config = self::initConfig($currentName, $config);
		$frontController = new WindFrontController($currentName, $config);
		return $frontController;
	}
	
	/**
	 * 是否支持预编译
	 * @return string
	 */
	static public function ifCompile() {
		return defined('COMPILE_PATH') ? true : false;
	}
	
	/**
	 * 自动加载框架底层类库
	 * 包括基础的抽象类和接口
	 */
	static private function loadBaseLib() {
		if (!IS_DEBUG && is_file(COMPILE_IMPORT_PATH)) {
			return include COMPILE_IMPORT_PATH;
		} else
			L::loadCoreLibrary();
	}
	
	/**
	 * 初始化配置信息
	 */
	static private function initConfig($currentName, $config = '') {
		if (!is_array($config)) {
			L::import('COM:config.WindConfigParser');
			$configParser = new WindConfigParser();
			$config = $configParser->parse($currentName, $config, true);
		}
		C::init($config);
		L::register(C::getRootPath(), $currentName);
		return $config;
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
	 * 环境检查
	 */
	static private function checkEnvironment() {

	}
	
	/**
	 * 注册自动加载器
	 * 系统信息注册
	 */
	static private function systemRegister() {
		L::registerAutoloader();
		L::register(WIND_PATH, 'WIND');
		L::register(WIND_PATH . 'component' . D_S, 'COM');
		//self::registerApplications();
	}
	
	/**
	 * 初始化错误处理
	 */
	static private function initErrorHandle() {
		if (IS_DEBUG) return;
		set_exception_handler(array('WindErrorHandle', 'exceptionHandle'));
		set_error_handler(array('WindErrorHandle', 'errorHandle'));
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
			$dirPath = self::getRealPath($filePath);
			if (!$dh = opendir($dirPath)) throw new Exception('the file ' . $dirPath . ' open failed!');
			while (($file = readdir($dh)) !== false) {
				if (is_dir($dirPath . D_S . $file)) {
					if ($recursivePackage && $file !== '.' && $file !== '..' && (strpos($file, '.') !== 0)) {
						$_filePath = $filePath . '.' . $file . '.' . '*';
						self::import($_filePath, $autoInclude, $recursivePackage);
					}
				} else {
					if (($pos = strrpos($file, '.')) === false)
						$fileName = $file;
					else
						$fileName = substr($file, 0, $pos);
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
		if ($path === '') throw new Exception('auto load ' . $className . ' failed.');
		$path = self::getRealPath($path, self::$_extensions);
		if ((@include $path) === false) throw new Exception('include file ' . $path . ' failed.');
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
		} elseif (!function_exists('__autoload')) {
			function __autoload($className) {
				L::autoLoad($className);
			}
		} else {
			self::$_isAutoLoad = false;
		}
	}
	
	/**
	 * 获得一个类的静态单例对象
	 * 全局的静态单例对象以数组的形式保存在 < self::$_instances >中，索引为类名称
	 * 类名称必须和文件名称相同，否则将抛出异常
	 * 支持构造函数参数
	 * 返回一个对象的引用
	 *
	 * @param string $className
	 * @return Object
	 */
	static public function getInstance($className, $args = array(), $nameSpace = '') {
		$className = strtolower($className);
		$nameSpace = $nameSpace === '' ? $className : $className . '_' . $nameSpace;
		if (!key_exists($nameSpace, self::$_instances)) {
			self::$_instances[$nameSpace] = self::createInstance($className, $args);
		}
		return self::$_instances[$nameSpace];
	}
	
	/**
	 * 解析路径信息，并返回路径的详情
	 * 
	 * @param string $filePath 路径信息
	 * @param boolean $info 是否返回路径详情
	 * @param string $ext 扩展名,如果不填该值，则自动在允许的扩展名列表中匹配
	 * @return string|array('isPackage','fileName','extension','realPath')
	 */
	static public function getRealPath($filePath, $ext = '') {
		$namespace = '';
		if (($pos = strpos($filePath, ':')) !== false) {
			$namespace = substr($filePath, 0, $pos);
			$filePath = substr($filePath, $pos + 1);
		}
		$filePath = str_replace('.', D_S, $filePath);
		if ($namespace) $filePath = rtrim(self::getRootPath($namespace), D_S) . D_S . $filePath;
		if ($ext) $filePath .= '.' . $ext;
		return $filePath;
	}
	
	/**
	 * 加载框架核心库文件
	 * 
	 * @return
	 */
	static public function loadCoreLibrary() {
		self::import('WIND:core.*', true, true);
		self::import('COM:config.*', true, true);
		if (!IS_DEBUG && W::ifCompile()) self::perLoadCoreLibrary();
	}
	
	/**
	 * 预加载处理回调处理，注入内容到打包文件头部
	 * 
	 * @param array $classes 
	 * @return string
	 */
	static public function perLoadInjection($classes = array()) {
		if (!empty($classes)) {
			foreach ($classes as $key => $value) {
				if (!self::isImported($key)) self::$_imports[$key] = $value;
			}
		} else {
			L::import('COM:format.WindString');
			return "L::perLoadInjection(" . WindString::varExport(L::getImports()) . ");";
		}
	}
	
	/**
	 * 根据类名称创建类的单例对象，并保存到静态对象中
	 * 同时调用清理单例对象的策略
	 * 
	 * @param string $className 类名称
	 * @param array $args 参数数组
	 * @return void|string
	 */
	static private function createInstance($className, $args) {
		$class = new ReflectionClass($className);
		if ($class->isAbstract() || $class->isInterface()) return;
		if (!is_array($args)) $args = array($args);
		$object = call_user_func_array(array($class, 'newInstance'), $args);
		return $object;
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
		if ($namespace === '') $namespace = W::getCurrentApp();
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
		if (empty(self::$_includePaths) || self::$_includePaths === null) {
			self::$_includePaths = array_unique(explode(PATH_SEPARATOR, get_include_path()));
			if (($pos = array_search('.', self::$_includePaths, true)) !== false) unset(self::$_includePaths[$pos]);
		}
		array_unshift(self::$_includePaths, $path);
		if (set_include_path('.' . PATH_SEPARATOR . implode(PATH_SEPARATOR, self::$_includePaths)) === false) {
			throw new Exception('set include path error.');
		}
	}
	
	static private function perLoadCoreLibrary() {
		self::import('COM:WindPack');
		$pack = new WindPack();
		$pack->setContentInjectionCallBack(array('L', 'perLoadInjection'));
		$fileList = array();
		foreach (L::getImports() as $key => $value) {
			$key = self::getRealPath($key, self::$_extensions);
			$fileList[$key] = $value;
		}
		$pack->packFromFileList($fileList, COMPILE_IMPORT_PATH, WindPack::STRIP_PHP, true);
	}
	
	/**
	 * @param $_isAutoLoad the $_isAutoLoad to set
	 * @author Qiong Wu
	 */
	public static function setIsAutoLoad($isAutoLoad) {
		L::$_isAutoLoad = $isAutoLoad;
	}

}

/**
 * 全文配置访问
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 * @deprecated
 */
class C {
	private static $config = array();
	/**
	 * 初始化配置文件对象
	 * @param array $configSystem
	 */
	static public function init($configSystem) {
		if (empty($configSystem) || !is_array($configSystem)) {
			throw new Exception('system config file is not exists.');
		}
		self::$config = $configSystem;
	}
	
	/**
	 * 根据配置名取得相应的配置
	 * @param string $configName
	 * @param string $subConfigName
	 * @return string
	 */
	static public function getConfig($configName = '', $subConfigName = '') {
		if (!$configName) return self::$config;
		$_config = array();
		if (isset(self::$config[$configName])) {
			$_config = self::$config[$configName];
		}
		if (!$subConfigName) return $_config;
		$_subConfig = array();
		if (is_array($_config) && isset($_config[$subConfigName])) {
			$_subConfig = $_config[$subConfigName];
		}
		return $_subConfig;
	}
	
	static public function getRootPath() {
		if (!($rootPath = self::getConfig(IWindConfig::ROOTPATH))) {
			$rootPath = dirname($_SERVER['SCRIPT_FILENAME']);
		}
		return $rootPath;
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getModules($name = '') {
		return self::getConfig(IWindConfig::MODULES, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getTemplate($name = '') {
		return self::getConfig(IWindConfig::TEMPLATE, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getFilters($name = '') {
		return self::getConfig(IWindConfig::FILTERS, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getViewerResolvers($name = '') {
		return self::getConfig(IWindConfig::VIEWER_RESOLVERS, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getRouter($name = '') {
		return self::getConfig(IWindConfig::ROUTER, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getRouterParsers($name = '') {
		return self::getConfig(IWindConfig::ROUTER_PARSERS, $name);
	}
	
	/**
	 * @param string $name
	 * @return Ambigous <string, multitype:, unknown>
	 */
	static public function getApplications($name = '') {
		return self::getConfig(IWindConfig::APPLICATIONS, $name);
	}
	
	/**
	 * @param string $name
	 * @return Ambigous <string, multitype:, unknown>
	 */
	static public function getErrorMessage($name = '') {
		return self::getConfig(IWindConfig::ERROR, $name);
	}
	
	/**
	 * @param unknown_type $name
	 * @return Ambigous <string, multitype:, unknown>
	 */
	static public function getDataBase($name = '') {
		return self::getConfig(IWindDbConfig::DATABASE, $name);
	}
	
	static public function getDataBaseConnection($name = '') {
		return ($drivers = self::getDataBase(IWindDbConfig::CONNECTIONS)) ? $name ? $drivers[$name] : $drivers : '';
	}
	static public function getDataBaseDriver($name = '') {
		return ($drivers = self::getDataBase(IWindDbConfig::DRIVERS)) ? $name ? $drivers[$name] : $drivers : '';
	}
	
	static public function getDataBaseBuilDer($name = '') {
		return ($drivers = self::getDataBase(IWindDbConfig::BUILDERS)) ? $name ? $drivers[$name] : $drivers : '';
	
	}

}
