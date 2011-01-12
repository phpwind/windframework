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
	
	protected $_aspects = array();
	
	protected $_setPropertyAspects = array();
	
	protected $_getPropertyAspects = array();
	
	/**
	 * Enter description here ...
	 * 
	 * @param string|object $targetObj
	 */
	public function __construct($targetObj) {

	}
	
	/* (non-PHPdoc)
	 * @see IWindClassProxy::registerAspect()
	 */
	public function registerAspect(string $method, IWindHandlerInterceptor $interceptor) {

	}
	
	/* (non-PHPdoc)
	 * @see IWindClassProxy::registerSetPropertyAspect()
	 */
	public function registerSetPropertyAspect(string $property, IWindHandlerInterceptor $interceptor) {

	}
	
	/* (non-PHPdoc)
	 * @see IWindClassProxy::registerGetPropertyAspect()
	 */
	public function registerGetPropertyAspect(string $property, IWindHandlerInterceptor $interceptor) {

	}
	
	/* (non-PHPdoc)
	 * @see IWindClassProxy::getClassPrototype()
	 */
	public function getClassPrototype() {
		// TODO Auto-generated method stub
	

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

}

?>