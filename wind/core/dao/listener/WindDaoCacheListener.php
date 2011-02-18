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
		//TODO 读缓存
		print_r($this->event);
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		//TODO 写缓存
		print_r($this->event);
	}

}

?>