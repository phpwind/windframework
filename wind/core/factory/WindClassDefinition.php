<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-31
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.base.WindModule');
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
class WindClassDefinition extends WindEnableValidateModule {

	/* 配置信息定义 */
	const NAME = 'name';

	const PATH = 'path';

	const FACTORY_METHOD = 'factory-method';

	const INIT_METHOD = 'init-method';

	const SCOPE = 'scope';

	const PROXY = 'proxy';

	const PROPERTIES = 'properties';

	const CONSTRUCTOR_ARG = 'constructor-arg';

	const REF = 'ref';

	const VALUE = 'value';

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
	 * 类代理对象定义
	 *
	 * @var string
	 */
	protected $proxy = '';

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
		L::import('WIND:component.validator.WindValidator');
		$this->registerValidator(new WindValidator());
		$this->init($classDefinition);
	}

	/**
	 * 通过对象工厂创建单例对象
	 * @param IWindFactory $factory
	 * @return instance|Ambigous <prototype, void, mixed>|NULL
	 */
	public function getInstance($factory, $args = array()) {
		switch ($this->scope) {
			case 'singleton':
				return $this->createInstanceWithSingleton($factory, $args);
			case 'prototype':
				return $this->createInstanceWithPrototype($factory, $args);
			case 'request':
				return $this->createInstanceWithRequest($factory, $args);
			case 'application':
				return $this->createInstanceWithApplication($factory, $args);
			default:
				return null;
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
		return $this->createInstance($factory, $args);
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
	 * @param AbstractWindFactory $factory
	 * @param array $args
	 */
	protected function createInstance($factory, $args = array()) {
		if ($this->prototype === null) {
			$instance = null;
			if (empty($args)) $args = $this->setProperties($this->getConstructArgs(), $factory);
			$contructArgs = $this->getConstructArgs();
			$instance = $factory->createInstance($this->getClassName(), $args);
			$this->setProxyForClass($instance);
			$this->setProperties($this->getPropertys(), $factory, $instance);
			$this->prototype = $instance;
		}
		return clone $this->prototype;
	}

	/**
	 * 为类设置代理
	 * 
	 * @param WindModule $instance
	 */
	protected function setProxyForClass($instance) {
		if (!$instance instanceof WindModule) return;
		$proxyClass = L::import($this->getProxy());
		if (class_exists($proxyClass)) $proxyClass = new $proxyClass();
		if ($proxyClass instanceof WindClassProxy) $instance->setClassProxy($proxyClass);
	}

	/**
	 * 将类实例的依赖注入到类实例中
	 * @param array $subDefinitions | 类定义
	 * @param AbstractWindFactory $factory | 抽象的类工厂
	 * @param object  $instance | 类实例
	 */
	private function setProperties($subDefinitions, $factory, $instance = null) {
		//TODO add check
		$_temp = array();
		foreach ($subDefinitions as $key => $subDefinition) {
			if (isset($subDefinition[self::REF]))
				$_temp[$key] = $factory->getInstance($subDefinition[self::REF]);
			elseif (isset($subDefinition[self::VALUE]))
				$_temp[$key] = $subDefinition[self::VALUE];
			if ($instance !== null && is_array($key)) {
				$instance->$key = $_temp[$key];
			}
		}
		return $_temp;
	}

	/**
	 * 返回配置对象
	 * validator : required/not-required
	 * @return multitype:multitype:string  
	 */
	protected function validateRules() {
		$rules[] = $this->buildValidateRule(self::NAME, 'isRequired', '');
		$rules[] = $this->buildValidateRule(self::PATH, 'isRequired', '');
		$rules[] = $this->buildValidateRule(self::SCOPE, 'isRequired', 'singleton');
		$rules[] = $this->buildValidateRule(self::INIT_METHOD, 'isRequired', '');
		$rules[] = $this->buildValidateRule(self::FACTORY_METHOD, 'isRequired', '');
		$rules[] = $this->buildValidateRule(self::PROXY, 'isRequired', '');
		$rules[] = $this->buildValidateRule(self::PROPERTIES, 'isRequired', array());
		$rules[] = $this->buildValidateRule(self::CONSTRUCTOR_ARG, 'isRequired', array());
		return $rules;
	}

	/**
	 * 初始化类定义
	 * @param array $classDefinition
	 */
	private function init($classDefinition) {
		$this->validate($classDefinition);
		$className = L::import($classDefinition[self::PATH]);
		if (!$className) throw new WindException($className, WindException::ERROR_CLASS_NOT_EXIST);
		
		$this->setClassName($className);
		$this->setAlias($classDefinition[self::NAME]);
		$this->setPath($classDefinition[self::PATH]);
		$this->setScope($classDefinition[self::SCOPE]);
		$this->setProxy($classDefinition[self::PROXY]);
		$this->setPropertys($classDefinition[self::PROPERTIES]);
		$this->setConstructArgs($classDefinition[self::CONSTRUCTOR_ARG]);
		$this->setClassDefinition($classDefinition);
	}

	/**
	 * @return the $className
	 */
	public function getClassName() {
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
	 * @param $className the $className to set
	 * @author Qiong Wu
	 */
	public function setClassName($className) {
		$this->className = $className;
	}

	/**
	 * @param $alias the $alias to set
	 * @author Qiong Wu
	 */
	public function setAlias($alias) {
		$this->alias = $alias;
	}

	/**
	 * @param $path the $path to set
	 * @author Qiong Wu
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @param $scope the $scope to set
	 * @author Qiong Wu
	 */
	public function setScope($scope) {
		$this->scope = $scope;
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
	 * @param $constructArgs the $constructArgs to set
	 * @author Qiong Wu
	 */
	public function setConstructArgs($constructArgs) {
		if (is_array($constructArgs) && !empty($constructArgs)) $this->constructArgs = $constructArgs;
	}

	/**
	 * @param $propertys the $propertys to set
	 * @author Qiong Wu
	 */
	public function setPropertys($propertys) {
		if (is_array($propertys) && !empty($propertys)) $this->propertys = $propertys;
	}

	/**
	 * @param $classDefinition the $classDefinition to set
	 * @author Qiong Wu
	 */
	public function setClassDefinition($classDefinition) {
		$this->classDefinition = $classDefinition;
	}

	/**
	 * @return the $proxy
	 */
	public function getProxy() {
		return $this->proxy;
	}

	/**
	 * @param string $proxy
	 */
	public function setProxy($proxy) {
		$this->proxy = $proxy;
	}

}