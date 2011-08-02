<?php

Wind::import('WIND:core.filter.WindHandlerInterceptor');

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindViewCacheListener extends WindHandlerInterceptor {

	private $windView = null;

	/**
	 * Enter description here ...
	 * @param WindView $windView
	 */
	public function __construct($windView) {
		$this->windView = $windView;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		$data = $this->windView->getViewCache()->get($this->getKey());
		$data = !$data ? null : $data;
		return $data;
	}
	
	/**
	 * 获得保存的key值
	 */
	private function getKey() {
		$host = Wind::getApp()->getRequest()->getHostInfo();
		$uri = Wind::getApp()->getRequest()->getRequestUri();
		return md5($host.$uri . '/' . $this->windView->getTemplateName() . '.' . $this->windView->getTemplateExt());
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		$args = func_get_args();
		$result = end($args);
		$cache = $this->windView->getViewCache();
		$cache->set($this->getKey(), $result);
	}
}

?>