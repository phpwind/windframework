<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 * tags
 */
Wind::import('WIND:component.cache.AbstractWindCache');
Wind::import('WIND:component.cache.operator.WindMemcache');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 */
class WindCacheMem extends AbstractWindCache {

	/**
	 * memcache缓存操作句柄
	 * @var WindMemcache 
	 */
	protected $memcache = null;

	/**
	 * 是否对缓存采取压缩存储
	 * @var int 
	 */
	protected $compress = 0;

	//配置信息
	/**
	 * 是否对缓存进行压缩，如果缓存的值较大，可进行压缩
	 * @var int 
	 */
	const COMPRESS = 'compress';

	/**
	 * 取得memcache配置项
	 * @var string 
	 */
	const SERVERCONFIG = 'servers';

	public function __construct() {
		$this->memcache = new WindMemcache();
	}

	/* 
	 * @see AbstractWindCache::set()
	 */
	public function set($key, $value, $expire = null, IWindCacheDependency $denpendency = null) {
		$expire = null === $expire ? $this->getExpire() : $expire;
		return $this->memcache->set($this->buildSecurityKey($key), $this->storeData($value, $expire, $denpendency), $this->compress, (int) $expire);
	}

	/* 
	 * @see AbstractWindCache::get()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->memcache->get($this->buildSecurityKey($key), $this->compress)));
	}

	/* 
	 * @see AbstractWindCache::delete()
	 */
	public function delete($key) {
		return $this->memcache->delete($this->buildSecurityKey($key));
	}

	/* 
	 * @see AbstractWindCache::clear()
	 */
	public function clear() {
		return $this->memcache->flush();
	}

	/* 
	 * @see WindComponentModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->memcache->setServers($this->getServersConfig());
		$this->compress = $this->getCompress();
	}

	/* 
	 * @see AbstractWindCache#getCacheHandler()
	 * @return WindMemcache
	 */
	public function getCacheHandler() {
		return $this->memcache;
	}

	/**
	 * 取得缓存配置
	 * @return array|mixed
	 */
	protected function getServersConfig($name = '', $subName = '') {
		$servers = $this->getConfig(self::SERVERCONFIG);
		if (empty($name)) {
			return $servers;
		}
		if (empty($subName)) {
			return isset($servers[$name]) ? $servers[$name] : $servers;
		}
		return isset($servers[$name][$subName]) ? $servers[$name][$subName] : $servers[$name];
	}

	protected function getCompress() {
		$compress = $this->getConfig(self::COMPRESS, WIND_CONFIG_VALUE, '', 0);
		return $compress ? MEMCACHE_COMPRESSED : 0;
	}

}