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

	const CLASSES_DEFINITIONS = 'classes';

	protected $classDefinitionType = 'WIND:core.factory.WindClassDefinition';

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
	public function __construct($classDefinitions) {
		$this->loadClassDefinitions($classDefinitions);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindFactory::getInstance()
	 */
	public function getInstance($alias) {
		$classDefinition = $this->getClassDefinition($alias);
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
	public function createInstance($className, $args = array()) {
		if (!class_exists($className)) {
			throw new WindException($className, WindException::ERROR_CLASS_NOT_EXIST);
		}
		$reflection = new ReflectionClass($className);
		if ($reflection->isAbstract() || $reflection->isInterface())
			return null;
		return call_user_func_array(array($reflection, 'newInstance'), (array) $args);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $classAlias
	 * @return boolean
	 */
	public function isSingled($classAlias) {
		$classDefinition = $this->getClassDefinition($classAlias);
		return isset($classDefinition[self::CLASS_SINGLE]) && $classDefinition[self::CLASS_SINGLE] === 'false';
	}

	/**
	 * 获得类定义对象
	 * 
	 * @param string $classAlias
	 * @return WindClassDefinition
	 */
	public function getClassDefinition($classAlias, $isAlias = true) {
		if (isset($this->classDefinitions[$classAlias])) {
			return $this->classDefinitions[$classAlias];
		} elseif ($isAlias && isset($this->classAlias[$classAlias])) {
			return $this->getClassDefinition($this->classAlias[$classAlias], false);
		}
		return null;
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
		$classDefinitionType = L::import($this->classDefinitionType);
		if (!class_exists($classDefinitionType)) {
			throw new WindException($classDefinitionType, WindException::ERROR_CLASS_NOT_EXIST);
		}
		foreach ($classDefinitions as $classAlias => $definition) {
			if (!$this->checkClassDefinition($definition))
				continue;
			$classDefinition = self::createInstance($classDefinitionType, array($definition));
			$classDefinition->setAlias($classAlias);
			$this->addClassDefinitions($classDefinition);
		}
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