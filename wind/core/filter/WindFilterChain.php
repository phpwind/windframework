<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

L::import('WIND:core.filter.WindHandlerInterceptorChain');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class WindFilterChain extends WindHandlerInterceptorChain {
	
	/**
	 * @param array $filterConfig
	 */
	public function __construct($filterConfig) {
		$this->_initFilters($filterConfig);
	}
	
	/**
	 * @param string $filterName
	 */
	public function deleteFilter($alias) {
		unset($this->_interceptors[$alias]);
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
	public function addFilter($filter, $beforFilter = '') {
		if ($beforFilter === '') {
			$this->addInterceptors(array(get_class($filter) => $filter));
			return true;
		}
		$_interceptors = array();
		foreach ($this->_interceptors as $key => $interceptor) {
			if ($beforFilter === $key) break;
			$_interceptors[$key] = $interceptor;
			unset($this->_interceptors[$key]);
		}
		$_interceptors[get_class($filter)] = $filter;
		$this->_interceptors = (array) $_interceptors + (array) $this->_interceptors;
	}
	
	/**
	 * Enter description here ...
	 * @param array $filters
	 */
	private function _initFilters($filters = array()) {
		$cleanFilters = array();
		foreach ((array) $filters as $key => $filter) {
			$filterClass = L::import($filter[WindConfig::CLASS_PATH]);
			if (!class_exists($filterClass)) continue;
			$cleanFilters[$key] = new $filterClass();
		}
		$this->addInterceptors($cleanFilters);
	}

}