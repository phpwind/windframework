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
class WindClassProxy {
	const EVENT_TYPE_METHOD = 'method';
	const EVENT_TYPE_SETTER = 'setter';
	const EVENT_TYPE_GETTER = 'getter';
	
	private $_interceptorChain = 'WIND:core.filter.WindHandlerInterceptorChain';
	private $_interceptorChainObj = null;
	protected $_attributes = array();
	protected $_className = '';
	protected $_classPath = '';
	protected $_reflection = null;
	protected $_instance = null;
	protected $_listener = array();

	/**
	 * @param string|object $targetObj
	 */
	public function __construct($targetObject = null, $args = array()) {
		try {
			if (is_object($targetObject)) {
				$this->_setClassName(get_class($targetObject));
				$this->_instance = $targetObject;
			} elseif (is_string($targetObject) && !empty($targetObject)) {
				$this->_setClassPath($targetObject);
				$reflection = new ReflectionClass($this->_className);
				if ($reflection->isAbstract() || $reflection->isInterface()) {
					throw new WindException($this->_className, WindException::ERROR_CLASS_NOT_EXIST);
				}
				$this->_reflection = $reflection;
				$this->_instance = call_user_func_array(array($this->_reflection, 'newInstance'), $args);
			} else
				throw new WindException($this->_className, WindException::ERROR_CLASS_NOT_EXIST);
			
			$types = array(self::EVENT_TYPE_METHOD, self::EVENT_TYPE_GETTER, self::EVENT_TYPE_SETTER);
			foreach ($types as $type) {
				$this->_listener[$type] = array();
			}
			
			if ($this->_instance !== null) return;
		
		} catch (Exception $e) {
			Wind::log('[core.factory.proxy.WindClassProxy.initClassProxy] Initialization proxy failed.' . $e->getMessage(), WindLogger::LEVEL_DEBUG, 'wind.core');
		}
	
		//$this->initClassProxy($targetObj, $args);
	}

	/* (non-PHPdoc)
	 * @see IWindClassProxy::registerAspect()
	 */
	public function registerEventListener($event, $listener, $type = self::EVENT_TYPE_METHOD) {
		if (!in_array($type, array(self::EVENT_TYPE_METHOD, self::EVENT_TYPE_GETTER, self::EVENT_TYPE_SETTER))) {
			throw new WindException('[core.factory.proxy.WindClassProxy.registerEventListener] Unsupport event type:' . $type, WindException::ERROR_PARAMETER_TYPE_ERROR);
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
				$this->_setClassPath($targetObject);
				$reflection = new ReflectionClass($this->_className);
				if ($reflection->isAbstract() || $reflection->isInterface()) {
					throw new WindException($this->_className, WindException::ERROR_CLASS_NOT_EXIST);
				}
				$this->_reflection = $reflection;
				$this->_instance = call_user_func_array(array($this->_reflection, 'newInstance'), $args);
			} else
				throw new WindException($this->_className, WindException::ERROR_CLASS_NOT_EXIST);
			
			$types = array(self::EVENT_TYPE_METHOD, self::EVENT_TYPE_GETTER, self::EVENT_TYPE_SETTER);
			foreach ($types as $type) {
				$this->_listener[$type] = array();
			}
		
		} catch (Exception $e) {
			Wind::log('[core.factory.proxy.WindClassProxy.initClassProxy] Initialization proxy failed.' . $e->getMessage(), WindLogger::LEVEL_DEBUG, 'wind.core');
		}
	}

	/**
	 * @param string $event
	 * @return
	 */
	private function _getInterceptorChain($event = '') {
		if (null === $this->_interceptorChainObj) {
			$chain = Wind::import($this->_interceptorChain);
			$interceptorChain = WindFactory::createInstance($chain);
			if ($interceptorChain && $interceptorChain instanceof WindHandlerInterceptorChain) {
				$this->_interceptorChainObj = $interceptorChain;
			} else
				throw new WindException('[core.factory.proxy.WindClassProxy._getInterceptorChain] Unable to create interceptorChain.');
		}
		$this->_interceptorChainObj->reset();
		return $this->_interceptorChainObj;
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
	}

	/**
	 * @param string $propertyName
	 */
	public function _getProperty($propertyName) {
		return $this->_getInstance()->$propertyName;
	}
}
?>