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

	const EVENT_TYPE_METHOD = 'method';

	/**
	 * Enter description here ...
	 *
	 * @var unknown_type
	 * @deprecated
	 */
	const EVENT_TYPE_SETTER = 'setter';

	/**
	 * Enter description here ...
	 *
	 * @var unknown_type
	 * @deprecated
	 */
	const EVENT_TYPE_GETTER = 'getter';

	/**
	 * Enter description here ...
	 * @return ReflectionClass
	 */
	public function _getReflection();

	/**
	 * Enter description here ...
	 */
	public function _getInstance();

	/**
	 * Enter description here ...
	 * 
	 * @param string $event
	 * @param Object $listener
	 * @param string $type
	 */
	public function registerEventListener($event, $listener, $type = self::EVENT_TYPE_METHOD);

}

?>