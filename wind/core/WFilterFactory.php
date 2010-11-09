<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WFilterFactory extends WFactory {
	private static $index = 0;
	private static $filters = array();
	private static $configs = array();
	private static $state = false;
	
	private static $callBack = null;
	private static $args = array();
	
	/**
	 * 创建一个Filter
	 * @param WSystemConfig $config
	 * @return WFilter
	 */
	static function create($config = null) {
		if ($config != null && empty(self::$filters))
			self::_initFilters($config);
		return self::createFilter();
	}
	
	static function &createFilter() {
		if ((int) self::$index >= count(self::$filters)) {
			self::$state = true;
			return null;
		}
		list($filterName, $path) = self::$filters[self::$index++];
		W::import($path);
		if ($filterName && class_exists($filterName) && in_array('WFilter', class_parents($filterName))) {
			$class = new ReflectionClass($filterName);
			$object = $class->newInstance();
			return $object;
		}
		self::createFilter();
	}
	
	/**
	 * 执行完过滤器后执行该方法的回调
	 */
	static public function execute() {
		if (self::$callBack === null)
			self::$callBack = array(
				'WFrontController', 
				'process'
			);
		if (is_array(self::$callBack)) {
			list($className, $action) = self::$callBack;
			if (!class_exists($className, true))
				throw new WException($className . ' is not exists!');
			if (!in_array($action, get_class_methods($className)))
				throw new WException('method ' . $action . ' is not exists in ' . $className . '!');
		} elseif (is_string(self::$callBack))
			if (!function_exists(self::$callBack))
				throw new WException(self::$callBack . ' is not exists!');
				
		call_user_func_array(self::$callBack, (array) self::$args);
	}
	
	/**
	 * 设置回调方法，执行完毕所有过滤器后将回调该方法
	 * 
	 * @param array $callback
	 * @param array $args
	 */
	static public function setExecute($callback) {
		$args = func_get_args();
		if (count($args) > 1) {
			unset($args[0]);
			self::$args = $args;
		}
		self::$callBack = $callback;
	}
	
	/**
	 * 在filter链中动态的删除一个filter
	 * @param string $filterName
	 */
	static protected function deleteFilter($filterName) {
		if (!in_array($filterName, self::$filters))
			return false;
		$deleteIndex = 0;
		foreach (self::$filters as $key => $value) {
			if ($value[0] == $filterName) {
				$deleteIndex = $key;
				unset(self::$filters[$key]);
			}
		}
		if ($deleteIndex == self::$index)
			self::$index++;
	}
	
	/**
	 * 在filter链中动态的添加一个filter，当befor为空时，添加到程序结尾处
	 * @param string $filterName
	 * @param string $path
	 * @param string $beforFilter
	 */
	static protected function addFilter($filterName, $path, $beforFilter = '') {
		$addIndex = count(self::$filters);
		if ($beforFilter) {
			$exchange = null;
			foreach (self::$filters as $key => $value) {
				if ($key > $addIndex) {
					self::$filters[$key] = $exchange;
					$exchange = $value;
				}
				if ($value[0] == $beforFilter) {
					$addIndex = $key + 1;
					$exchange = self::$filters[$key + 1];
				}
			}
			$exchange != null && self::$filters[$key + 1] = $exchange;
		}
		self::$filters[$addIndex] = array(
			$filterName, 
			$path
		);
	}
	
	/**
	 * 获得当前过滤器状态，是否已经被初始化了
	 * @return string
	 */
	static public function getState() {
		return self::$state;
	}
	
	/**
	 * 初始化一个过滤器
	 * @param WSystemConfig $config
	 */
	static private function _initFilters($configObj) {
		self::$index = 0;
		self::$filters = array();
		$config = $configObj->getFiltersConfig();
		foreach ((array) $config as $key => $value) {
			self::$filters[] = array(
				$key, 
				$value
			);
		}
		self::$configs = $config;
	}

}