<?php
/**
 * 代理类，职责：
 * 通过代理类实现，对类的注册监听
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindClassProxy implements IWindClassProxy {
	private $_interceptorChain = 'WIND:core.filter.WindHandlerInterceptorChain';
	protected $_attributes = array();
	protected $_className = '';
	protected $_classPath = '';
	protected $_reflection = null;
	protected $_instance = null;
	protected $_listener = array();

	/**
	 * @param string|object $targetObj
	 */
	public function __construct($targetObj = null, $args = array()) {
		$this->initClassProxy($targetObj, $args);
	}

	/* (non-PHPdoc)
	 * @see IWindClassProxy::registerAspect()
	 */
	public function registerEventListener($event, $listener, $type = self::EVENT_TYPE_METHOD) {
		if (!in_array($type, array(self::EVENT_TYPE_METHOD, self::EVENT_TYPE_GETTER, self::EVENT_TYPE_SETTER))) {
			throw new WindException(
				'[core.factory.proxy.WindClassProxy.registerEventListener] Unsupport event type:' . $type, 
				WindException::ERROR_PARAMETER_TYPE_ERROR);
		}
		!isset($this->_listener[$type][$event]) && $this->_listener[$type][$event] = array();
		array_push($this->_listener[$type][$event], $listener);
	}

	/**
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
		if (empty($listeners)) return call_user_func_array(array($this, '_setProperty'), array($propertyName, $value));
		$interceptorChain = $this->_getInterceptorChain($propertyName);
		$interceptorChain->addInterceptors($listeners);
		$interceptorChain->setCallBack(array($this, '_setProperty'), array($propertyName, $value));
		return $interceptorChain->getHandler()->handle($value);
	}

	/**
	 * @param unknown_type $propertyName
	 * @deprecated
	 */
	public function __get($propertyName) {
		$property = $this->_getReflection()->getProperty($propertyName);
		if (!$property || !$property->isPublic()) {
			throw new WindException('undefined property name. ');
		}
		$listeners = $this->_getListenerByType(self::EVENT_TYPE_GETTER, $propertyName);
		if (empty($listeners)) return call_user_func_array(array($this, '_getProperty'), array($propertyName));
		$interceptorChain = $this->_getInterceptorChain($propertyName);
		$interceptorChain->addInterceptors($listeners);
		$interceptorChain->setCallBack(array($this, '_getProperty'), array($propertyName));
		return $interceptorChain->getHandler()->handle($propertyName);
	}

	/**
	 * @param string $methodName
	 * @param array $args
	 * @throws WindException
	 */
	public function __call($methodName, $args) {
		$listeners = $this->_getListenerByType(self::EVENT_TYPE_METHOD, $methodName);
		if (empty($listeners)) return call_user_func_array(array($this->_getInstance(), $methodName), (array) $args);
		$interceptorChain = $this->_getInterceptorChain($methodName);
		$interceptorChain->addInterceptors($listeners);
		$interceptorChain->setCallBack(array($this->_getInstance(), $methodName), $args);
		return call_user_func_array(array($interceptorChain->getHandler(), 'handle'), (array) $args);
	}

	/**
	 * 初始化类代理对象
	 * 
	 * @param string|object $targetObject
	 * @param array $args
	 * @throws WindException
	 */
	protected function initClassProxy($targetObject, $args = array()) {
		try {
			if (is_object($targetObject)) {
				$this->_setClassName(get_class($targetObject));
				$this->_instance = $targetObject;
			} elseif (is_string($targetObject) && !empty($targetObject)) {
				$_className = Wind::import($targetObject);
				$this->_setClassName($_className);
			} else
				throw new WindException($this->_className, WindException::ERROR_CLASS_NOT_EXIST);
			
			$types = array(self::EVENT_TYPE_METHOD, self::EVENT_TYPE_GETTER, self::EVENT_TYPE_SETTER);
			foreach ($types as $type) {
				$this->_listener[$type] = array();
			}
			$reflection = new ReflectionClass($this->_className);
			if ($reflection->isAbstract() || $reflection->isInterface()) {
				throw new WindException($this->_className, WindException::ERROR_CLASS_NOT_EXIST);
			}
			$this->_reflection = $reflection;
			if ($this->_instance !== null) return;
			$this->_instance = call_user_func_array(array($this->_reflection, 'newInstance'), $args);
		} catch (Exception $e) {
			Wind::log(
				'[core.factory.proxy.WindClassProxy.initClassProxy] Initialization proxy failed.' . $e->getMessage(), 
				WindLogger::LEVEL_DEBUG, 'wind.core');
		}
	}

	/**
	 * @param string $event
	 * @return
	 */
	private function _getInterceptorChain($event = '') {
		$interceptorChain = WindFactory::createInstance($this->_interceptorChain);
		if ($interceptorChain && $interceptorChain instanceof WindHandlerInterceptorChain) {
			$interceptorChain->setAttribute($this->_getAttribute());
			$interceptorChain->setAttribute('instance', $this->_getInstance());
			$interceptorChain->setAttribute('event', array($this->_getClassName(), $event));
			return $interceptorChain;
		} else
			throw new WindException('unable to create interceptorChain.');
	}

	/**
	 * 根据监听器类型，返回对象的监听器对象
	 * 
	 * @param string $type
	 * @param string $subType
	 */
	private function _getListenerByType($type, $subType) {
		$listener = array();
		if (isset($this->_listener[$type]) && isset($this->_listener[$type][$subType])) {
			$listener = $this->_listener[$type][$subType];
		}
		return $listener;
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
			throw new WindException(get_class($this) . '->_reflection, ' . gettype($this->_reflection), 
				WindException::ERROR_CLASS_TYPE_ERROR);
	}

	/**
	 * @return string
	 */
	public function _getClassName() {
		return $this->_className;
	}

	/**
	 * @return string
	 */
	public function _getClassPath() {
		return $this->_classPath;
	}

	/**
	 * @param string $className
	 * @return 
	 */
	public function _setClassName($className) {
		$this->_className = $className;
	}

	/**
	 * @param string $classPath
	 * @return 
	 */
	public function _setClassPath($classPath) {
		$this->_setClassName(Wind::import($classPath));
		$this->_classPath = $classPath;
	}

	/**
	 * @param string $propertyName
	 * @param $value
	 */
	public function _setProperty($propertyName, $value) {
		$this->_getInstance()->$propertyName = $value;
		return true;
	}

	/**
	 * @param string $propertyName
	 */
	public function _getProperty($propertyName) {
		return $this->_getInstance()->$propertyName;
	}

	/**
	 * 根据别名返回属性定义，别名为空时返回整个属性定义列表
	 * 
	 * @param string $alias
	 * @return object | array
	 */
	public function _getAttribute($alias = '') {
		if ($alias === '')
			return $this->_attributes;
		else
			return isset($this->_attributes[$alias]) ? $this->_attributes[$alias] : null;
	}

	/**
	 * 设置属性对象,设置的属性可以在listener中被访问到
	 * 
	 * @param string|array $alias
	 * @param object $object
	 */
	public function _setAttribute($alias, $object = null) {
		if (is_array($alias))
			$this->_attributes += $alias;
		elseif (is_string($alias))
			$this->_attributes[$alias] = $object;
	}
}
?>