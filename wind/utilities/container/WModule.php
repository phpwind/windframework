<?php
/**
 * @author $Author$ <papa0924@gmail.com>
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

/**
 * 所有module的基础抽象类
 * 主要实现__get(), __set()等方法
 * 通过继承该类
 * 
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 */
abstract class WModule {
	
	protected $_trace = array();
	protected $_serialize = NULL;
	
	function __construct() {
		$this->_init();
	}
	
	private function _init() {}
	
	public function __get($propertyName) {
		if (!$this->_validateProperties($propertyName))
			return;
		return $this->$propertyName;
	}
	
	public function __set($propertyName, $value) {
		if (!$this->_validateProperties($propertyName))
			return;
		$this->_trace['setted'][$propertyName] = $value;
		$this->$propertyName = $value;
	}
	
	public function isseted($propertyName) {
		if (!$this->_validateProperties($propertyName))
			return;
		return array_key_exists($propertyName, $this->_trace['setted']);
	}
	
	/**
	 * 验证属性文件是否存在
	 * @param string $propertyName
	 */
	private function _validateProperties($propertyName) {
		return $propertyName && array_key_exists($propertyName, get_class_vars(get_class($this)));
	}

}