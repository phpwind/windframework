<?php
/**
 * Wind容器基类，创建类对象（分为两种模式，一种是普通模式，一种为单利模式）
 * 职责：
 * 类创建
 * 统一类接口访问
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindFactory implements IWindFactory {
	/**
	 * 类定义集合
	 * 
	 * @var array
	 */
	protected $classDefinitions = array();
	
	/**
	 * 类实例集合
	 *
	 * @var array
	 */
	protected $instances = array();

	/**
	 * 初始化抽象工厂类
	 * 可以通过两种方式初始化该工厂
	 * 1. 直接传递一个解析好的类配置信息
	 * @param string $configFile
	 */
	public function __construct($classDefinitions = array()) {
		$this->loadClassDefinitions($classDefinitions);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindFactory::getInstance()
	 */
	public function getInstance($alias) {
		if (isset($this->instances[$alias])) return $this->instances[$alias];
		if (!$classDefinition = $this->getClassDefinitionByAlias($alias)) return null;
		$args = func_get_args();
		unset($args[0]);
		return $classDefinition->getInstance($this, $args);
	}

	/* (non-PHPdoc)
	 * @see IWindFactory::getPrototype()
	 */
	public function getPrototype($alias) {
		$instance = $this->getInstance($alias);
		if ($instance === null) return null;
		return clone $instance;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindFactory::createInstance()
	 */
	static public function createInstance($className, $args = array()) {
		try {
			if (IS_DEBUG && IS_DEBUG <= WindLogger::LEVEL_DEBUG) {
				Wind::log('[core.factory.WindFactory.createInstance] create instance:' . $className, 
					WindLogger::LEVEL_DEBUG, 'core.factory');
			}
			$reflection = new ReflectionClass($className);
			return call_user_func_array(array($reflection, 'newInstance'), (array) $args);
		} catch (Exception $e) {
			throw new WindException($className, WindException::ERROR_CLASS_NOT_EXIST);
		}
	}

	/**
	 * @param string $classAlias
	 * @return boolean
	 */
	public function setSingled($classAlias, $instance) {
		Wind::log('[core.factory.WindFactory.createInstance] create singled instance:' . $classAlias, 
			WindLogger::LEVEL_INFO, 'core.factory');
		$this->instances[$classAlias] = $instance;
	}

	/**
	 * 获得类定义对象
	 * 
	 * @param string $classAlias
	 * @return WindClassDefinition
	 */
	protected function getClassDefinitionByAlias($classAlias) {
		if (!($definition = $this->classDefinitions[$classAlias])) return null;
		if ($definition instanceof WindClassDefinition) return $definition;
		$classDefinition = self::createInstance('WindClassDefinition', array($definition));
		$classDefinition->setAlias($classAlias);
		$this->addClassDefinitions($classDefinition);
		return $classDefinition;
	}

	/**
	 * 动态添加类定义对象
	 * 
	 * @param WindClassDefinition|array $classDefinition
	 * @return 
	 */
	public function addClassDefinitions($classDefinition) {
		if ($classDefinition instanceof WindClassDefinition) {
			$alias = $classDefinition->getAlias();
			$this->classDefinitions[$alias] = $classDefinition;
		} elseif (is_array($classDefinition)) {
			foreach ($classDefinition as $value)
				$this->addClassDefinitions($value);
		}
	}

	/**
	 * 类定义检查，检查类型以是否已经存在
	 * 
	 * @param array $definition
	 * @return boolean
	 */
	public function checkAlias($alias) {
		return isset($this->classDefinitions[$alias]);
	}

	/**
	 * 加载类定义信息
	 * 
	 * @param array $classDefinitions
	 * @throws WindException
	 * @return 
	 */
	protected function loadClassDefinitions($classDefinitions) {
		if (is_array($classDefinitions))
			$this->classDefinitions = $classDefinitions;
		else
			throw new WindException('[core.factory.WindFactory.loadClassDefinitions]', 
				WindException::ERROR_PARAMETER_TYPE_ERROR);
	}

}