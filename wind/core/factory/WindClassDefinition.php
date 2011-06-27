<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-31
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

Wind::import('WIND:core.WindModule');
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
class WindClassDefinition extends WindModule {

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

	/* 支持的类命名空间 */
	const SCOPE_SINGLETON = 'singleton';

	const SCOPE_PROTOTYPE = 'prototype';

	const SCOPE_REQUEST = 'request';

	/**
	 * 类名称
	 * @var string
	 */
	protected $className = '';

	/**
	 * 类别名
	 * @var string
	 */
	protected $alias = '';

	/**
	 * 类路径
	 * @var string
	 */
	protected $path = '';

	/**
	 * 类的存储空间
	 * singleton/prototype/request/session
	 * @var string
	 */
	protected $scope = '';

	/**
	 * 类自定义的初始化方法
	 * @var string
	 */
	protected $factoryMethod = '';

	/**
	 * 类设置属性之后的调用处理操作
	 * @var string
	 */
	protected $initMethod = '';

	/**
	 * 构造参数定义
	 * @var array
	 */
	protected $constructArgs = array();

	/**
	 * 类属性定义
	 * @var array
	 */
	protected $propertys = array();

	/**
	 * 类定义
	 * @var array
	 */
	protected $classDefinition;

	/**
	 * @var prototype
	 */
	private $prototype = null;

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
		if (($instance = $this->executeFactoryMethod($args)) != null) return $instance;
		switch ($this->scope) {
			case 'prototype':
				return $this->createInstanceWithPrototype($factory, $args);
			case 'request':
				return $this->createInstanceWithRequest($factory, $args);
			case 'application':
				return $this->createInstanceWithApplication($factory, $args);
			default:
				return $this->createInstanceWithSingleton($factory, $args);
		}
	}

	/**
	 * @param IWindFactory $factory
	 * @param array $args
	 * @return NULL|object
	 */
	protected function createInstanceWithApplication($factory, $args) {
		if (!isset($factory->application)) return null;
		if (null === $factory->application->getAttribute($this->getAlias())) {
			$factory->application->setAttribute($this->getAlias(), $this->createInstanceWithPrototype($factory, $args));
		}
		return $factory->application->getAttribute($this->getAlias());
	}

	/**
	 * @param IWindFactory $factory
	 * @param array $args
	 * @return NULL|object
	 */
	protected function createInstanceWithRequest($factory, $args) {
		if (!isset($factory->request)) return null;
		if (null === $factory->request->getAttribute($this->getAlias(), null)) {
			$factory->request->setAttribute($this->getAlias(), $this->createInstanceWithPrototype($factory, $args));
		}
		return $factory->request->getAttribute($this->getAlias());
	}

	/**
	 * @param IWindFactory $factory
	 * @param array $args
	 * @return NULL|object
	 */
	protected function createInstanceWithPrototype($factory, $args) {
		if ($this->prototype === null) {
			$instance = $this->createInstance($factory, $args);
			$this->setProperties($this->getPropertys(), $factory, $instance);
			$this->executeInitMethod($instance);
			$this->setPrototype($instance);
		}
		return clone $this->prototype;
	}

	/**
	 * @param IWindFactory $factory
	 * @param array $args
	 * @return NULL|object
	 */
	protected function createInstanceWithSingleton($factory, $args) {
		if (!isset($this->instance)) {
			$this->instance = $this->createInstanceWithPrototype($factory, $args);
		}
		return $this->instance;
	}

	/**
	 * 
	 * @modified xiaoxia.xu
	 * @param AbstractWindFactory $factory
	 * @param array $args
	 */
	protected function createInstance($factory, $args = array()) {
		$instance = null;
		if (empty($args)) {
			$args = $this->setProperties($this->getConstructArgs(), $factory);
		}
		$instance = $factory->createInstance($this->getClassName(), $args);
		
		return $instance;
	}

	/**
	 * Enter description here ...
	 * @param instance
	 * @param proxy
	 */
	private function setPrototype($instance) {
		if ($this->prototype === null) {
			if (($instance instanceof WindModule) && (null !== ($proxy = $instance->getClassProxy())))
				$this->prototype = $proxy;
			else
				$this->prototype = $instance;
		}
	}

	/**
	 * 执行用户配置的初始化操作
	 * 
	 * @author xiaoxia.xu
	 * @param object $instance
	 */
	private function executeInitMethod($instance) {
		if (!($initMethod = $this->getInitMethod())) return;
		if (!in_array($initMethod, get_class_methods($instance))) throw new WindException(get_class($instance) . '->' . $initMethod, WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		$instance->$initMethod();
	}

	/**
	 * 将类实例的依赖注入到类实例中
	 * @param array $subDefinitions | 类定义
	 * @param AbstractWindFactory $factory | 抽象的类工厂
	 * @param object  $instance | 类实例
	 */
	private function setProperties($subDefinitions, $factory, $instance = null) {
		$_temp = array();
		foreach ($subDefinitions as $key => $subDefinition) {
			if (isset($subDefinition[self::REF]))
				$_temp[$key] = $factory->getInstance($subDefinition[self::REF]);
			elseif (isset($subDefinition[self::VALUE]))
				$_temp[$key] = $subDefinition[self::VALUE];
			if ($instance !== null) {
				call_user_func_array(array($instance, 'set' . ucfirst(trim($key, '_'))), array($_temp[$key]));
			}
		}
		return $_temp;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param array $args
	 * @throws WindException
	 * @return NULL|mixed
	 */
	private function executeFactoryMethod($args) {
		if (!($factoryMethod = $this->getFactoryMethod())) return null;
		if (!in_array($factoryMethod, get_class_methods($this->getClassName()))) throw new WindException($this->getClassName() . '->' . $factoryMethod, WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		return call_user_func_array(array($this->getClassName(), $factoryMethod), $args);
	}

	/**
	 * 初始化类定义
	 * @param array $classDefinition
	 */
	protected function init($classDefinition) {
		if (empty($classDefinition)) return;
		if (isset($classDefinition[self::NAME])) $this->setAlias($classDefinition[self::NAME]);
		if (isset($classDefinition[self::PATH])) $this->setPath($classDefinition[self::PATH]);
		if (isset($classDefinition[self::SCOPE])) $this->setScope($classDefinition[self::SCOPE]);
		if (isset($classDefinition[self::FACTORY_METHOD])) $this->setFactoryMethod($classDefinition[self::FACTORY_METHOD]);
		if (isset($classDefinition[self::INIT_METHOD])) $this->setInitMethod($classDefinition[self::INIT_METHOD]);
		if (isset($classDefinition[self::PROPERTIES])) $this->setPropertys($classDefinition[self::PROPERTIES]);
		if (isset($classDefinition[self::CONSTRUCTOR_ARG])) $this->setConstructArgs($classDefinition[self::CONSTRUCTOR_ARG]);
		$this->setClassDefinition($classDefinition);
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
		return $this->propertys;
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
	public function setPropertys($propertys) {
		if (is_array($propertys) && !empty($propertys)) $this->propertys += $propertys;
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

}