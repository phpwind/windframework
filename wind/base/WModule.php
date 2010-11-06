<?php
/**
 * @author $Author$ <papa0924@gmail.com>
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

/**
 * 所有module的基础抽象类
 * 主要实现__get(), __set() , __isset() , __unset()等方法
 * 通过继承该类
 * 
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 */
abstract class WModule {
	
	private $_trace = array();
	private $_serialize = NULL;
	
	function __construct() {
		$this->_init();
	}
	
	private function _init();
	
	function __get($propertyName) {
		$this->_validateProperties($propertyName);
		return $this->$propertyName;
	}
	
	function __set($propertyName, $value) {
		$this->_validateProperties($propertyName);
		$this->_trace['setted'][$propertyName] = $value;
		$this->$propertyName = $value;
	}
	
	function isseted($propertyName) {
		$this->_validateProperties($propertyName);
		return array_key_exists($propertyName, $this->_trace['setted']);
	}
	
	/**
	 * 验证属性文件是否存在
	 * @param string $propertyName
	 */
	private function _validateProperties($propertyName) {
		if (!$propertyName)
			throw new Exception('empty args !!!!');
		if (!property_exists(__CLASS__, $propertyName))
			throw new Exception('property ' . $propertyName . ' not exist!!!!');
	}

}