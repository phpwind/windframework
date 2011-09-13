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
	protected $proxyType = 'WIND:factory.WindClassProxy';
	protected $classDefinitions = array();
	protected $instances = array();
	protected $prototype = array();
	protected $destories = array();

	/**
	 * 初始化抽象工厂类
	 * 可以通过两种方式初始化该工厂
	 * 1. 直接传递一个解析好的类配置信息
	 * @param string $configFile
	 */
	public function __construct($classDefinitions = array()) {
		if (is_array($classDefinitions)) {
			$this->classDefinitions = $classDefinitions;
		}
	}

	/* (non-PHPdoc)
	 * @see AbstractWindFactory::getInstance()
	 */
	public function getInstance($alias, $args = array()) {
		$instance = null;
		$definition = isset($this->classDefinitions[$alias]) ? $this->classDefinitions[$alias] : array();
		if (isset($this->prototype[$alias])) {
			$instance = clone $this->prototype[$alias];
			if (isset($definition['destroy']))
				$this->destories[] = array($instance, $definition['destroy']);
		} elseif (isset($this->instances[$alias])) {
			$instance = $this->instances[$alias];
		} else {
			if (!$definition)
				throw new WindException('[factory.WindFactory.getInstance] component \'' . $alias . '\' is not exist.');
			if (isset($definition['constructor-args']) && !$args)
				$this->buildArgs($definition['constructor-args'], $args);
			if (!isset($definition['className']))
				$definition['className'] = Wind::import(@$definition['path']);
			$instance = $this->createInstance($definition['className'], $args);
			if (isset($definition['config']))
				$this->resolveConfig($definition['config'], $alias, $instance);
			if (isset($definition['properties']))
				$this->buildProperties($definition['properties'], $instance);
			if (isset($definition['initMethod']))
				$this->executeInitMethod($definition['initMethod'], $instance);
			!isset($definition['scope']) && $definition['scope'] = 'application';
			$this->setScope($alias, $definition['scope'], $instance, $definition);
		}
		if (isset($definition['proxy']))
			$instance = $this->setProxyForClass($definition['proxy'], $instance);
		return $instance;
	}

	/**
	 * 对象组件对象到应用工厂中
	 * @param object $instance
	 * @param string $alias
	 * @param string $scope
	 * @return boolean
	 */
	public function registInstance($instance, $alias, $scope = 'singleton') {
		if (!is_object($instance) || !$alias)
			return false;
		return $this->setScope($alias, $scope, $instance);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindFactory::createInstance()
	 */
	static public function createInstance($className, $args = array()) {
		try {
			if (!$className || !class_exists($className))
				throw new WindException('class is not exist.');
			if (empty($args)) {
				return new $className();
			} else {
				$reflection = new ReflectionClass($className);
				return call_user_func_array(array($reflection, 'newInstance'), (array) $args);
			}
		} catch (Exception $e) {
			throw new WindException('[base.WindFactory] create instance \'' . $className . '\' fail.' . $e->getMessage(), 
				WindException::ERROR_CLASS_NOT_EXIST);
		}
	}

	/* (non-PHPdoc)
	 * @see IWindFactory::getPrototype()
	 */
	public function getPrototype($alias) {
		return isset($this->prototype[$alias]) ? clone $this->prototype[$alias] : null;
	}

	/**
	 * 动态添加类定义对象
	 * @param string $alias
	 * @param array $classDefinition
	 * @return 
	 */
	public function addClassDefinitions($alias, $classDefinition) {
		if (is_string($alias) && !empty($alias)) {
			if (!isset($this->classDefinitions[$alias]))
				$this->classDefinitions[$alias] = $classDefinition;
		} else
			throw new WindException('[base.WindFactory.addClassDefinitions] class alias is empty.', 
				WindException::ERROR_PARAMETER_TYPE_ERROR);
	}

	/**
	 * 加载类定义,如果merge为true，则覆盖原有配置信息
	 * @param array $classDefinitions
	 * @param boolean $merge
	 * @return
	 */
	public function loadClassDefinitions($classDefinitions, $merge = true) {
		foreach ((array) $classDefinitions as $alias => $definition) {
			if (!is_array($definition))
				continue;
			if (!isset($this->classDefinitions[$alias]) || $merge === false) {
				$this->classDefinitions[$alias] = $definition;
				continue;
			}
			$this->classDefinitions[$alias] = WindUtility::mergeArray($this->classDefinitions[$alias], $definition);
			unset($this->instances[$alias], $this->prototype[$alias]);
		}
	}

	/**
	 * 类定义检查，检查类型以是否已经存在
	 * @param array $definition
	 * @return boolean
	 */
	public function checkAlias($alias) {
		if (isset($this->prototype[$alias]))
			return true;
		elseif (isset($this->instances[$alias]))
			return true;
		return false;
	}

	/**
	 * @return boolean
	 */
	public function executeDestroyMethod() {
		try {
			foreach ($this->destories as $call)
				call_user_func_array($call, array());
		} catch (Exception $e) {
			throw new WindException($e->getMessage());
		}
	}

	/**
	 * @param $constructors
	 * @param args
	 */
	protected function buildArgs($constructors, &$args) {
		foreach ((array) $constructors as $key => $_var) {
			$key = intval($key);
			if (isset($_var['value'])) {
				$args[$key] = $_var['value'];
			} elseif (isset($_var['ref']))
				$args[$key] = $this->getInstance($_var['ref']);
			elseif (isset($_var['path'])) {
				$_className = Wind::import($_var['path']);
				$args[$key] = $this->createInstance($_className);
			}
		}
		ksort($args);
	}

	/**
	 * @param string $alias
	 * @param string $scope
	 * @param object $instance
	 */
	protected function setScope($alias, $scope, $instance, $definition) {
		switch ($scope) {
			case 'prototype':
				$this->prototype[$alias] = clone $instance;
				break;
			case 'application':
				$this->instances[$alias] = $instance;
			case 'singleton':
				$this->instances[$alias] = $instance;
			default:
				if (isset($definition['destroy']))
					$this->destories[$alias] = array($instance, $definition['destroy']);
				break;
		}
		return true;
	}

	/**
	 * 为类对象设置配置
	 * @param array|string $config
	 * @param string $alias
	 * @param WindModule $instance
	 * @return
	 */
	protected function resolveConfig($config, $alias, $instance) {
		if (isset($config['resource'])) {
			$_configPath = Wind::getRealPath($config['resource'], true);
			$configParser = $this->getInstance('configParser');
			$config = $configParser->parse($_configPath, $alias, true, Wind::getApp()->getComponent('windCache'));
		}
		if ($config && method_exists($instance, 'setConfig'))
			$instance->setConfig($config);
	}

	/**
	 * 执行用户配置的初始化操作
	 * @param string $initMethod
	 * @param object $instance
	 * @return
	 */
	protected function executeInitMethod($initMethod, $instance) {
		try {
			return call_user_func_array(array($instance, $initMethod), array());
		} catch (Exception $e) {
			throw new WindException(
				'[base.WindFactory.executeInitMethod] (' . $initMethod . ', ' . $e->getMessage() . ')', 
				WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		}
	}

	/**
	 * 为类设置代理
	 * @param string $definition
	 * @param WindModule $instance
	 * @return WindClassProxy
	 */
	protected function setProxyForClass($proxy, $instance) {
		if ($proxy === 'false' || $proxy === false)
			return $instance;
		
		if ($proxy === 'true' || $proxy === true)
			$proxy = $this->proxyType;
		$this->addClassDefinitions($proxy, array('path' => $proxy, 'scope' => 'prototype'));
		return $this->getInstance($proxy)->registerTargetObject($instance);
	}

	/**
	 * 将类实例的依赖注入到类实例中
	 * @param string $properties
	 * @param WindModule  $instance
	 */
	protected function buildProperties($properties, $instance) {
		if (!isset($properties['delay'])) {
			$instance->setDelayAttributes($properties);
		} elseif ($properties['delay'] === 'false' || $properties['delay'] === false) {
			foreach ($properties as $key => $subDefinition) {
				$_value = '';
				if (isset($subDefinition['value']))
					$_value = $subDefinition['value'];
				elseif (isset($subDefinition['ref']))
					$_value = $this->getInstance($subDefinition['ref']);
				elseif (isset($subDefinition['path'])) {
					$_className = Wind::import($subDefinition['path']);
					$_value = $this->createInstance($_className);
				}
				$_setter = 'set' . ucfirst(trim($key, '_'));
				if (method_exists($instance, $_setter))
					call_user_func_array(array($instance, $_setter), array($_value));
			}
		} else
			$instance->setDelayAttributes($properties);
	}
}