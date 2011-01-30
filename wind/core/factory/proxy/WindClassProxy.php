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

	private $_attribute = array();

	protected $_className = '';

	protected $_classPath = '';

	protected $_reflection = null;

	protected $_instance = null;

	protected $_listener = array();

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
	public function registerEventListener($event, $listener, $type = self::EVENT_TYPE_METHOD) {
		//TODO add Logger
		if (!in_array($type, array(self::EVENT_TYPE_METHOD, self::EVENT_TYPE_GETTER, self::EVENT_TYPE_SETTER))) {
			throw new WindException('incorrect registerType ' . $type);
		}
		if (!isset($this->_listener[$type])) $this->_listener[$type] = array();
		if (!isset($this->_listener[$type][$event])) $this->_listener[$type][$event] = array();
		array_push($this->_listener[$type][$event], $listener);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $propertyName
	 * @param $value
	 */
	public function _setProperty($propertyName, $value) {
		$this->_getInstance()->$propertyName = $value;
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
		$property = $this->_getReflection()->getProperty($propertyName);
		if (!$property || !$property->isPublic()) {
			throw new WindException('undefined property name. ');
		}
		$listeners = $this->_getListenerByType(self::EVENT_TYPE_SETTER, $propertyName);
		$interceptorChain = $this->_getInterceptorChain($propertyName);
		$interceptorChain->addInterceptors($listeners);
		$interceptorChain->setCallBack(array($this, '_setProperty'), array($propertyName, $value));
		return $interceptorChain->getHandler()->handle($value);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $propertyName
	 */
	public function _getProperty($propertyName) {
		return $this->_getInstance()->$propertyName;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param unknown_type $propertyName
	 * @deprecated
	 */
	public function __get($propertyName) {
		$property = $this->_getReflection()->getProperty($propertyName);
		if (!$property || !$property->isPublic()) {
			throw new WindException('undefined property name. ');
		}
		$listeners = $this->_getListenerByType(self::EVENT_TYPE_GETTER, $propertyName);
		$interceptorChain = $this->_getInterceptorChain($propertyName);
		$interceptorChain->addInterceptors($listeners);
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
		$method = $this->_getReflection()->getMethod($methodName);
		if (!$method || !$method->isPublic()) {
			throw new WindException('undefined method name in ' . $this->_getReflection()->getName());
		}
		$listeners = $this->_getListenerByType(self::EVENT_TYPE_METHOD, $methodName);
		
		$interceptorChain = $this->_getInterceptorChain($methodName);
		$interceptorChain->addInterceptors($listeners);
		$interceptorChain->setCallBack(array($this->_getInstance(), $methodName), $args);
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
			$this->_setClassName(get_class($targetObject));
			$this->_instance = $targetObject;
		} elseif (is_string($targetObject) && !empty($targetObject)) {
			if (!class_exists($targetObject)) throw new WindException($targetObject, WindException::ERROR_CLASS_NOT_EXIST);
			$this->_setClassName($targetObject);
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
	private function _getInterceptorChain($event = '') {
		$interceptorChain = call_user_func_array(array(new ReflectionClass(L::import($this->_interceptorChain)), 
			'newInstance'), array());
		if ($interceptorChain instanceof WindComponentModule) {
			$interceptorChain->setAttribute($this->_getAttribute());
			$interceptorChain->setAttribute('instance', $this->_getInstance());
			$interceptorChain->setAttribute('event', array($this->_getClassName(), $event));
		}
		return $interceptorChain;
	}

	/**
	 * Enter description here ...
	 */
	private function _getListenerByType($type, $subType) {
		$listener = array();
		if (isset($this->_listener[$type]) && isset($this->_listener[$type][$subType])) {
			$listener = $this->_listener[$type][$subType];
		}
		return $listener;
	}

	/**
	 * Enter description here ...
	 */
	private function _getAttribute($alias = '') {
		if ($alias === '')
			return $this->_attribute;
		else
			return isset($this->_attribute[$alias]) ? $this->_attribute[$alias] : null;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $alias
	 * @param object $object
	 */
	public function _setAttribute($alias, $object = null) {
		if (is_array($alias))
			$this->_attribute += $alias;
		elseif (is_string($alias))
			$this->_attribute[$alias] = $object;
	}

	/* (non-PHPdoc)
	 * @see IWindClassProxy::_getInstance()
	 */
	public function _getInstance() {
		return $this->_instance;
	}

	/* (non-PHPdoc)
	 * @see IWindClassProxy::_getReflection()
	 */
	public function _getReflection() {
		if ($this->_reflection instanceof ReflectionClass)
			return $this->_reflection;
		else
			throw new WindException(get_class($this) . '->_reflection, ' . gettype($this->_reflection), WindException::ERROR_CLASS_TYPE_ERROR);
	}

	/**
	 * @return the $_className
	 */
	public function _getClassName() {
		return $this->_className;
	}

	/**
	 * @return the $_classPath
	 */
	public function _getClassPath() {
		return $this->_classPath;
	}

	/**
	 * @param string $className
	 */
	public function _setClassName($className) {
		$this->_className = $className;
	}

	/**
	 * @param string $classPath
	 */
	public function _setClassPath($classPath) {
		$this->_setClassName(L::import($classPath));
		$this->_classPath = $classPath;
	}

}

?>