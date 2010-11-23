<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
//error_reporting(E_ERROR | E_PARSE);


/* 路径相关配置信息  */
!defined('WIND_PATH') && define('WIND_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
!defined('SYSTEM_CONFIG_PATH') && define('SYSTEM_CONFIG_PATH', WIND_PATH . 'config.php');
!defined('D_S') && define('D_S', DIRECTORY_SEPARATOR);

define('RUNTIME_START', microtime(true));
define('USEMEM_START', memory_get_usage());
define('LOG_PATH', WIND_PATH . 'log' . DIRECTORY_SEPARATOR);
define('LOG_DISPLAY_TYPE', 'log');

/*define('LOG_RECORD', true);
define('DEBUG', true);*/

/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 */
class W {
	private static $_apps = array();
	private static $_default = '';
	private static $_systemConfig = null;
	
	/**
	 * 初始化框架上下文
	 * 1. 策略加载框架必须的基础类库
	 */
	static public function init() {
		self::_initConfig();
		self::_initBaseLib();
		self::_initLog();
	}
	
	/**
	 * 返回系统配置信息
	 * 
	 * @return array
	 */
	static public function getSystemConfig() {
		if (W::$_systemConfig === null) {
			if (!file_exists(SYSTEM_CONFIG_PATH)) {
				throw new Exception('System config file ' . SYSTEM_CONFIG_PATH . ' is not exists!');
			}
			@include SYSTEM_CONFIG_PATH;
			$vars = get_defined_vars();
			W::$_systemConfig = (array) array_pop($vars);
		}
		return W::$_systemConfig;
	}
	
	/**
	 * 获得应用相关配置信息
	 * 
	 * @param string $name
	 * @return array
	 */
	static public function getApps($name = '') {
		return $name ? W::$_apps[$name] : W::$_apps[W::$_default];
	}
	
	/**
	 * @param string $name
	 * @param array $value
	 * @param boolean $default
	 */
	static public function setApps($name = '', $value = array(), $default = false) {
		W::$_apps[$name] = $value;
		if ($default) W::$_default = $name;
		L::register($name, $value['rootPath']);
	}
	
	/**
	 * 自动加载框架底层类库
	 * 包括基础的抽象类和接口
	 */
	static private function _initBaseLib() {
		/* 核心加载 */
		L::import('WIND:core.base.impl.*');
		L::import('WIND:core.base.*');
		L::import('WIND:core.*');
		
		L::import('WIND:component.exception.base.impl.*');
		L::import('WIND:component.exception.base.*');
		L::import('WIND:component.exception.WindException');
	}
	
	/**
	 * 解析配置文件
	 */
	static private function _initConfig() {
		W::setApps('WIND', array('rootPath' => WIND_PATH));
	}
	
	/**
	 * 初始化系统日志，调试系统
	 */
	static private function _initLog() {
		set_exception_handler(array('W', 'WExceptionHandler'));
		defined('LOG_RECORD') && W::import('utility.WLog');
		defined('DEBUG') && W::import('utility.WDebug');
	}
	
	/**
	 * 异常、调试及其它信息记录到日志
	 * @param $message
	 * @param $trace
	 */
	static public function recordLog($message, $type = 'INFO', $ifrecord = 'add') {
		//TODO 重构
		if (defined('LOG_RECORD')) {
			$message = str_replace('<br/>', "\r\n", $message);
			$ifrecord == 'add' ? WLog::add($message, strtoupper($type)) : WLog::log($message, strtoupper($type));
		}
	}
	
	/**
	 * 对于输出信息是否debug处理
	 * @param $message
	 * @param $trace
	 */
	static public function debug($message, $trace = array()) {
		//TODO 重构
		return defined('DEBUG') ? WDebug::debug($message, $trace) : $message;
	}
	
	static public function WExceptionHandler($e) {
		$trace = is_a($e, 'WindException') ? $e->getStackTrace() : $e->getTrace();
		$message = W::debug("{$e}", $trace);
		W::recordLog($message, 'TRACE', 'log');
		die($message);
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
		if (!isset(L::$_namespace[$name])) {
			L::$_namespace[$name] = $path;
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
		if (!$filePath) return null;
		if (file_exists($filePath)) {
			L::_include($filePath);
			return $filePath;
		}
		list($isPackage, $fileName, $ext, $realPath) = self::getRealPath($filePath, true);
		$fileNames = array();
		if (!$isPackage) {
			L::_include($realPath, $fileName);
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
			L::_include($realPath . D_S . $var[0] . '.' . $var[1], $var[0]);
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
		if (!key_exists($className, L::$_instances)) L::_createInstance($className, $args);
		return L::$_instances[$className];
	}
	
	/**
	 * 解析路径信息，并返回路径的详情
	 * 返回array('isPackage','fileName','extension','realPath')
	 * @param string $filePath 路径信息
	 * @param boolean $info 是否返回路径详情
	 * @param string $ext 扩展名,如果不填该值，则自动在允许的扩展名列表中匹配
	 * @return string|array
	 */
	static public function getRealPath($filePath, $info = false, $ext = '') {
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
			$fileName = basename($filePath, '.' . $ext);
			$isPackage = false;
		} elseif (!is_file($filePath) && !is_dir($filePath)) {
			if (($pos = strrpos($filePath, '.')) === false) {
				$fileName = $filePath;
			} else {
				$fileName = (string) substr($filePath, $pos + 1);
				$filePath = (string) substr($filePath, 0, $pos);
			}
			if (($pos = strpos($filePath, ':')) !== false) {
				$namespace = (string) substr($filePath, 0, $pos);
				$filePath = (string) substr($filePath, $pos + 1);
			}
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
		if ($info) return array($isPackage, $fileName, $ext, realpath($realpath));
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
	 * @param string $className 类名称/文件名
	 * @param string $classPath 类路径/文件路径
	 * @return string
	 */
	static private function _include($realPath, $fileName = '') {
		if (empty($realPath)) return;
		if (!file_exists($realPath)) throw new Exception('file ' . $realPath . ' is not exists');
		if (key_exists($fileName, self::$_imports)) return $realPath;
		include $realPath;
		$fileName && self::$_imports[$fileName] = $realPath;
		return $realPath;
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
		if ($namespace && isset(L::$_namespace[$namespace])) return L::$_namespace[$namespace];
		$rp = W::getApps();
		return $rp['rootPath'];
	}
}

W::init();