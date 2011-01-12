<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.factory.IWindFactory');
L::import('WIND:core.base.WindModule');
/**
 * 抽象的类工厂
 * 解析类配置文件
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class AbstractWindFactory extends WindModule implements IWindFactory {
	
	protected $classDefinitions = array();
	
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
	
	/**
	 * 根据类的别名获得类实例
	 * 
	 * @param string $classAlias
	 * @return object|null
	 */
	public function getInstance($alias) {
		$instance = null;
		if (!isset($this->classDefinitions[$alias])) return $instance;
		/*@var $classDefinition WindClassDefinition */
		$classDefinition = $this->classDefinitions[$alias];
		$args = func_get_args();
		if ($args > 1) unset($args[0]);
		return $classDefinition->getInstance($this, $args);
	}
	
	/**
	 * 根据类名创建类实例
	 * 
	 * @param array $classDefinition
	 * @return void|mixed
	 */
	public function createInstance($className, $args) {
		if (!class_exists($className)) throw new WindException('create class instance error. class ' . $className . 'is not exists.');
		$class = new ReflectionClass($className);
		if ($class->isAbstract() || $class->isInterface()) return;
		$object = call_user_func_array(array($class, 'newInstance'), (array) $args);
		return $object;
	}
	
	/**
	 * 返回类定义，是否为单利模式加载
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
	 * @param string $classDefinition
	 */
	public function getClassDefinition($classAlias) {
		if (!isset($this->classDefinitions[$classAlias])) return null;
		return $this->classDefinitions[$classAlias];
	}
	
	/**
	 * @param WindClassDefinistion $classDefinition
	 */
	public function addClassDefinitions($classDefinition) {
		$alias = $classDefinition->getAlias();
		if (!isset($this->classDefinitions[$alias])) {
			$this->classDefinitions[$alias] = $classDefinition;
		}
	}
	
	/**
	 * 加载并解析类定义文件
	 * 
	 * @param string $classesDefinitions
	 */
	protected function loadClassDefinitions($classDefinitions, $classDefinitionType = 'WindClassDefinition') {
		if (!is_array($classDefinitions)) throw new WindException('input type error.');
		foreach ($classDefinitions as $key => $definition) {
			if (!$this->checkClassDefinition($definition)) continue;
			$definition[WindClassDefinition::NAME] = $key;
			$classDefinition = $this->createInstance($classDefinitionType, array($definition));
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
		return true;
	}

}