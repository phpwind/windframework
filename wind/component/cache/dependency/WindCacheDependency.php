<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 缓存依赖基类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindCacheDependency implements IWindCacheDependency{
	/**
	 * @var mixed 缓存依赖控制者
	 */
	protected $dependent = null;
	/**
	 * @var string 依赖项的上次更改时间
	 */
	protected $lastModified;
	/**
	 * @see wind/component/cache/base/IWindCacheDependency#injectDependent()
	 */
	
	public function __construct(){
		$this->injectDependent();
	}
	
	public  function injectDependent(){
		$this->dependent = $this->notifyDependencyChanged();
	}
	
	/**
	 * @see wind/component/cache/base/IWindCacheDependency#hasChanged()
	 */
	public function hasChanged(){
		return $this->dependent != $this->notifyDependencyChanged();
	}
	/**
	 * @see wind/component/cache/base/IWindCacheDependency#getLastModified()
	 */
	public function getLastModified(){
		return $this->lastModified;
	}
	

	/**
	 * @see wind/component/cache/base/IWindCacheDependency#setLastModified()
	 */
	public function setLastModified($lastModified){
		$this->lastModified = $lastModified;
	}
	
	/**
	 * 获取缓存依赖控制者
	 * @return mixed
	 */
	public function getDependent(){
		return $this->dependent;
	}
	
	/*	 
	 *  通知依赖项已更改。
	 */
	protected function notifyDependencyChanged(){
		return null;
	}
	
	
}