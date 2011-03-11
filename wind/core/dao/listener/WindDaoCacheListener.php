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

	private $caches = array();

	/**
	 * @param AbstractWindDao $instance
	 */
	function __construct($instance) {
		$this->daoObject = $instance;
		$this->caches = $this->cookCache($instance->getCacheMethods());
	}

	/*
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		if (in_array($this->event[1], $this->caches['clear'])) return null;
		
		/* @var $cacheHandler AbstractWindCache */
		$cacheHandler = $this->daoObject->getCacheHandler();
		list($type, $key) = $this->generateKey(func_get_args());
		if ($cacheHandler instanceof WindFileCache) $cacheHandler->setCacheType($type);
		$result = $cacheHandler->get($key);
		return empty($result) ? null : $result;
	}

	/* 
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		/* @var $cacheHandler AbstractWindCache */
		$cacheHandler = $this->daoObject->getCacheHandler();
		list($type, $key) = $this->generateKey(func_get_args());
		if ($cacheHandler instanceof WindFileCache) $cacheHandler->setCacheType($type);
		
		if (in_array($this->event[1], $this->caches['clear'])) {
			$cacheHandler->clearByType($key, $type);
		} else
			$cacheHandler->set($key, $this->result);
	}

	/**
	 * 返回缓存键值
	 * @param array $args
	 * @return string
	 */
	private function generateKey($args) {
		$_type = $this->event[0];
		$_key = $this->event[1] . '_' . (is_array($args[0]) ? $args[0][0] : $args[0]);
		return array($_type, $_key);
	}

	/**
	 * @param array $caches
	 */
	private function cookCache($caches) {
		$_tmp = array('cache' => array(), 'clear' => array());
		return array_merge($_tmp, $caches);
	}

}

?>