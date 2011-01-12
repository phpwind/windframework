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

	const EVENT_TYPE_METHOD = 'method';

	const EVENT_TYPE_SETTER = 'setter';

	const EVENT_TYPE_GETTER = 'getter';

	protected $_reflection = null;

	protected $_prototype = null;

	protected $_event = array();

	/**
	 * Enter description here ...
	 * 
	 * @param string|object $targetObj
	 */
	public function __construct($className, $args) {
		if (!class_exists($className)) throw new WindException('unable to create instace for ' . $className . ', class is not exist.');
		$this->_setReflection($className);
	}

	/* (non-PHPdoc)
	 * @see IWindClassProxy::registerAspect()
	 */
	public function registerEvent(string $event, IWindHandlerInterceptor $interceptor, string $registerType) {
		//TODO add Logger
		if (!in_array($registerType, array(self::EVENT_TYPE_METHOD, self::EVENT_TYPE_GETTER, 
			self::EVENT_TYPE_SETTER))) {
			throw new WindException('incorrect registerType ' . $registerType);
		}
		if (!isset($this->_event[$registerType])) $this->_event[$registerType] = array();
		if (!isset($this->_event[$registerType][$event])) $this->_event[$registerType][$event] = array();
		if (!($interceptor instanceof IWindClassProxy)) {
			throw new WindException('unable to register event, class type error.');
		}
		array_push($this->_event[$registerType][$event], $interceptor);
	}

	/* (non-PHPdoc)
	 * @see IWindClassProxy::getClassPrototype()
	 */
	public function getClassPrototype() {
		return $this->_prototype;
	}

	/* (non-PHPdoc)
	 * @see IWindClassProxy::getInstance()
	 */
	public function getInstance() {
		// TODO Auto-generated method stub
	

	}

	/**
	 * @return the $_reflection
	 */
	public function getReflection() {
		return $this->_reflection;
	}

	public function __set() {

	}

	public function __get() {

	}

	public function __call() {

	}

	/**
	 * Enter description here ...
	 * 
	 * @param className
	 */
	private function _setReflection($className, $args) {
		$reflection = new ReflectionClass($className);
		if ($reflection->isAbstract() || $reflection->isInterface()) return;
		$this->_prototype = call_user_func_array(array($reflection, 'newInstance'), $args);
		$this->_reflection = $reflection;
	}

}

?>