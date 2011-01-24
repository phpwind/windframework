<?php

L::import('WIND:core.factory.IWindClassProxy');

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindClassProxy implements IWindClassProxy {

	protected $_className = '';

	protected $_classPath = '';

	protected $_reflection = null;

	protected $_instance = null;

	protected $_events = array();

	private $_interceptorChain = 'WIND:core.web.WindHandlerInterceptorChain';

	/**
	 * Enter description here ...
	 * 
	 * @param string|object $targetObj
	 */
	public function __construct($targetObj = null, $args = array()) {
		$this->initClassProxy($targetObj, $args);
	}

	/* (non-PHPdoc)
	 * @see IWindClassProxy::registerAspect()
	 */
	public function registerEventListener($event, $listener, $type) {
		//TODO add Logger
		if (!in_array($type, array(self::EVENT_TYPE_METHOD, self::EVENT_TYPE_GETTER, self::EVENT_TYPE_SETTER))) {
			throw new WindException('incorrect registerType ' . $type);
		}
		if (!isset($this->_events[$type])) $this->_events[$type] = array();
		if (!isset($this->_events[$type][$event])) $this->_events[$type][$event] = array();
		array_push($this->_events[$type][$event], $listener);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $propertyName
	 * @param $value
	 */
	public function _setProperty($propertyName, $value) {
		$this->getInstance()->$propertyName = $value;
		return true;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $propertyName
	 * @param  $value
	 * @throws WindException
	 * @deprecated
	 */
	public function __set($propertyName, $value) {
		$property = $this->getReflection()->getProperty($propertyName);
		if (!$property || !$property->isPublic()) {
			throw new WindException('undefined property name. ');
		}
		$events = $this->_getEventsByType(self::EVENT_TYPE_SETTER, $propertyName);
		$interceptorChain = call_user_func_array(array(new ReflectionClass(L::import($this->_interceptorChain)), 
			'newInstance'), array());
		$interceptorChain->addInterceptors($events);
		$interceptorChain->setCallBack(array($this, '_setProperty'), array($propertyName, $value));
		return $interceptorChain->getHandler()->handle($value);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $propertyName
	 */
	public function _getProperty($propertyName) {
		return $this->getInstance()->$propertyName;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param unknown_type $propertyName
	 * @deprecated
	 */
	public function __get($propertyName) {
		$property = $this->getReflection()->getProperty($propertyName);
		if (!$property || !$property->isPublic()) {
			throw new WindException('undefined property name. ');
		}
		$events = $this->_getEventsByType(self::EVENT_TYPE_GETTER, $propertyName);
		$interceptorChain = call_user_func_array(array(new ReflectionClass(L::import($this->_interceptorChain)), 
			'newInstance'), array());
		$interceptorChain->addInterceptors($events);
		$interceptorChain->setCallBack(array($this, '_getProperty'), array($propertyName));
		return $interceptorChain->getHandler()->handle($propertyName);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $methodName
	 * @param array $args
	 * @throws WindException
	 */
	public function __call($methodName, $args) {
		$method = $this->getReflection()->getMethod($methodName);
		if (!$method || !$method->isPublic()) {
			throw new WindException('undefined method name in ' . $this->getReflection()->getName());
		}
		$events = $this->_getEventsByType(self::EVENT_TYPE_METHOD, $methodName);
		
		$interceptorChain = call_user_func_array(array(new ReflectionClass(L::import($this->_interceptorChain)), 
			'newInstance'), array());
		$interceptorChain->addInterceptors($events);
		$interceptorChain->setCallBack(array($this->getInstance(), $methodName), $args);
		return call_user_func_array(array($interceptorChain->getHandler(), 'handle'), $args);
	}

	/**
	 * 初始化类代理对象
	 * 
	 * @param string|object $targetObject
	 * @param array $args
	 * @throws WindException
	 */
	public function initClassProxy($targetObject, $args = array()) {
		if ($targetObject === null) return null;
		if (is_object($targetObject)) {
			$this->setClassName(get_class($targetObject));
			$this->_instance = $targetObject;
		} elseif (is_string($targetObject) && !empty($targetObject)) {
			if (!class_exists($targetObject)) throw new WindException($targetObject, WindException::ERROR_CLASS_NOT_EXIST);
			$this->setClassName($targetObject);
		}
		if ($this->_reflection === null) {
			$reflection = new ReflectionClass($this->_className);
			if ($reflection->isAbstract() || $reflection->isInterface()) return;
			$this->_reflection = $reflection;
		}
		if ($this->_instance === null) {
			$this->_instance = call_user_func_array(array($this->_reflection, 'newInstance'), $args);
		}
		return $this;
	}

	/**
	 * Enter description here ...
	 */
	private function _getEventsByType($type, $subType) {
		$events = array();
		if (isset($this->_events[$type]) && isset($this->_events[$type][$subType])) {
			$events = $this->_events[$type][$subType];
		}
		return $events;
	}

	/* (non-PHPdoc)
	 * @see IWindClassProxy::getClassPrototype()
	 */
	public function getInstance() {
		return $this->_instance;
	}

	/**
	 * @see IWindClassProxy::getReflection()
	 * @return ReflectionClass
	 */
	public function getReflection() {
		return $this->_reflection;
	}

	/**
	 * @return the $_className
	 */
	public function getClassName() {
		return $this->_className;
	}

	/**
	 * @return the $_classPath
	 */
	public function getClassPath() {
		return $this->_classPath;
	}

	/**
	 * @param string $className
	 */
	public function setClassName($className) {
		$this->_className = $className;
	}

	/**
	 * @param string $classPath
	 */
	public function setClassPath($classPath) {
		$this->setClassName(L::import($classPath));
		$this->_classPath = $classPath;
	}

}

?>