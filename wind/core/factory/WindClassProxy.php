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

	protected $_reflection = null;

	protected $_instance = null;

	protected $_events = array();

	private $_interceptorChain = 'WIND:core.web.WindHandlerInterceptorChain';

	/**
	 * Enter description here ...
	 * 
	 * @param string|object $targetObj
	 */
	public function __construct($className, $args = array()) {
		if (!class_exists($className)) {
			throw new WindException('unable to create instace for ' . $className . ', class is not exist.');
		}
		$this->_setReflection($className, $args);
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
		if ($events = $this->_getEventsByType(self::EVENT_TYPE_SETTER, $propertyName)) {
			$interceptorChain = call_user_func_array(array(new ReflectionClass(L::import($this->_interceptorChain)), 
				'newInstance'), array($events, array($this, '_setProperty'), array($propertyName, $value)));
			if (null !== ($handler = $interceptorChain->getHandler())) {
				return $handler->handle($value);
			}
		}
		return $this->_setProperty($propertyName, $value);
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
		if ($events = $this->_getEventsByType(self::EVENT_TYPE_GETTER, $propertyName)) {
			$interceptorChain = call_user_func_array(array(new ReflectionClass(L::import($this->_interceptorChain)), 
				'newInstance'), array($events, array($this, '_getProperty'), $propertyName));
			if (null !== ($handler = $interceptorChain->getHandler())) {
				return $handler->handle($this->_getProperty($propertyName));
			}
		}
		return $this->_getProperty($propertyName);
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
		if ($events = $this->_getEventsByType(self::EVENT_TYPE_METHOD, $methodName)) {
			$interceptorChain = call_user_func_array(array(new ReflectionClass(L::import($this->_interceptorChain)), 
				'newInstance'), array($events, array($this->getInstance(), $methodName), $args));
			if (null !== ($handler = $interceptorChain->getHandler())) {
				return call_user_func_array(array($handler, 'handle'), $args);
			}
		}
		return call_user_func_array(array($this->getInstance(), $methodName), $args);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param className
	 */
	private function _setReflection($className, $args) {
		$reflection = new ReflectionClass($className);
		if ($reflection->isAbstract() || $reflection->isInterface()) return;
		$this->_instance = call_user_func_array(array($reflection, 'newInstance'), $args);
		$this->_reflection = $reflection;
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

}

?>