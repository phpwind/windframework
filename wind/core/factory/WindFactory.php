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
	const WIND_PROXY = 'WIND:core.factory.proxy.WindClassProxy';
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
		$this->classDefinitions = $classDefinitions;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindFactory::getInstance()
	 */
	public function getInstance($alias, $args = array()) {
		if (isset($this->instances[$alias])) return $this->instances[$alias];
		if (!($definition = $this->checkAlias($alias))) return null;
		$this->buildDefinition($definition);
		$_subDefinitions = $definition['constructorArg'];
		foreach ($_subDefinitions as $_subDefinition) {
			if (isset($_subDefinition['value'])) {
				$args[] = $_subDefinition['value'];
			} elseif (isset($_subDefinition['ref']))
				$args[] = $this->getInstance($_subDefinition['ref']);
		}
		$config = $this->buildConfig($definition, $alias);
		$instance = $this->createInstance($definition['className'], $args);
		if (!empty($config)) $instance->setConfig($config);
		if ($definition['properties']) $this->buildProperties($definition['properties'], $instance);
		if ($definition['initMethod']) $this->executeInitMethod($definition['initMethod'], $instance);
		if ($definition['proxy']) $instance = $this->setProxyForClass($definition['proxy'], $instance);
		
		$this->setScope($alias, $definition['scope'], $instance);
		return $instance;
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

	/* (non-PHPdoc)
	 * @see IWindFactory::getPrototype()
	 */
	public function getPrototype($alias) {
		$instance = $this->getInstance($alias);
		if ($instance === null) return null;
		return clone $instance;
	}

	/**
	 * 动态添加类定义对象
	 * 
	 * @param string $alias
	 * @param array $classDefinition
	 * @return 
	 */
	public function addClassDefinitions($alias, $classDefinition) {
		if (isset($this->classDefinitions[$alias])) return;
		$this->classDefinitions[$alias] = $classDefinition;
	}

	/**
	 * 类定义检查，检查类型以是否已经存在
	 * 
	 * @param array $definition
	 * @return boolean
	 */
	public function checkAlias($alias) {
		return isset($this->classDefinitions[$alias]) ? $this->classDefinitions[$alias] : false;
	}

	/**
	 * @param string $alias
	 * @param string $scope
	 * @param object $instance
	 */
	protected function setScope($alias, $scope, $instance) {
		switch ($scope) {
			case 'prototype':
				break;
			case 'application':
				$this->instances[$alias] = $instance;
				break;
			default:
				$this->instances[$alias] = $instance;
				break;
		
		}
	}

	/**
	 * 为类对象设置配置
	 * 
	 * @param array|string $config
	 * @param string $alias
	 * @return
	 */
	protected function buildConfig(&$definition, $alias) {
		if (!($config = $definition['config'])) return array();
		if (isset($config['resource']) && !empty($config['resource'])) {
			$_configPath = Wind::getRealPath($config['resource'], true);
			$configParser = $this->getInstance(COMPONENT_CONFIGPARSER);
			$cache = $alias !== COMPONENT_CACHE ? $this->getInstance(COMPONENT_CACHE) : null;
			$config = $configParser->parse($_configPath, $alias, 'components_config_cache', $cache);
		}
		if (isset($config['class']) && !$definition['path']) {
			$definition['path'] = $config['class'];
			$definition['className'] = Wind::import($definition['path']);
		}
		return $config;
	}

	/**
	 * 执行用户配置的初始化操作
	 * 
	 * @param string $initMethod
	 * @param object $instance
	 * @return
	 */
	protected function executeInitMethod($initMethod, $instance) {
		try {
			return call_user_func_array(array($instance, $initMethod), array());
		} catch (Exception $e) {
			throw new WindException(
				'[core.factory.WindFactory.executeInitMethod] (' . $initMethod . ', ' . $e->getMessage() . ')', 
				WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		}
	}

	/**
	 * 为类设置代理
	 * 
	 * @param string $definition
	 * @param WindModule $instance
	 * @return WindClassProxy
	 */
	protected function setProxyForClass($proxy, $instance) {
		if ($proxy === 'false' || $proxy === false) return $instance;
		$proxy = Wind::import(self::WIND_PROXY);
		return $this->createInstance($proxy, array($instance));
	}

	/**
	 * 将类实例的依赖注入到类实例中
	 * 
	 * @param string $properties
	 * @param WindModule  $instance
	 */
	protected function buildProperties($properties, $instance) {
		if (isset($properties['delay']) && ($properties['delay'] === 'false' || $properties['delay'] === false)) {
			unset($properties['delay']);
			foreach ($properties as $key => $subDefinition) {
				$_value = '';
				if (isset($subDefinition['value']))
					$_value = $subDefinition['value'];
				elseif (isset($subDefinition['ref']))
					$_value = $this->getInstance($subDefinition['ref']);
				if ($_value) {
					$_setter = 'set' . ucfirst(trim($key, '_'));
					call_user_func_array(array($instance, $_setter), array($_value));
				}
			}
		}
		$instance->setDelayAttributes($properties);
	}

	/**
	 * 验证类定义的正确性
	 * 
	 * @param array definition
	 * @return boolean
	 */
	private function buildDefinition(&$definition) {
		$_definition = array('path' => '', 'className' => '', 'factoryMethod' => '', 'initMethod' => '', 'scope' => 'application', 'proxy' => false, 'properties' => array(), 'config' => array(), 'constructorArg' => array());
		$definition = array_merge($_definition, $definition);
		$definition['className'] = Wind::import($definition['path']);
	}

}