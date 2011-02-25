<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
L::import('WIND:component.cache.strategy.AbstractWindCache');
L::import('WIND:component.utility.WindUMemcache');
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
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expire = 0, IWindCacheDependency $denpendency = null) {
		return $this->memcache->set($this->buildSecurityKey($key), $this->storeData($value, $expire, $denpendency), $this->compress, (int) $expire);
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->memcache->get($this->buildSecurityKey($key), $this->compress)));
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#delete()
	 */
	public function delete($key) {
		return $this->memcache->delete($this->buildSecurityKey($key));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		return $this->memcache->flush();
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
	 * 
	 * )
	 * @see wind/core/WindComponentModule#setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$_config = is_object($config) ? $config->getConfig() : $config;
		if (!isset($_config[self::SERVERCONFIG]) || !is_array($_config[self::SERVERCONFIG])) {
			throw new WindException('The server config is not exist');
		}
		$this->memcache->setServers($_config[self::SERVERCONFIG]);
		$this->compress = isset($_config[self::COMPRESS]) ? MEMCACHE_COMPRESSED : 0;
	}

}