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
	
	/*
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		$cacheHandler = $this->daoObject->getCacheHandler(); /* @var $cacheHandler IWindCache */
		if('WindDbCache' === get_class($cacheHandler)){
			$cacheHandler->setDbHandler($this->daoObject->getDbHandler());
		}
		$result = $cacheHandler->fetch($this->generateKey( func_get_args()));
		return empty($result) ? null : $result;
	
	}
	
	/* 
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		$cacheHandler = $this->daoObject->getCacheHandler();/* @var $cacheHandler IWindCache */
		$config = $cacheHandler->getConfig()->getConfig();
		$dependencyPath = $config[IWindCache::DEPENDENCY];
		$dependency = null;
		if($dependencyPath){
			$dependency = WindFactory::createInstance(L::import($dependencyPath));
		}
		$cacheHandler->add($this->generateKey(func_get_args()), $this->result, (int)$config[IWindCache::EXPIRES], $dependency);
	}
	
	public function generateKey($args){
		return $this->event[0].'-'.$this->event[1].'-'.(is_array($args[0]) ? $args[0][0] : $args[0]);
	}
	
}

?>