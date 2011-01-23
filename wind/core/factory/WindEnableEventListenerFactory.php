<?php

L::import('WIND:core.factory.WindFactory');
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindEnableEventListenerFactory extends WindFactory {

	/* (non-PHPdoc)
	 * @see WindFactory::createInstance()
	 */
	public function createInstance($className, $args = array()) {
		$object = parent::createInstance($className, $args);
		$classProxy = '';
		if ($object instanceof WindModule) {
			$classProxy = $object->getClassProxy();
		}
		if ($classProxy instanceof WindClassProxy) {
			return $classProxy;
		}
		return $object;
	}

	/* (non-PHPdoc)
	 * @see WindFactory::create()
	 */
	public function create() {
		// TODO Auto-generated method stub
	

	}
}

?>