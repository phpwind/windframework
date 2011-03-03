<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.factory.IWindFactory');
/**
 * Wind容器基类，创建类对象（分为两种模式，一种是普通模式，一种为单利模式）
 * 
 * 职责：
 * 类创建
 * 统一类接口访问
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindFactory implements IWindFactory {

	protected $classDefinitionType = 'WIND:core.factory.WindClassDefinition';

	protected $_classDefinitions = array();

	protected $classDefinitions = array();

	protected $classAlias = array();

	protected $cache = '';

	/**
	 * 初始化抽象工厂类
	 * 可以通过两种方式初始化该工厂
	 * 1. 直接传递一个解析好的类配置信息
	 * 
	 * @param string $configFile
	 */
	public function __construct($classDefinitions = array()) {
		$this->loadClassDefinitions($classDefinitions);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindFactory::getInstance()
	 */
	public function getInstance($alias) {
		$classDefinition = $this->getClassDefinitionByAlias($alias);
		if (!($classDefinition instanceof WindClassDefinition)) {
			return null;
		}
		/*@var $classDefinition WindClassDefinition */
		$args = func_get_args();
		unset($args[0]);
		return $classDefinition->getInstance($this, $args);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindFactory::createInstance()
	 */
	static public function createInstance($className, $args = array()) {
		if (!$className) return null;
		if (strpos($className, ':') !== false) $className = L::import($className);
		if (!$className || !class_exists($className)) {
			throw new WindException($className, WindException::ERROR_CLASS_NOT_EXIST);
		}
		$reflection = new ReflectionClass($className);
		if ($reflection->isAbstract() || $reflection->isInterface()) return null;
		return call_user_func_array(array($reflection, 'newInstance'), (array) $args);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $classAlias
	 * @return boolean
	 */
	public function isSingled($classAlias) {
		$classDefinition = $this->getClassDefinitionByAlias($classAlias);
		return isset($classDefinition[self::CLASS_SINGLE]) && $classDefinition[self::CLASS_SINGLE] === 'false';
	}

	/**
	 * 获得类定义对象
	 * 
	 * @param string $classAlias
	 * @return WindClassDefinition
	 */
	public function getClassDefinitionByAlias($classAlias) {
		if (!isset($this->classAlias[$classAlias]) && isset($this->_classDefinitions[$classAlias])) {
			$definition = $this->_classDefinitions[$classAlias];
			$classDefinition = self::createInstance($this->classDefinitionType, array($definition));
			$classDefinition->setAlias($classAlias);
			$this->addClassDefinitions($classDefinition);
			return $classDefinition;
		}
		return $this->classDefinitions[$this->classAlias[$classAlias]];
	}

	/**
	 * Enter description here ...
	 * 
	 * @param WindClassDefinition|array $classDefinition
	 */
	public function addClassDefinitions($classDefinition) {
		if ($classDefinition instanceof WindClassDefinition) {
			$className = $classDefinition->getClassName();
			$alias = $classDefinition->getAlias();
			$this->classDefinitions[$className] = $classDefinition;
			$this->classAlias[$alias] = $className;
		} elseif (is_array($classDefinition)) {
			foreach ($classDefinition as $value)
				$this->addClassDefinitions($value);
		}
	}

	/**
	 * Enter description here ...
	 * 
	 * @param array $classDefinitions
	 * @throws WindException
	 */
	protected function loadClassDefinitions($classDefinitions) {
		if (!is_array($classDefinitions)) {
			throw new WindException($classDefinitions, WindException::ERROR_PARAMETER_TYPE_ERROR);
		}
		$this->_classDefinitions = $classDefinitions;
	}

	/**
	 * 类定义检查
	 * 
	 * @param array $definition
	 * @return string
	 */
	protected function checkClassDefinition($definition) {
		//TODO check class definition
		return true;
	}

}