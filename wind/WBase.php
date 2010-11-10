<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
//error_reporting(E_ERROR | E_PARSE);


/* 路径相关配置信息  */
defined('WIND_PATH') or define('WIND_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
defined('SYSTEM_CONFIG_PATH') or define('SYSTEM_CONFIG_PATH', WIND_PATH);

/* 扩展名 */
defined('EXT') or define('EXT', 'php');

/* import */
defined('IMPORT_SEPARATOR') or define('IMPORT_SEPARATOR', '.');
defined('IMPORT_PACKAGE') or define('IMPORT_PACKAGE', '*');

defined('RUNTIME_START') or define('RUNTIME_START', microtime(true));

defined('USEMEM_START') or define('USEMEM_START', memory_get_usage());

defined('LOG_PATH') or define('LOG_PATH', WIND_PATH . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR);

defined('LOG_DISPLAY_TYPE') or define('LOG_DISPLAY_TYPE', 'log');

//defined('LOG_RECORD') or define('LOG_RECORD', true);

//defined('DEBUG') or define('DEBUG', true);

/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 */
class W {
	
	/* 已经被include过的类或者包 */
	static $_included = array();
	
	/* 已经被实例化过的对象集合 */
	static $_instances = array();
	
	static $_vars = array();
	
	static $_system_config = 'config';
	
	/**
	 * 初始化框架上下文
	 * 1. 策略加载框架必须的基础类库
	 * 
	 */
	static public function init() {
		self::_autoIncludeBaseLib();
		set_exception_handler(array(
			'W', 
			'WExceptionHandler'
		));
		defined('LOG_RECORD') && W::import('utility.WLog');
		defined('DEBUG') && W::import('utility.WDebug');
	}
	
	static public function getSystemConfig() {
		return self::getInstance('WSystemConfig');
	}
	
	/**
	 * 获得文件的绝对路径
	 * @param string $path
	 */
	static public function getRealPath($path = '', $root = '', $is_dir = false) {
		if (file_exists($path) && $root == '')
			return realpath($path);
		if ($root == '' || !realpath($root))
			$root = self::getFrameWorkPath() . $root . self::getSeparator();
		
		$realPath = $root . $path;
		$realPath = str_replace(IMPORT_SEPARATOR, self::getSeparator(), $realPath);
		if (!$is_dir)
			$realPath .= '.' . self::getExtendName();
		return realpath($realPath);
	}
	
	static public function getVar($name) {
		return self::$_vars[$name];
	}
	
	static public function setVar($name, $value) {
		if (!isset(self::$_vars[$name]))
			self::$_vars[$name] = $value;
	}
	
	/**
	 * 获得框架跟路径
	 * @return string
	 */
	static public function getFrameWorkPath() {
		return WIND_PATH;
	}
	
	/**
	 * 获得系统配置文件路径信息
	 * @return string
	 */
	static public function getSystemConfigPath() {
		return self::getRealPath(W::$_system_config, SYSTEM_CONFIG_PATH, false);
	}
	
	/**
	 * 获得文件路径分隔符
	 * @return string
	 */
	static public function getSeparator() {
		return DIRECTORY_SEPARATOR;
	}
	
	/**
	 * 获得文件扩展名
	 * @return string
	 */
	static public function getExtendName() {
		return EXT;
	}
	
	/**
	 * 获得支持加载的扩展名数组
	 * 判断扩展名是否支持
	 * @param string $ext
	 * @return boolean|multitype:string 
	 */
	static public function getExtendNames($ext = '') {
		$exts = array();
		if ($ext)
			return in_array($ext, $exts);
		return $exts;
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
	static public function getInstance($className, $args = array()) {
		if (!key_exists($className, self::$_instances))
			self::_createInstance($className, $args);
		return self::$_instances[$className];
	}
	
	/**
	 * 加载一个类或者加载一个包
	 * 以框架路径为跟路径进行加载
	 * 加载一个类的参数方式：'core.WFrontController.php'
	 * 加载一个包的参数方式：'core.*'
	 *
	 * 如果加载的类是继承了上下文类 WContext
	 * 那么加载类的同时会生成该类的静态单利对象
	 * 用户可以通过getInstance()方法获得该对象
	 *
	 * @param string $classPath
	 * @author Qiong Wu
	 * @return void
	 */
	static public function import($filePath, $instance = false) {
		if (!isset($filePath))
			throw new Exception('is not right path');
		
		if (file_exists($filePath)) {
			self::_include($filePath);
			return;
		}
		
		if (($pos = strrpos($filePath, '.')) === false) {
			self::_include($filePath, '', $instance);
			return;
		}
		
		$filePath = str_replace(self::getSeparator(), IMPORT_SEPARATOR, $filePath);
		$filePath = trim($filePath, ' ' . IMPORT_SEPARATOR);
		
		$className = (string) substr($filePath, $pos + 1);
		$filePath = (string) substr($filePath, 0, $pos);
		$isPackage = $className === IMPORT_PACKAGE;
		$dir = self::getFrameWorkPath();
		$classNames = array();
		if ($isPackage) {
			$dir = self::getRealPath(substr($filePath, 0, $pos), '', true);
			if (!is_dir($dir))
				throw new Exception('the file path ' . $dir . ' is not exists!!');
			
			if (!$dh = opendir($dir))
				throw new Exception('the file ' . $dir . ' open failed!');
			
			while (($file = readdir($dh)) !== false) {
				if ($file != "." && $file != ".." && !(is_dir($dir . self::getSeparator() . $file))) {
					$pos = strrpos($file, '.');
					if (($pos = strrpos($file, '.')) !== false && substr($file, $pos + 1) == self::getExtendName())
						$classNames[] = substr($file, 0, $pos);
				}
			}
			closedir($dh);
		} else
			$classNames[] = $className;
		
		foreach ($classNames as $className) {
			self::_include($className, $filePath, $instance);
		}
		return;
	}
	
	/**
	 * 全局包含文件的唯一入口
	 * @param string $className 类名称/文件名
	 * @param string $classPath 类路径/文件路径
	 * @return string
	 */
	static private function _include($fileName, $filePath = '', $instance = false) {
		if (empty($fileName)) {return;}
		$realPath = self::getRealPath($fileName, $filePath);
		if (!file_exists($realPath))
			throw new Exception('file ' . $realPath . ' is not exists');
		
		if (key_exists($fileName, self::$_included))
			return $realPath;
		
		if (!is_dir($realPath) && $realPath)
			include $realPath;
		
		$var = get_defined_vars();
		if (count($var) > 3)
			self::$_vars += array_splice($var, 3);
		
		self::$_included[$fileName] = $realPath;
		$instance && self::getInstance($fileName);
		return $realPath;
	}
	
	/**
	 * 根据类名称创建类的单例对象，并保存到静态对象中
	 * 同时调用清理单例对象的策略
	 * 
	 * @param string $className 类名称
	 * @return void|string
	 */
	static private function _createInstance($className, $args) {
		if (key_exists($className, self::$_instances))
			return;
		$class = new ReflectionClass($className);
		if ($class->isAbstract() || $class->isInterface())
			return;
		
		if (!is_array($args))
			$args = array();
		$object = call_user_func_array(array(
			$class, 
			'newInstance'
		), $args);
		
		/*if (in_array('WContext', (array) class_implements($className))) {
			$class->setStaticPropertyValue('instance', & $object);
			$scope = $class->getStaticPropertyValue('scope', 'request');
			//TODO 变量作用域设置
		}*/
		self::$_instances[$className] = & $object;
	
	}
	
	/**
	 * 自动加载框架底层类库
	 * 包括基础的抽象类和接口
	 */
	static private function _autoIncludeBaseLib() {
		self::import('exception.WException');
		self::import('base.WModule');
		self::import('base.*');
		self::import('core.*');
	}
	
	/**
	 * 初始化系统配置信息
	 * 
	 */
	static private function _initSystemConfig($config) {
		$systemConfigPath = self::getSystemConfigPath();
		$systemConfig = self::$_system_config;
		$realPath = self::getRealPath($systemConfig, false, $systemConfigPath);
		if (!file_exists($realPath))
			throw new Exception('SYS Excetion ：配置文件不存在!!!');
		self::import($realPath);
		self::getSystemConfig()->parse($systemConfig, $config);
	}
	
	/**
	 * 异常、调试及其它信息记录到日志
	 * @param $message
	 * @param $trace
	 */
	static public function recordLog($message, $type = 'INFO', $ifrecord = 'add') {
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
		return defined('DEBUG') ? WDebug::debug($message, $trace) : $message;
	}
	
	static public function WExceptionHandler($e) {
		$trace = is_a($e, 'WException') ? $e->getStackTrace() : $e->getTrace();
		$message = W::debug("{$e}", $trace);
		W::recordLog($message, 'TRACE', 'log');
		die($message);
	}

}

W::init();
