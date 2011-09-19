<?php
Wind::import('WIND:cache.AbstractWindCache');
/**
 * $server = array(
 * array(
 * 'host'=>'localhost',
 * 'port'=>11211
 * 'pconn'=>true
 * ),
 * array(
 * 'host'=>'localhost',
 * 'port'=>11212
 * 'pconn'=>false
 * )
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-7-26  xiaoxiao
 */
class WindMemCache extends AbstractWindCache {
	
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

	public function __construct() {
		if (!extension_loaded('Memcache')) {
			throw new WindCacheException('WindMemCache requires PHP `Memcache` extension to be loaded !');
		}
		$this->memcache = new Memcache();
	}

	/* 
	 * @see AbstractWindCache::setValue()
	 */
	protected function setValue($key, $value, $expire = 0) {
		return $this->memcache->set($key, $value, $this->compress, (int) $expire);
	}

	/* 
	 * @see AbstractWindCache::getValue()
	 */
	protected function getValue($key) {
		return $this->memcache->get($key);
	}

	/* 
	 * @see AbstractWindCache::deleteValue()
	 */
	protected function deleteValue($key) {
		return $this->memcache->delete($key);
	}

	/* 
	 * @see AbstractWindCache::clear()
	 */
	public function clear() {
		return $this->memcache->flush();
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->compress = $this->getConfig('compress', '', '0');
		$servers = $this->getConfig('servers', '', array());
		$defaultServer = array(
			'host' => '', 
			'port' => '', 
			'pconn' => true, 
			'weight' => 1, 
			'timeout' => 15, 
			'retry' => 15, 
			'status' => true, 
			'fcallback' => null);
		foreach ((array) $servers as $server) {
			if (!is_array($server)) throw new WindException('The memcache config is incorrect');
			if (!isset($server['host'])) throw new WindException('The memcache server ip address is not exist');
			if (!isset($server['port'])) throw new WindException('The memcache server port is not exist');
			call_user_func_array(array($this->memcache, 'addServer'), array_merge($defaultServer, $server));
		}
	}

}