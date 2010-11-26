<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:utility.factory.IWindFactory');

/**
 * 过滤器工场
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package
 */
class WindFilterFactory implements IWindFactory {
	private $index = 0;
	private $filters = array();
	private $state = false;
	
	private $callBack = null;
	private $args = array();
	
	private static $instance = null;
	
	/**
	 * 创建一个Filter
	 * 
	 * @param WSystemConfig $config
	 * @return WFilter 
	 */
	public function create() {
		if (empty($this->filters)) {
			$this->_initFilters();
		}
		return $this->createFilter();
	}
	
	/**
	 * 创建一个filter
	 * 
	 * @return WFilter 
	 */
	public function createFilter() {
		if ((int) $this->index >= count($this->filters)) {
			$this->state = true;
			return null;
		}
		list($filterName, $path) = $this->filters[$this->index++];
		L::import($path);
		if ($filterName && class_exists($filterName) && in_array('WFilter', class_parents($filterName))) {
			return new $filterName();
		}
		$this->createFilter();
	}
	
	/**
	 * 执行完过滤器后执行该方法的回调
	 */
	public function execute() {
		if ($this->callBack === null) $this->callBack = array('WFrontController', 'process');
		if (is_array($this->callBack)) {
			list($className, $action) = $this->callBack;
			if (!class_exists($className, true)) throw new WException($className . ' is not exists!');
			if (!in_array($action, get_class_methods($className))) throw new WException('method ' . $action . ' is not exists in ' . $className . '!');
		} elseif (is_string($this->callBack))
			if (!function_exists($this->callBack)) throw new WException($this->callBack . ' is not exists!');
		
		call_user_func_array($this->callBack, (array) $this->args);
	}
	
	/**
	 * 设置回调方法，执行完毕所有过滤器后将回调该方法
	 * 
	 * @param array $callback
	 * @param array $args
	 */
	public function setExecute($callback) {
		$args = func_get_args();
		if (count($args) > 1) {
			unset($args[0]);
			$this->args = $args;
		}
		$this->callBack = $callback;
	}
	
	/**
	 * 在filter链中动态的删除一个filter
	 * 思路：记录删除的位置，并且从被删除的元素开始，所有后面的元素都往前移，移完之后将最后一个元素删除
	 * 
	 * @param string $filterName
	 */
	public function deleteFilter($filterName) {
		$deleteIndex = -1;
		foreach ($this->filters as $key => $value) {
			if ($key > $deleteIndex && $deleteIndex >= 0) $this->filters[$key - 1] = $value;
			if ($value[0] == $filterName) $deleteIndex = $key;
		}
		if ($deleteIndex >= 0) {
			array_pop($this->filters);
		}
	}
	
	/**
	 * 在filter链中动态的添加一个filter
	 * 当befor为空时，添加到程序结尾处
	 * 如果befor有值，则遍历数组，找到befor的位置，将新的过滤器添加到befor后面，
	 * 并将所有原befor位置后的过滤器往后移一位
	 * 
	 * @param string $filterName
	 * @param string $path
	 * @param string $beforFilter
	 */
	public function addFilter($filterName, $path, $beforFilter = '') {
		$addIndex = count($this->filters);
		if ($beforFilter) {
			$exchange = null;
			foreach ($this->filters as $key => $value) {
				if ($key > $addIndex) {
					$this->filters[$key] = $exchange;
					$exchange = $value;
				}
				if ($value[0] == $beforFilter) {
					$addIndex = $key + 1;
					if (!isset($this->filters[$key + 1])) break;
					$exchange = $this->filters[$key + 1];
				}
			}
			$exchange != null && $this->filters[] = $exchange;
		}
		$this->filters[$addIndex] = array($filterName, $path);
	}
	
	/**
	 * 获得当前过滤器状态，是否已经被初始化了
	 * 
	 * @return boolean 
	 */
	public function getState() {
		return $this->state;
	}
	
	/**
	 * 初始化一个过滤器
	 * 
	 * @param WSystemConfig $config
	 */
	private function _initFilters() {
		$this->index = 0;
		$this->filters = array();
		$filters = C::getConfig('filters');
		foreach ((array) $filters as $key => $value) {
			$path = $value[IWindConfig::FILTER_NAME];
			$name = $value[IWindConfig::FILTER_PATH];
			if (($pos = strrpos($path, '.')) === false)
				$filterName = $path;
			else
				$filterName = substr($path, $pos + 1);
			$this->filters[] = array($filterName, $path, $name);
		}
	}
	
	/**
	 * 创建一个工厂
	 * 
	 * @return WindFilterFactory
	 */
	static function getFactory() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
}