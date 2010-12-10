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
	private static $_apps = array();
	private static $_current = '';
	private static $_systemConfig = null;
	
	static public function init() {
		self::_initConfig();
		self::_initBaseLib();
		self::_initErrorHandle();
	}
	
	/**
	 * 初始化框架上下文
	 * 1. 策略加载框架必须的基础类库
	 */
	static public function application($current) {
		self::init();
		W::setCurrentApp($current);
		return new WindFrontController();
	}
	
	/**
	 * 获得应用相关配置信息
	 *
	 * @param string $name
	 * @return array
	 */
	static public function getApps($name = '') {
		if ($name && isset(W::$_apps[$name]))
			return W::$_apps[$name];
		elseif (W::$_current && isset(W::$_apps[W::$_current]))
			return W::$_apps[W::$_current];
		else
			return '';
	}
	
	/**
	 * @param string $name
	 * @param array $value
	 * @param boolean $default
	 */
	static public function setApps($name = '', $value = array(), $current = false) {
		if (empty($value)) return;
		W::$_apps[$name] = $value;
		if ($current) self::$_current = $name;
		L::register($name, $value['rootPath']);
	}
	
	/**
	 * 设置当前应用的名称
	 * 
	 * @param string $name
	 */
	static public function setCurrentApp($name) {
		if ($name) self::$_current = $name;
	}
	
	/**
	 * 获得当前应用名字
	 * @return string $name
	 */
	static public function getCurrentApp() {
		return self::$_current;
	}
	
	/**
	 * 自动加载框架底层类库
	 * 包括基础的抽象类和接口
	 */
	static private function _initBaseLib() {
		if (is_file(COMPILE_IMPORT_PATH) && !IS_DEBUG) {
			return include COMPILE_IMPORT_PATH;
		} else
			self::_initLoad();
	}
	
	/**
	 * 加载框架核心文件
	 * 如果开启了预加载编译缓存则将加载的文件保存到编译缓存中
	 */
	static private function _initLoad() {
		L::import('WIND:core.base.*');
		L::import('WIND:core.*');
		L::import('WIND:component.message.WindErrorMessage');
		if (self::ifCompile() && !IS_DEBUG) {
			L::import('WIND:utility.WindPack');
			$pack = L::getInstance('WindPack');
			$pack->packFromFile(L::getImports(), COMPILE_IMPORT_PATH, WindPack::STRIP_PHP, true);
		}
	}
	
	/**
	 * 是否支持预编译
	 * @return string
	 */
	static public function ifCompile() {
		return defined('COMPILE_PATH') && is_writable(COMPILE_PATH) ? true : false;
	}
	
	/**
	 * 解析配置文件
	 */
	static private function _initConfig() {
		W::setApps('WIND', array('name' => 'WIND', 'rootPath' => WIND_PATH));
		if (!is_file(COMPILE_PATH . '/config.php')) return false;
		$sysConfig = @include COMPILE_PATH . '/config.php';
		foreach ($sysConfig as $appName => $appConfig) {
			W::setApps($appName, $appConfig);
		}
	}
	
	static private function _initErrorHandle() {
		set_exception_handler(array('WindErrorHandle', 'exceptionHandle'));
		set_error_handler(array('WindErrorHandle', 'errorHandle'));
	}

}

