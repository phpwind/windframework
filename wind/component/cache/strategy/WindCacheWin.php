<?php
/**
 * @author Su Qian <weihu@alibaba-inc.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.cache.AbstractWindCache');
L::import('WIND:component.cache.operator.WindWinCache');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 */

class WindCacheWin extends AbstractWindCache {
	/**
	 * @var WindWinCache
	 */
	protected $wincache = null;
	
	public function __construct(){
		$this->wincache = new WindWinCache();
	}
	/* 
	 * @see AbstractWindCache#set()
	 */
	public function set($key, $value, $expire = null, IWindCacheDependency $denpendency = null) {
		$expire = null === $expire  ? $this->getExpire() : $expire;
		return $this->wincache->set($this->buildSecurityKey($key), $this->storeData($value, $expire, $denpendency), $expire);
	}
	/* 
	 * @see AbstractWindCache#fetch()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->wincache->get($this->buildSecurityKey($key))));
	}
	
	/* 
	 * @see AbstractWindCache#delete()
	 */
	public function delete($key) {
		return $this->wincache->delete($this->buildSecurityKey($key));
	}
	
	/* 
	 * @see AbstractWindCache#clear()
	 */
	public function clear() {
		return $this->wincache->flush();
	}
	
}