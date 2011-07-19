<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * 
 * 配置文件格式:
 * <class name='' path='' factory-method='' init-method='' scope="singleton/prototype/request/session">
 * <property name='' ref/value=''>
 * <constructor-arg ref/value=''>
 * <import resource=''>
 * 
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindClassDefinition {
	/* 配置信息定义 */
	const NAME = 'name';
	const PATH = 'path';
	const FACTORY_METHOD = 'factory-method';
	const INIT_METHOD = 'init-method';
	const SCOPE = 'scope';
	const PROPERTIES = 'properties';
	const CONSTRUCTOR_ARG = 'constructor-arg';
	const REF = 'ref';
	const VALUE = 'value';
	const DELAY = 'delay';
	const PROXY = 'proxy';
	const CONFIG = 'config';
	const RESOURCE = 'resource';
	/* 支持的类命名空间 */
	const SCOPE_SINGLETON = 'singleton';
	const SCOPE_PROTOTYPE = 'prototype';
	const SCOPE_REQUEST = 'request';
	/**
	 * 类代理类型
	 *
	 * @var string
	 */
	protected $proxyClass = 'WIND:core.factory.proxy.WindClassProxy';
	/**
	 * 配置信息
	 *
	 * @var string|array
	 */
	protected $config;
	/**
	 * 类代理信息
	 *
	 * @var boolean|string
	 */
	protected $proxy;
	/**
	 * 类名称
	 * 
	 * @var string
	 */
	protected $className;
	/**
	 * 类别名
	 * 
	 * @var string
	 */
	protected $alias;
	/**
	 * 类路径
	 * 
	 * @var string
	 */
	protected $path;
	/**
	 * 类的存储空间
	 * 
	 * singleton/prototype/request/session
	 * @var string
	 */
	protected $scope;
	/**
	 * 类自定义的初始化方法
	 * 
	 * @var string
	 */
	protected $factoryMethod;
	/**
	 * 类设置属性之后的调用处理操作
	 * 
	 * @var string
	 */
	protected $initMethod;
	/**
	 * 构造参数定义
	 * 
	 * @var array
	 */
	protected $constructArgs = array();
	/**
	 * 类属性定义
	 * 
	 * @var array
	 */
	protected $properties = array();
	/**
	 * 类定义
	 * 
	 * @var array
	 */
	protected $classDefinition;
	/**
	 * @var instance
	 */
	private $instance = null;

	/**
	 * 根据类定义信息，初始化类对象
	 * 
	 * @param array $classDefinition
	 */
	public function __construct($classDefinition = array()) {
		$this->init($classDefinition);
	}

	/**
	 * 通过对象工厂创建单例对象
	 * 如果用户配置有factory-method项，则调用该组件的该方法生成实例
	 * 如果用户配置的该项方法不存在，则正常调用
	 * 
	 * @modified xiaoxia.xu
	 * @param IWindFactory $factory
	 * @return instance|Ambigous <prototype, void, mixed>|NULL
	 */
	public function getInstance($factory, $args = array()) {
		if ($instance = $this->executeFactoryMethod($args)) return $instance;
		switch ($this->scope) {
			case 'prototype':
				return $this->createInstanceWithPrototype($factory, $args);
			default:
				return $this->createInstanceWithSingleton($factory, $args);
		}
	}

	/**
	 * 以原型方式创建类实例
	 * @param IWindFactory $factory
	 * @param array $args
	 * @return NULL|object
	 */
	protected function createInstanceWithPrototype($factory, $args) {
		return $this->createInstance($factory, $args);
	}

	/**
	 * @param WindFactory $factory
	 * @param array $args
	 * @return NULL|object
	 */
	protected function createInstanceWithSingleton($factory, $args) {
		$_instance = $this->createInstance($factory, $args);
		$factory->setSingled($this->getAlias(), $_instance);
		return $_instance;
	}

	/**
	 * @modified xiaoxia.xu
	 * @param AbstractWindFactory $factory
	 * @param array $args
	 */
	protected function createInstance($factory, $args) {
		$args = $this->buildConstructArgs($factory, $args);
		$instance = $factory->createInstance($this->getClassName(), $args);
		if ($instance instanceof WindModule) {
			$this->buildConfig($instance, $factory);
			$this->buildProperties($instance, $factory);
			$this->executeInitMethod($instance);
			$instance = $this->setProxyForClass($instance, $factory);
		}
		return $instance;
	}

	/**
	 * 为类对象设置配置
	 * 
	 * @param WindModule $instance
	 * @param WindFactory $factory
	 * @return
	 */
	protected function buildConfig($instance, $factory) {
		if (!$config = $this->getConfig()) return;
		if (isset($config[self::RESOURCE])) {
			$config = $config[self::RESOURCE];
		}
		$instance->setConfig($config);
	}

	/**
	 * 为类设置代理
	 * 
	 * @param WindModule $instance
	 * @param WindFactory $factory
	 * @return WindClassProxy
	 */
	protected function setProxyForClass($instance, $factory) {
		if (!($proxy = $this->getProxy())) return $instance;
		if ($proxy === 'false' || $proxy === false) return $instance;
		$proxy = Wind::import($this->proxyClass);
		return $factory->createInstance($proxy, array($instance));
	}

	/**
	 * 执行用户配置的初始化操作
	 * 
	 * @author xiaoxia.xu
	 * @param object $instance
	 * @return
	 */
	private function executeInitMethod($instance) {
		try {
			if (!($initMethod = $this->getInitMethod())) return;
			return call_user_func_array(array($instance, $initMethod), array());
		} catch (Exception $e) {
			throw new WindException(
				'[core.factory.WindClassDefinition.executeInitMethod] (' . $this->getClassName() . '->' . $initMethod .
					 '()) "' . $e->getMessage() . '"', WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		}
	}

	/**
	 * 构造构造函数参数对象
	 * 
	 * @param WindFactory $factory
	 * @throws WindException
	 */
	protected function buildConstructArgs($factory, $args) {
		if ($args) return $args;
		$subDefinitions = $this->getConstructArgs();
		$_tmp = array();
		foreach ($subDefinitions as $key => $subDefinition) {
			if (isset($subDefinition[self::VALUE])) {
				$_tmp[$key] = $subDefinition[self::VALUE];
			} elseif (isset($subDefinition[self::REF]))
				$_tmp[$key] = $factory->getInstance($subDefinition[self::REF]);
		}
		return $_tmp;
	}

	/**
	 * 将类实例的依赖注入到类实例中
	 * 
	 * @param WindModule  $instance | 类实例
	 * @param WindFactory $factory | 抽象的类工厂
	 */
	protected function buildProperties($instance, $factory) {
		if (!$subDefinitions = $this->getPropertys()) return;
		foreach ($subDefinitions as $key => $subDefinition) {
			$_value = '';
			if (isset($subDefinition[self::VALUE])) $_value = $subDefinition[self::VALUE];
			if ($_value) {
				$_setter = 'set' . ucfirst(trim($key, '_'));
				call_user_func_array(array($instance, $_setter), array($_value));
			}
		}
		$instance->setDelayAttributes($subDefinitions);
	}

	/**
	 * 执行调用工厂方法
	 * 
	 * @param array $args
	 * @throws WindException
	 * @return NULL|mixed
	 */
	protected function executeFactoryMethod($args) {
		try {
			if (!($factoryMethod = $this->getFactoryMethod())) return null;
			return call_user_func_array(array($this->getClassName(), $factoryMethod), $args);
		} catch (Exception $e) {
			throw new WindException(
				'[core.factory.WindClassDefinition.executeFactoryMethod] (' . $this->getClassName() . '->' .
					 $factoryMethod . ')', WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		}
	}

	/**
	 * 初始化类定义
	 * 
	 * @param array $classDefinition
	 */
	protected function init($classDefinition) {
		try {
			if (empty($classDefinition)) return;
			foreach ($classDefinition as $key => $value) {
				if (strpos($key, '-') !== false) {
					list($_s1, $_s2) = explode('-', $key);
					$_s1 = ucfirst($_s1);
					$_s2 = ucfirst($_s2);
					$_setter = 'set' . $_s1 . $_s2;
				} else
					$_setter = 'set' . ucfirst($key);
				call_user_func_array(array($this, $_setter), array($value));
			}
			$this->setClassDefinition($classDefinition);
		} catch (Exception $e) {
			throw new WindException("[core.factory.WindClassDefinition.init]" . $e->getMessage(), 
				WindException::ERROR_SYSTEM_ERROR);
		}
	}

	/**
	 * @return the $className
	 */
	public function getClassName() {
		if (!$this->className) {
			$this->className = Wind::import($this->getPath());
		}
		return $this->className;
	}

	/**
	 * @return the $alias
	 */
	public function getAlias() {
		return $this->alias;
	}

	/**
	 * @return the $path
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return the $scope
	 */
	public function getScope() {
		return $this->scope;
	}

	/**
	 * @param string $className the $className to set
	 * @author Qiong Wu
	 */
	public function setClassName($className) {
		$this->className = $className;
	}

	/**
	 * @param string $alias the $alias to set
	 * @author Qiong Wu
	 */
	public function setAlias($alias) {
		$this->alias = $alias;
	}

	/**
	 * @param string $path the $path to set
	 * @author Qiong Wu
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @param string $scope the $scope to set
	 * @author Qiong Wu
	 */
	public function setScope($scope) {
		$this->scope = strtolower($scope);
	}

	/**
	 * @return the $constructArgs
	 */
	public function getConstructArgs() {
		return $this->constructArgs;
	}

	/**
	 * @return the $propertys
	 */
	public function getPropertys() {
		return $this->properties;
	}

	/**
	 * @return the $classDefinition
	 */
	public function getClassDefinition() {
		return $this->classDefinition;
	}

	/**
	 * @param array $constructArgs the $constructArgs to set
	 * @author Qiong Wu
	 */
	public function setConstructArgs($constructArgs) {
		if (is_array($constructArgs) && !empty($constructArgs)) $this->constructArgs += $constructArgs;
	}

	/**
	 * @param array $propertys the $propertys to set
	 * @author Qiong Wu
	 */
	public function setProperties($properties) {
		if (!is_array($properties)) return;
		$this->properties = array_merge($this->properties, $properties);
	}

	/**
	 * @param array $classDefinition the $classDefinition to set
	 * @author Qiong Wu
	 */
	public function setClassDefinition($classDefinition) {
		$this->classDefinition = $classDefinition;
	}

	/**
	 * return the $factoryMethod
	 * 
	 * @author xiaoxia.xu
	 * @return the $factoryMethod
	 */
	public function getFactoryMethod() {
		return $this->factoryMethod;
	}

	/**
	 * return the $initMethod
	 * 
	 * @author xiaoxia.xu
	 * @return the $initMethod
	 */
	public function getInitMethod() {
		return $this->initMethod;
	}

	/**
	 * the $factoryMethod to set
	 * 
	 * @author xiaoxia.xu
	 * @param string $factoryMethod 
	 */
	public function setFactoryMethod($factoryMethod) {
		$this->factoryMethod = $factoryMethod;
	}

	/**
	 * the $initMethod to set
	 * 
	 * @author xiaoxia.xu
	 * @param string $initMethod
	 */
	public function setInitMethod($initMethod) {
		$this->initMethod = $initMethod;
	}

	/**
	 * @return the $proxy
	 */
	public function getProxy() {
		return $this->proxy;
	}

	/**
	 * @param boolean $proxy
	 */
	public function setProxy($proxy) {
		$this->proxy = $proxy;
	}

	/**
	 * @return the $config
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @param string $config
	 */
	public function setConfig($config) {
		$this->config = $config;
	}

}