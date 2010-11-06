<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

/* 路径相关配置信息  */
defined('WIND_PATH') or define('WIND_PATH', dirname(__FILE__));
defined('SYSTEM_CONFIG_PATH') or define('SYSTEM_CONFIG_PATH', WIND_PATH);

/* 扩展名 */
defined('EXT') or define('EXT', 'php');

defined('RUNTIME_START') or define('RUNTIME_START', microtime(true));

defined('USEMEM_START') or define('USEMEM_START', memory_get_usage());

defined('LOG_PATH') or define('LOG_PATH', WIND_PATH . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR);

defined('LOG_DISPLAY_TYPE') or define('LOG_DISPLAY_TYPE', 'log');

defined('LOG_RECORD') or define('LOG_RECORD', true);

defined('DEBUG') or define('DEBUG', true);

/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 */
class W {
	
	/* 已经被include过的类或者包 */
	static $_included = array();
	
	/* 已经被实例化过的对象集合 */
	static $_instances = array();
	
	static $_instance_max = 0;
	static $_instance_frequence = 0;
	
	static $_system_config = 'config.php';
	
	/**
	 * 初始化框架上下文
	 * 1. 策略加载框架必须的基础类库
	 * 
	 */
	static public function init($config = NULL) {
		self::_autoIncludeBaseLib();
	
	}
	
	static public function getSystemConfig() {
		return self::getInstance('WSystemConfig');
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
		return SYSTEM_CONFIG_PATH;
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
	 * 获得一个类的静态单例对象
	 * 全局的静态单例对象以数组的形式保存在 < self::$_instances >中，索引为类名称
	 * 类名称必须和文件名称相同，否则将抛出异常
	 * 支持构造函数参数
	 * 返回一个对象的引用
	 * 
	 * @param string $className
	 * @retur Object
	 */
	static public function getInstance($className) {
		if (key_exists($className, self::$_instances))
			return self::$_instances[$className]['instance'];
		return NULL;
	}
	
	/**
	 * 加载一个类或者加载一个包
	 * 以框架路径为跟路径进行加载
	 * 加载一个类的参数方式：'core.WFrontController'
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
	static public function import($classPath) {
		$classPath = trim($classPath, ' .');
		if (!isset($classPath))
			throw new Exception(__CLASS__ . ' throw exception!!!!');
		if (($pos = strrpos($classPath, '.')) === false)
			return self::_include($classPath, $classPath);
		
		$className = (string) substr($classPath, $pos + 1);
		$isPackage = $className === '*';
		$classPath = str_replace('.', DIRECTORY_SEPARATOR, $classPath);
		$classNames = array();
		if ($isPackage) {
			$dir = (string) substr($classPath, 0, $pos);
			if (!is_dir($dir))
				return false;
			if (!$dh = opendir($dir))
				return false;
			while (($file = readdir($dh)) !== false) {
				if ($file != "." && $file != ".." && !(is_dir($dir . self::getSeparator() . $file))) {
					$pos = strrpos($file, '.');
					if ((string) substr($file, $pos + 1) == self::getExtendName()) {
						$classNames[] = (string) substr($file, 0, $pos);
					}
				}
			}
			closedir($dh);
		} else
			$classNames[] = $className;
		
		foreach ($classNames as $value) {
			self::_include($value, str_replace('*', $value, $classPath));
		}
		return true;
	}
	
	/**
	 * 全局包含文件的唯一入口
	 * @param string $className 类名称/文件名
	 * @param string $classPath 类路径/文件路径
	 * @return boolean
	 */
	static private function _include($className, $classPath) {
		$path = self::getFrameWorkPath() . self::getSeparator() . $classPath . '.' . self::getExtendName();
		if (!file_exists($path))
			return false;
		if (key_exists($className, self::$_included))
			return true;
		include $path;
		self::$_included[$className] = $classPath;
		self::_autoInstance($className);
		return true;
	}
	
	/**
	 * 自动的类实例化
	 * 将import进来的类进行自动实例化，自动实例化的类必须继承了WContext接口
	 * 自动实例化时自动加载其父类
	 * @param string $className
	 * @return void
	 */
	static private function _autoInstance($className) {
		if (in_array('WContext', (array) class_implements($className, true)))
			self::getInstance($className);
	}
	
	/**
	 * 根据类名称创建类的单例对象，并保存到静态对象中
	 * 同时调用清理单例对象的策略
	 * 
	 * @param string $className 类名称
	 * @return void|string
	 */
	static private function _createInstance($className) {
		if (key_exists($className, self::$_instances))
			return;
		$class = new ReflectionClass($className);
		if ($class->isAbstract() || $class->isInterface())
			return false;
		$args = func_get_args();
		unset($args[0]);
		$object = call_user_func_array(array(
			$class, 
			'newInstance'
		), $args);
		self::$_instances[$className]['instance'] = & $object;
		if (self::$_instance_frequence)
			self::_cleanInstanceByFrequence($className);
		if (self::$_instance_max)
			self::_cleanInstancesByMax();
	}
	
	/**
	 * 全局静态类加载策略 - 根据存储长度来清理
	 * @return string
	 */
	static private function _cleanInstancesByMax() {
		if (!self::$_instance_max)
			return false;
		$max = intval(self::$_instance_max);
		if (count(self::$_instances) > ($max + 10)) {
			self::$_instances = array_slice(self::$_instances, -$max, $max);
		}
	}
	
	/**
	 * 全局静态类加载策略 - 根据使用频率来清除使用频率较低的值
	 * @param string $key
	 * @return string
	 */
	static private function _cleanInstanceByFrequence($key) {
		if (!self::$_instance_frequence)
			return false;
		if (!isset(self::$_instances[$key]['frequence']))
			self::$_instances[$key]['frequence'] = self::$_instance_frequence;
		foreach (self::$_instances as $k => $v) {
			if ($key == $k)
				continue;
			if (intval(self::$_instances[$k]['frequence']) < 1) {
				unset(self::$_instances[$k]);
			} else
				self::$_instances[$k]['frequence']--;
		}
	}
	
	/**
	 * 自动加载框架底层类库
	 * 包括基础的抽象类和接口
	 */
	static private function _autoIncludeBaseLib() {
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
		if (($pos = strpos($systemConfig, '.')) !== false)
			$systemConfig = substr($systemConfig, 0, $pos);
		if (!file_exists($systemConfigPath . self::getSeparator() . $systemConfig))
			throw new Exception('SYS Excetion ：配置文件不存在!!!');
		self::import($systemConfigPath . self::getSeparator() . $systemConfig);
		self::getInstance('WSystemConfig')->parse($systemConfig, $config);
	
	}

}

/*
 * 初始化框架上下文
 * 
 * */
W::init($sysConfig);
