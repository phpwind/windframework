<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
!defined('VERSION') && define('VERSION', '1.0.2');
!defined('IS_DEBUG') && define('IS_DEBUG', true);

/* 路径相关配置信息  */
!defined('D_S') && define('D_S', DIRECTORY_SEPARATOR);
!defined('WIND_PATH') && define('WIND_PATH', dirname(__FILE__) . D_S);
!defined('COMPILE_PATH') && define('COMPILE_PATH', WIND_PATH . 'compile' . D_S);
!defined('COMPILE_IMPORT_PATH') && define('COMPILE_IMPORT_PATH', COMPILE_PATH . 'preload_' . VERSION . '.php');

/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 */
class W {
	private static $_current = '';
	
	static public function init() {
		L::register(WIND_PATH, 'wind');
		self::initBaseLib();
		self::initErrorHandle();
	}
	
	/**
	 * 初始化框架上下文
	 * 1. 策略加载框架必须的基础类库
	 */
	static public function application($currentName, $config = '', $type = 'web') {
		self::init();
		$config = self::initConfig($currentName, $config);
		$frontController = new WindFrontController();
		return new WindFrontController();
	}
	
	/**
	 * 获得当前应用名字
	 * @return string $name
	 */
	static public function getCurrentApp() {
		return self::$_current;
	}
	
	/**
	 * 是否支持预编译
	 * @return string
	 */
	static public function ifCompile() {
		return defined('COMPILE_PATH') ? true : false;
	}
	
	static public function getCurrent() {
		return self::$_current;
	}
	
	/**
	 * 自动加载框架底层类库
	 * 包括基础的抽象类和接口
	 */
	static private function initBaseLib() {
		if (!IS_DEBUG && is_file(COMPILE_IMPORT_PATH)) {
			return include COMPILE_IMPORT_PATH;
		} else
			self::initLoad();
	}
	
	/**
	 * 加载框架核心文件
	 * 如果开启了预加载编译缓存则将加载的文件保存到编译缓存中
	 */
	static private function initLoad() {
		L::import('WIND:core.base.*');
		L::import('WIND:core.router.*');
		L::import('WIND:core.exception.*');
		L::import('WIND:core.filter.*');
		L::import('WIND:core.viewer.*');
		L::import('WIND:core.*');
		if (self::ifCompile() && !IS_DEBUG) {
			L::import('WIND:component.WindPack');
			$pack = L::getInstance('WindPack');
			$pack->packFromFile(L::getImports(), COMPILE_IMPORT_PATH, WindPack::STRIP_PHP, true);
		}
	}
	
	/**
	 * 初始化配置信息
	 */
	static private function initConfig($currentName, $config = '') {
		if (!is_array($config)) {
			L::import('WIND:component.config.WindConfigParser');
			$configParser = new WindConfigParser();
			$config = $configParser->parser($currentName, $config);
		}
		C::init($config);
		L::register($config['rootPath'],$currentName);
		return $config;
	}
	
	/**
	 * @param appConfig
	 */
	static private function registerApplications() {
		$appConfigPath = WIND_PATH . '/app_config.php';
		if (file_exists($appConfigPath)) {
			$appConfig = include $appConfigPath;
			foreach ($appConfig as $appName => $appConfig) {
				L::register($appName, $appConfig['rootPath']);
			}
		}
	}
	
	static private function initErrorHandle() {/*set_exception_handler(array('WindErrorHandle', 'exceptionHandle'));
		set_error_handler(array('WindErrorHandle', 'errorHandle'));*/
}

}

