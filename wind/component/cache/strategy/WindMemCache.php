<?php
Wind::import('COM:cache.AbstractWindCache');
/**
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
			throw new WindException('WindMemCache requires PHP `Memcache` extension to be loaded !');
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
		$this->setServers($this->getConfig('servers'));
	}
	
	/**
	 * 设置配置信息
	 * 
	 * @param array $servers
	 * @example  
	 * $server = array(
	 * 	array(
	 * 		'host'=>'localhost',
	 * 		'port'=>11211
	 * 		'pconn'=>true
	 *  ),
	 *  array(
	 * 		'host'=>'localhost',
	 * 		'port'=>11212
	 * 		'pconn'=>false
	 *  )
	 * @throws WindException
	 */
	private function setServers($servers) {
		foreach ($servers as $server) {
			if (!is_array($server)) {
				throw new WindException('The memcache config is incorrect');
			}
			$this->setServer($server);
		}
	}

	/**
	 * 设置配置信息
	 * 
	 * @throws WindException
	 */
	private function setServer($server) {
		if (!isset($server['host'])) {
			throw new WindException('The memcache server ip address is not exist');
		}
		if (!isset($server['port'])) {
			throw new WindException('The memcache server port is not exist');
		}
		$defaultServer = array('host' => '', 'port' => '', 'pconn' => true, 'weight' => 1, 
			'timeout' => 15, 'retry' => 15, 'status' => true, 'fcallback' => null);
		list($host, $port, $pconn, $weight, $timeout, $retry, $status, $fcallback) = array_values(array_merge($defaultServer, $server));
		$this->memcache->addServer($host, $port, $pconn, $weight, $timeout, $retry, $status, $fcallback);
	}
}