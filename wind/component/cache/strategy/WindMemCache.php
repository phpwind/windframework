<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
L::import('WIND:component.cache.strategy.AbstractWindCache');
L::import('WIND:component.cache.operator.WindUMemcache');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMemCache extends AbstractWindCache {

	/**
	 * @var WindMemcache memcache缓存操作句柄
	 */
	protected $memcache = null;

	/**
	 * @var int 是否对缓存采取压缩存储
	 */
	protected $compress = 0;

	//配置信息
	/**
	 * @var int 是否对缓存进行压缩，如果缓存的值较大，可进行压缩
	 */
	const COMPRESS = 'compress';

	/**
	 * @var string 取得memcache配置项
	 */
	const SERVERCONFIG = 'servers';

	public function __construct() {
		$this->memcache = new WindUMemcache();
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::set()
	 */
	public function set($key, $value, $expire = 0, IWindCacheDependency $denpendency = null) {
		$expire = null === $expire ? $this->getExpire() : $expire;
		return $this->memcache->set($this->buildSecurityKey($key), $this->storeData($value, $expire, $denpendency), $this->compress, (int) $expire);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::get()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->memcache->get($this->buildSecurityKey($key), $this->compress)));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::delete()
	 */
	public function delete($key) {
		return $this->memcache->delete($this->buildSecurityKey($key));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::clear()
	 */
	public function clear() {
		return $this->memcache->flush();
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::clearByType()
	 */
	public function clearByType($key, $type) {

	}

	/* 
	 * @example $config = array(
	 * array(
	 * 'host'=>'localhost',
	 * 'port'=>11211
	 * 'pconn'=>true
	 * ),
	 * array(
	 * 'host'=>'localhost',
	 * 'port'=>11212
	 * 'pconn'=>false
	 * ),
	 * 'compress'=>true,
	 * 'security'=>'1x2aao@'
	 * 'prefix'=>'phpwind'
	 * )
	 * @see wind/core/WindComponentModule#setConfig()
	 */
	/* (non-PHPdoc)
	 * @see WindComponentModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$_config = is_object($config) ? $config->getConfig() : $config;
		$this->memcache->setServers($_config[self::SERVERCONFIG]);
		$this->compress = isset($_config[self::COMPRESS]) ? MEMCACHE_COMPRESSED : 0;
	}

}