/**
 * 文件加载类
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
	
	static public function getImports($key = '') {
		return $key ? self::$_imports[$key] : self::$_imports;
	}
	
	/**
	 * @param array $class
	 */
	static public function setImports($class = array()) {
		foreach ((array) $class as $key => $value) {
			if (!self::isImported($key)) self::$_imports[$key] = $value;
		}
	}
	
	/**
	 * 将路径信息注册到命名空间
	 *
	 * @param string $name
	 * @param string $path
	 */
	static public function register($path, $name = '', $includePath = false) {
		if (empty(self::$_includePaths) || self::$_includePaths === null) {
			self::$_includePaths = array_unique(explode(PATH_SEPARATOR, get_include_path()));
			if (($pos = array_search('.', self::$_includePaths, true)) !== false) unset(self::$_includePaths[$pos]);
		}
		array_unshift(self::$_includePaths, $path);
		if (set_include_path('.' . PATH_SEPARATOR . implode(PATH_SEPARATOR, self::$_includePaths)) === false) {
			throw new Exception('set include path error.');
		}
		if ($name !== '') {
			$name = strtolower($name);
			if (!isset(self::$_namespace[$name]) && $path) {
				self::$_namespace[$name] = $path;
			}
		}
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
	 * @param string $filePath //文件路径信息
	 * @author Qiong Wu
	 * @return string|null
	 */
	static public function import($filePath, $includes = false) {
		if (!$filePath) return false;
		if (key_exists($filePath, self::$_imports)) {
			return self::$_imports[$filePath];
		}
		if (($pos = strrpos($filePath, '.')) === false)
			$fileName = $filePath;
		else
			$fileName = substr($filePath, $pos + 1);
		$isPackage = $fileName === '*';
		if (!$isPackage) {
			if (!in_array($fileName, self::$_imports)) {
				self::$_imports[$filePath] = $fileName;
				if ($includes)
					self::autoLoad($fileName, $filePath);
				else
					self::$_classes[$fileName] = $filePath;
				return $fileName;
			}
		} elseif ($isPackage) {
			$filePath = substr($filePath, 0, $pos);
			$dirPath = self::getRealPath($filePath);
			if (!$dh = opendir($dirPath)) throw new Exception('the file ' . $dirPath . ' open failed!');
			while (($file = readdir($dh)) !== false) {
				if ($file != "." && $file != ".." && !(is_dir($dirPath . D_S . $file))) {
					if (($pos = strrpos($file, '.')) === false)
						$fileName = $file;
					else
						$fileName = substr($file, 0, $pos);
					$_filePath = $filePath . '.' . $fileName;
					self::$_imports[$_filePath] = $fileName;
					if ($includes)
						self::autoLoad($fileName, $_filePath);
					else
						self::$_classes[$fileName] = $_filePath;
				}
			}
			closedir($dh);
		}
		return true;
	}
	
	static public function autoLoad($className, $path = '') {
		if (!isset(self::$_classes[$className])) throw new Exception('auto load ' . $className . ' failed.');
		if ($path === '') $path = self::getRealPath(self::$_classes[$className]) . '.' . 'php';
		if (is_file($path))
			include $path;
		else
			throw new Exception('auto load ' . $path . ' failed.');
	}
	
	/**
	 * 获得一个类的静态单例对象
	 * 全局的静态单例对象以数组的形式保存在 < self::$_instances >中，索引为类名称
	 * 类名称必须和文件名称相同，否则将抛出异常
	 * 支持构造函数参数
	 * 返回一个对象的引用
	 *
	 * @param string $className
	 * @retur Object
	 */
	static public function getInstance($className, $args = array(), $nameSpace = '') {
		$className = strtolower($className);
		$app = W::getCurrentApp() ? W::getCurrentApp() : 'default';
		$nameSpace = $nameSpace === '' ? $className : $className . '_' . $nameSpace;
		if (!isset(self::$_instances[$app])) self::$_instances[$app] = array();
		if (!key_exists($nameSpace, self::$_instances[$app])) {
			self::$_instances[$app][$nameSpace] = self::createInstance($className, $args);
		}
		return self::$_instances[$app][$nameSpace];
	}
	
	/**
	 * 清理全局变量
	 *
	 * @param string $className
	 */
	static public function unsetInstance($className = '') {
		if ($className)
			unset(self::$_instances[$className]);
		else
			self::$_instances = array();
	}
	
	/**
	 * 解析路径信息，并返回路径的详情
	 * 返回array('isPackage','fileName','extension','realPath')
	 * @param string $filePath 路径信息
	 * @param boolean $info 是否返回路径详情
	 * @param string $ext 扩展名,如果不填该值，则自动在允许的扩展名列表中匹配
	 * @return string|array
	 */
	static public function getRealPath($filePath) {
		$namespace = '';
		if (($pos = strpos($filePath, ':')) !== false) {
			$namespace = substr($filePath, 0, $pos);
			$filePath = substr($filePath, $pos + 1);
		}
		$filePath = str_replace('.', D_S, $filePath);
		if ($namespace) $filePath = rtrim(self::getRootPath($namespace), D_S) . D_S . $filePath;
		return $filePath;
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
	
	/**
	 * 全局包含文件的唯一入口
	 *
	 * @param string $realPath 绝对路径名
	 * @param string $filePath 输入的路径名
	 * @param string $fileName 文件名称
	 * @return string
	 */
	static private function windInclude($realPath, $filePath, $fileName, $ispackage = false) {
		if (in_array($fileName, self::$_imports)) return $realPath;
		include $realPath;
		if ($ispackage) $filePath = str_replace('*', $fileName, $filePath);
		self::$_imports[$fileName] = $filePath;
		return $realPath;
	}
	
	/**
	 * @param string $key
	 */
	private static function isImported($path) {
		return key_exists($path, self::$_imports) || in_array($path, self::$_imports);
	}
	
	/**
	 * 获得所有支持的扩展名
	 *
	 * @return array
	 */
	static private function getExtension() {
		return self::$_extensions;
	}
	
	/**
	 * 获得跟路径信息
	 * @return string
	 */
	static private function getRootPath($namespace = '') {
		if ($namespace === '') $namespace = W::getCurrentApp();
		$namespace = strtolower($namespace);
		return isset(self::$_namespace[$namespace]) ? self::$_namespace[$namespace] : '';
	}
}

function __autoload($className) {
	L::autoLoad($className);
}

L::import('WIND:component.config.base.IWindConfig');
/**
 * 全文配置访问
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class C {
	private static $config = array();
	/**
	 * 初始化配置文件对象
	 * @param array $configSystem
	 */
	static public function init($configSystem) {
		if (empty($configSystem)) {
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
