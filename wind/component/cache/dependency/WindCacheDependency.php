<?php
/**
 * 缓存依赖基类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
Wind::import('WIND:component.cache.IWindCacheDependency');
abstract class WindCacheDependency implements IWindCacheDependency {
	/**
	 * @var mixed 缓存依赖控制者
	 */
	protected $dependent = '';
	
	protected $data = '';
	
	/**
	 * @var string 依赖项的上次更改时间
	 */
	protected $lastModified;
	/**
	 * @var IWindCache 缓存创建者
	 */
	protected $cache = null;

	public function __construct($dependent = null) {
		$this->dependent = $dependent;
	}

	/*
	 * @see wind/component/cache/base/IWindCacheDependency#injectDependent()
	 */
	public function injectDependent() {
		$this->lastModified = time();
	}

	/*
	 * @see wind/component/cache/base/IWindCacheDependency#hasChanged()
	 */
	public function hasChanged() {
		return $this->data != $this->notifyDependencyChanged();
	}

	/*
	 * @see wind/component/cache/base/IWindCacheDependency#getLastModified()
	 */
	public function getLastModified() {
		return $this->lastModified;
	}

	/**
	 * 是否有变化
	 * @return NULL
	 */
	protected abstract function notifyDependencyChanged();

}