/**
 * 文件加载类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
final class L {
	private static $_namespace = array();
	private static $_imports = array();
	private static $_instances = array();
	private static $_extensions = array('php', 'htm', 'class.php', 'db.php', 'phpx');
	
	static public function getImports($key = '') {
		return $key ? L::$_imports[$key] : L::$_imports;
	}
	
	/**
	 * 将路径信息注册到命名空间
	 *
	 * @param string $name
	 * @param string $path
	 */
	static public function register($name, $path) {
		$name = strtolower($name);
		if (!isset(L::$_namespace[$name])) {
			L::$_namespace[$name] = $path;
		}
	}
	
	/**
	 * @param array $class
	 */
	static public function setImports($class = array()) {
		foreach ($class as $key => $value) {
			if (!self::isImported()) {
				self::$_imports[$key] = $value;
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
	 * @return
	 */
	static public function import($filePath) {
		if (!$filePath || key_exists($filePath, L::$_imports) || in_array($filePath, L::$_imports)) {
			return $filePath;
		}
		list($fileName, $realPath, $ext, $isPackage) = self::getRealPath($filePath, true); //TODO
		if (!$realPath) {
			throw new Exception('import file ' . $filePath . ' is not exist.');
		}
		$fileNames = array();
		if (!$isPackage) {
			L::windInclude($realPath, $filePath, $fileName, $isPackage);
			return $realPath;
		}
		if (!$dh = opendir($realPath)) throw new Exception('the file ' . $realPath . ' open failed!');
		while (($file = readdir($dh)) !== false) {
			if ($file != "." && $file != ".." && !(is_dir($realPath . D_S . $file))) {
				if (($pos = strrpos($file, '.')) === false) $pos = strlen($file);
				$fileNames[] = array(substr($file, 0, $pos), substr($file, $pos + 1));
			}
		}
		closedir($dh);
		foreach ($fileNames as $var) {
			L::windInclude($realPath . D_S . $var[0] . '.' . $var[1], $filePath, $var[0], $isPackage);
		}
		return $realPath;
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
	static public function &getInstance($className, $args = array()) {
		$className = strtolower($className);
		if (!key_exists($className, L::$_instances)) L::_createInstance($className, $args);
		return L::$_instances[$className];
	}
	
	/**
	 * 清理全局变量
	 * 
	 * @param string $className
	 */
	static public function unsetInstance($className = '') {
		if ($className)
			unset(L::$_instances[$className]);
		else
			L::$_instances = array();
	}
	
	/**
	 * 解析路径信息，并返回路径的详情
	 * 返回array('isPackage','fileName','extension','realPath')
	 * @param string $filePath 路径信息
	 * @param boolean $info 是否返回路径详情
	 * @param string $ext 扩展名,如果不填该值，则自动在允许的扩展名列表中匹配
	 * @return string|array
	 */
	static public function getRealPath($filePath, $info = false, $ext = '', $dir = '') {
		$isPackage = false;
		$fileName = $namespace = '';
		if (is_dir($filePath)) {
			if (!$info) return realpath($filePath);
			$isPackage = true;
		} elseif (is_file($filePath)) {
			if (!$info) return realpath($filePath);
			$pathinfo = pathinfo($filePath);
			$filePath = $pathinfo['dirname'];
			$ext = $pathinfo['extension'];
			$fileName = $pathinfo['filename'];
			$isPackage = false;
		} else {
			if (key_exists($filePath, L::$_imports)) {
				return L::getRealPath(L::$_imports[$filePath], $info, $ext, $dir);
			}
			if (($pos = strrpos($filePath, '.')) === false) {
				$fileName = $filePath;
			} else {
				$fileName = substr($filePath, $pos + 1);
				$filePath = substr($filePath, 0, $pos);
			}
			if (($pos = strpos($filePath, ':')) !== false) {
				$namespace = substr($filePath, 0, $pos);
				$filePath = substr($filePath, $pos + 1);
			}
			if ($dir !== '')
				$filePath = $dir . D_S . str_replace('.', D_S, $filePath);
			else
				$filePath = L::_getAppRootPath($namespace) . D_S . str_replace('.', D_S, $filePath);
			$isPackage = $fileName === '*';
			if (!$isPackage && !$ext) {
				foreach ((array) L::_getExtension() as $key => $value) {
					if (file_exists($filePath . D_S . $fileName . '.' . $value)) {
						$ext = $value;
						break;
					}
				}
			}
		}
		$realpath = !$isPackage ? $filePath . D_S . $fileName . '.' . $ext : $filePath;
		if ($info) return array($fileName, realpath($realpath), $ext, $isPackage);
		return realpath($realpath);
	}
	
	/**
	 * 根据类名称创建类的单例对象，并保存到静态对象中
	 * 同时调用清理单例对象的策略
	 *
	 * @param string $className 类名称
	 * @param array $args 参数数组
	 * @return void|string
	 */
	static private function _createInstance($className, $args) {
		$class = new ReflectionClass($className);
		if ($class->isAbstract() || $class->isInterface()) return;
		if (!is_array($args)) $args = array($args);
		$object = call_user_func_array(array($class, 'newInstance'), $args);
		L::$_instances[$className] = & $object;
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
		if (!file_exists($realPath)) return;
		if (in_array($realPath, L::$_imports)) return $realPath;
		include $realPath;
		
		if ($ispackage) {
			$filePath = str_replace('*', $fileName, $filePath);
			self::$_imports[$filePath] = $realPath;
		} else
			self::$_imports[$filePath] = $realPath;
		return $realPath;
	}
	
	/**
	 * @param string $key
	 */
	private static function isImported($realPath) {
		return key_exists($realPath, L::$_imports) || in_array($realPath, L::$_imports);
	}
	
	/**
	 * 获得所有支持的扩展名
	 *
	 * @return array
	 */
	static private function _getExtension() {
		return L::$_extensions;
	}
	
	/**
	 * 获得跟路径信息
	 * @return string
	 */
	static private function _getAppRootPath($namespace = '') {
		$namespace = strtolower($namespace);
		if ($namespace && isset(L::$_namespace[$namespace])) {
			return L::$_namespace[$namespace];
		} else {
			return W::getCurrentApp() ? L::$_namespace[strtolower(W::getCurrentApp())] : '';
		}
	}
}

/**
 * 全文配置访问
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
final class C {
	private static $config = array();
	private static $c;
	/**
	 * 初始化配置文件对象
	 * @param array $configSystem
	 */
	static public function init($configSystem) {
		if (empty($configSystem)) {
			throw new Exception('system config file is not exists.');
		}
		self::$config = $configSystem;
		self::$c = new C();
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
		return self::getConfig(IWindConfig::ERRORMESSAGE, $name);
	}
	
	
	/**
	 * @param unknown_type $name
	 * @return Ambigous <string, multitype:, unknown>
	 */
	static public function getDataBase($name = '') {
		return self::getConfig(IWindDbConfig::DATABASE, $name);
	}
	
	static public function getDataBaseConnection($name = ''){
		return ($drivers = self::getDataBase(IWindDbConfig::CONNECTIONS)) ? $name ? $drivers[$name] : $drivers : '';
	}
	static public function getDataBaseDriver($name=''){
		return ($drivers = self::getDataBase(IWindDbConfig::DRIVERS)) ? $name ? $drivers[$name] : $drivers : '';
	}
	
	static public function getDataBaseBuilDer($name = ''){
		return ($drivers = self::getDataBase(IWindDbConfig::BUILDERS)) ? $name ? $drivers[$name] : $drivers : '';
		
	}
	
	
}
