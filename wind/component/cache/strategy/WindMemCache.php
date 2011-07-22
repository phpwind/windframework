<?php
/**
 * @author xiaoxiao <x_824@sina.com>  2011-7-18
 * @link http://www.cnblogs.com/xiaoyaoxia/
 * @copyright Copyright &copy; 2011-2012  xiaoxiao
 * @license
 * @package
 */
Wind::import('WIND:component.cache.AbstractWindCache');

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

	//配置信息
	const MEMCACHE = 'memcache';
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
	
	const HOST = 'host';

	const PORT = 'port';

	const PCONNECT = 'pconn';

	const WEIGHT = 'weight';

	const TIMEOUT = 'timeout';

	const RETRY = 'retry';

	const STATUS = 'status';

	const FCALLBACK = 'fcallback';

	public function __construct() {
		if (!extension_loaded('Memcache')) {
			throw new WindException('WindMemCache requires PHP `Memcache` extension to be loaded !');
		}
		$this->memcache = new Memcache();
	}

	/* 
	 * @see AbstractWindCache::addValue()
	 */
	protected function addValue($key, $value, $expire = 0) {
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
		$this->setServers($this->getConfig(self::MEMCACHE, self::SERVERCONFIG));
		$this->compress = $this->getSubConfig($this->getConfig(self::MEMCACHE), self::COMPRESS, '', 0);
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
		if (!isset($server[self::HOST])) {
			throw new WindException('The memcache server ip address is not exist');
		}
		if (!isset($server[self::PORT])) {
			throw new WindException('The memcache server port is not exist');
		}
		$defaultServer = array(self::HOST => '', self::PORT => '', self::PCONNECT => true, self::WEIGHT => 1, 
			self::TIMEOUT => 15, self::RETRY => 15, self::STATUS => true, self::FCALLBACK => null);
		list($host, $port, $pconn, $weight, $timeout, $retry, $status, $fcallback) = array_values(array_merge($defaultServer, $server));
		$this->memcache->addServer($host, $port, $pconn, $weight, $timeout, $retry, $status, $fcallback);
	}
}