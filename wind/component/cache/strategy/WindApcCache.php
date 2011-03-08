<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.cache.strategy.AbstractWindCache');
L::import('WIND:component.cache.operator.WindApc');
/**
 * php加速器缓存
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindApcCache extends AbstractWindCache {
	
	/**
	 * @var WindApc
	 */
	protected $apc = null;
	
	public function __construct(){
		$this->apc = new WindApc();
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		return $this->apc->set($this->buildSecurityKey($key), $this->storeData($value, $expires, $denpendency), $expires);
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->apc->get($this->buildSecurityKey($key))));
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#delete()
	 */
	public function delete($key) {
		return $this->apc->delete($this->buildSecurityKey($key));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		$this->apc->flush();
	}
	
}