<?php

L::import('WIND:core.filter.WindHandlerInterceptor');

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindDaoCacheListener extends WindHandlerInterceptor {

	private $daoObject = null;
	
	/**
	 * Enter description here ...
	 * @param WindView $windView
	 */
	function __construct($instance) {
		$this->daoObject = $instance;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		$args = func_get_args();
		$cacheHandler = $this->daoObject->getCacheHandler();/* @var $cacheHandler IWindCache */
		$result = $cacheHandler->batchFetch($args);
		return 1 < count($args) ? empty($result) ? null : $result : $result[$args[0]];
		
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		$args = func_get_args();
		$cacheHandler = $this->daoObject->getCacheHandler();
		$cacheHandler->add($args[0],$args[1],$args[2]);
	}
	
	

}

?>