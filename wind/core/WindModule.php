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
abstract class WindModule {

	private $_classProxy = null;

	public function toArray() {
		$class = new ReflectionClass(get_class($this));
		$properties = $class->getProperties();
		$vars = array();
		foreach ($properties as $property) {
			$_propertyName = $property->name;
			$vars[$_propertyName] = $this->$_propertyName;
		}
		return $vars;
	}

	/**
	 * @return the $_classProxy
	 */
	public function getClassProxy() {
		return ($this->_classProxy instanceof WindClassProxy) ? $this->_classProxy : null;
	}

	/**
	 * @param WindClassProxy $classProxy
	 */
	public function setClassProxy($classProxy) {
		$this->_classProxy = $classProxy->initClassProxy($this);
	}

}