<?php
/**
 * @author Su Qian <weihu@alibaba-inc.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.cache.AbstractWindCache');
L::import('WIND:component.cache.operator.WindApc');
/**
 * php加速器缓存
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 */
class WindCacheApc extends AbstractWindCache {
	
	/**
	 * @var WindApc
	 */
	protected $apc = null;
	
	public function __construct(){
		$this->apc = new WindApc();
	}
	/* 
	 * @see AbstractWindCache#set()
	 */
	public function set($key, $value, $expire = null, IWindCacheDependency $denpendency = null) {
		$expire = null === $expire  ? $this->getExpire() : $expire;echo $expire;
		return $this->apc->set($this->buildSecurityKey($key), $this->storeData($value, $expire, $denpendency), $expire);
	}
	
	/* 
	 * @see AbstractWindCache#get()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->apc->get($this->buildSecurityKey($key))));
	}
	/* 
	 * @see AbstractWindCache#delete()
	 */
	public function delete($key) {
		return $this->apc->delete($this->buildSecurityKey($key));
	}
	/**
	 * @see AbstractWindCache#clear()
	 */
	public function clear() {
		return $this->apc->flush();
	}
	
}