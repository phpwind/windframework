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
class WindViewCacheListener extends WindHandlerInterceptor {

	private $windWind = null;

	/**
	 * Enter description here ...
	 * @param WindView $windView
	 */
	function __construct($windView) {
		$this->windWind = $windView;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle($templateName = '') {
		//TODO 读缓存
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle($templateName = '') {
		//TODO 写缓存
	}
}

?>