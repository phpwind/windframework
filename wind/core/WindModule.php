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

	/**
	 * Enter description here ...
	 * 
	 * @param string $propertyName
	 * @param string $value
	 */
	public function __set($propertyName, $value) {
		if (!$this->validatePropertyName($propertyName, $value)) return;
		$this->$propertyName = $value;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $propertyName
	 */
	public function __get($propertyName) {
		if (!$this->validatePropertyName($propertyName)) return null;
		return $propertyName;
	}

	/**
	 * 实现setter或者getter方法调用
	 * 
	 */
	public function __call($methodName, $args) {
		$_propertyName = '';
		$_perfix = substr($methodName, 0, 3);
		if (in_array($_perfix, array('set', 'get'))) {
			$_propertyName = trim(substr($methodName, 3), '_');
			$_propertyName = strtolower(substr($_propertyName, 0, 1)) . substr($_propertyName, 1);
			if (!$_propertyName || !in_array($_propertyName, (array) $this->getWriteTableForGetterAndSetter())) return;
			switch ($_perfix) {
				case 'set':
					$this->$_propertyName = $args[0];
					break;
				case 'get':
					return $this->$_propertyName;
					break;
				default:
					break;
			}
		}
	}

	public function __clone() {
		foreach ($this->getCloneProperty() as $value) {
			$this->$value = clone $this->$value;
		}
	}

	/**
	 * Enter description here ...
	 * 
	 * @return multitype:
	 */
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

	/**
	 * 验证属性白名单
	 */
	protected function validatePropertyName($propertyName, $value = null) {
		$autoSetProperty = $this->getAutoSetProperty();
		if (empty($autoSetProperty)) return true;
		
		if (!key_exists($propertyName, $autoSetProperty)) return false;
		//TODO add check for value
		return true;
	}

	/**
	 * Enter description here ...
	 */
	protected function getAutoSetProperty() {
		return array();
	}

	/**
	 * 设置自动实现Getter/Setter方法的属性名称
	 */
	protected function getWriteTableForGetterAndSetter() {
		return array();
	}

	protected function getCloneProperty() {
		return array();
	}

}