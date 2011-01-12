<?php

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindClassProxy {

	/**
	 * Enter description here ...
	 */
	public function getInstance();

	/**
	 * Enter description here ...
	 */
	public function getReflection();

	/**
	 * Enter description here ...
	 */
	public function getClassPrototype();

	/**
	 * Enter description here ...
	 * 
	 * @param string $method
	 * @param IWindHandlerInterceptor $interceptor
	 */
	public function registerEvent(string $method, IWindHandlerInterceptor $interceptor, string $registerType);

}

?>