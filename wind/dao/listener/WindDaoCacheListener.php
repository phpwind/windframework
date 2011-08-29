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
class WindDaoCacheListener extends WindHandlerInterceptor {

	private $daoObject = null;

	/**
	 * @param AbstractWindDao $instance
	 */
	function __construct($instance) {
		$this->daoObject = $instance;
	}

	/*
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		/* @var $cacheHandler AbstractWindCache */
		$cacheHandler = $this->daoObject->getCacheHandler();
		$key = $this->generateKey(func_get_args());
		$result = $cacheHandler->get($key);
		return empty($result) ? null : $result;
	}

	/* 
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		/* @var $cacheHandler AbstractWindCache */
		$cacheHandler = $this->daoObject->getCacheHandler();
		$key = $this->generateKey(func_get_args());
		$cacheHandler->set($key, $this->result);
	}

	/**
	 * 返回缓存键值
	 * @param array $args
	 * @return string
	 */
	private function generateKey($args) {
		return $this->event[0] . '_' . $this->event[1] . '_' . (is_array($args[0]) ? $args[0][0] : $args[0]);
	}
}

?>