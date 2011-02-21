<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
L::import ( 'WIND:component.cache.base.IWindCache' );
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMemcache extends WindComponentModule implements IWindCache {
	
	/**
	 * @var string key的安全码
	 */
	protected $securityCode = '';
	/**
	 * @var array memcache服务器配置
	 */
	protected $servers = array();
	/**
	 * @var Memcache memcache缓存操作句柄
	 */
	protected $cache = null;
	/**
	 * @var int 是否对缓存采取压缩存储
	 */
	protected $compress = 0;
	//配置信息
	const HOST = 'host';
	const PORT = 'port';
	const PCONN = 'pconn';
	const WEIGHT = 'weight';
	const TIMEOUT = 'timeout';
	const RETRY = 'retry';
	const STATUS = 'status';
	const FCALLBACK = 'fcallback';
	const SECURITY = 'security';
	const COMPRESS = 'compress';

	/**
	 * 
	 * @example array(
	 * 		array(
	 * 			'host'=>'localhost',
	 * 			'port'=>11211
	 * 			'pconn'=>true
	 *      ),
	 *      array(
	 *      	'host'=>'localhost',
	 * 			'port'=>11212
	 * 			'pconn'=>false
	 *      ),
	 *      'compress'=>true,
	 *      'security'=>'1x2aao@'
	 * 
	 * )
	 * @param array $servers memcache 服务器配置
	 */
	public function __construct(array $servers = array()) {
		$this->cache = new Memcache();
		if($servers){
			$this->setCacheConfig();
		}
	}
	
	public function setCacheConfig(array $servers=array()){
		$servers = $servers ? $servers : $this->getConfig()->getConfig();
		foreach ($servers as $server) {
			if(is_array($server)){
				$hasServer = true;
				$this->addServer($server);
			}
		}
		if (isset($servers[self::COMPRESS])) {
			$this->compress = MEMCACHE_COMPRESSED;
		}else{
			$this->compress = 0;
		}
		if(isset($servers[self::SECURITY])){
			$this->securityCode = $servers[self::SECURITY];
		}
	}
	
	/**
	 * 添加memached服务器
	 * @param array $server
	 */
	public function addServer(array $server) {
		if (!isset($server[self::HOST])) {
			$server[self::HOST] = '127.0.0.1';
		}
		if (!isset($server[self::PORT])) {
			$server[self::PORT] = 11211;
		}
		if (!isset($server[self::PCONN])) {
			$server[self::PCONN] = true;
		}
		if (!isset($server[self::WEIGHT])) {
			$server[self::WEIGHT] = 1;
		}
		if (!isset($server[self::TIMEOUT])) {
			$server[self::TIMEOUT] = 15;
		}
		if (!isset($server[self::RETRY])) {
			$server[self::RETRY] = 15;
		}
		if (!isset($server[self::STATUS])) {
			$server[self::STATUS] = true;
		}
		if (!isset($server[self::FCALLBACK])) {
			$server[self::FCALLBACK] = null;
		}
		list($host, $port, $pconn, $weight, $timeout, $retry, $status, $fcallback) = array_values($server);
		$this->cache->addServer($host, $port, $pconn, $weight, $timeout, $retry, $status, $fcallback);
		array_push($this->servers, $server);
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#add()
	 */
	public function add($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$key = $this->buildSecurityKey($key);
		$data = $this->storeData($value, $expires, $denpendency);
		return $this->cache->add($key, $data, $this->compress, (int) $expires);
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$key = $this->buildSecurityKey($key);
		$data = $this->storeData($value, $expires, $denpendency);
		return $this->cache->set($key, $data, $this->compress, (int)$expires);
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#replace()
	 */
	public function replace($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$key = $this->buildSecurityKey($key);
		$data = $this->storeData($value, $expires, $denpendency);
		return $this->cache->replace($key, $data, $this->compress,(int)$expires);
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function fetch($key) {
		$key = $this->buildSecurityKey($key);
		$data = $this->cache->get($key,$this->compress);
		if(!is_array($data)){
			return $data;
		}
		if(isset($data[self::DEPENDENCY]) && isset($data[self::DEPENDENCYCLASS])){
			L::import('Wind:component.cache.dependency.'.$data[self::DEPENDENCYCLASS]);
			$dependency = unserialize($data[self::DEPENDENCY]);/* @var $dependency IWindCacheDependency*/
			if(($dependency instanceof IWindCacheDependency) && $dependency->hasChanged()){
				$this->delete($key);
				return null;
			}
		}
		return isset($data[self::DATA]) ? $data[self::DATA] : null;
	}
	
	/*
	 * @see wind/component/cache/base/IWindCache#batchFetch()
	 */
	public function batchFetch(array $keys) {
		$data = array();
		foreach($keys as $key){
			if('' != ($value = $this->fetch($key))){
				$data[$key] = $value;
			}
		}
		return $data;
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#delete()
	 */
	public function delete($key) {
		return $this->cache->delete($this->buildSecurityKey($key));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#batchDelete()
	 */
	public function batchDelete(array $keys) {
		foreach($keys as $key){
			$this->delete($key);
		}
		return true;
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		return $this->cache->flush();
	}
	
	/**
	 * 生成安全的key
	 * @param string $key
	 * @return string
	 */
	private function buildSecurityKey($key){
		return substr(sha1(md5($key).$this->securityCode),0,5);
	}
	
	/* 
	 * 获取存储的数据
	 * @see wind/component/cache/stored/IWindCache#set()
	 * @return array
	 */
	protected function storeData($value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$data = array(self::DATA=>$value, self::EXPIRES=> $expires,self::STORETIME=>time());
		if ($denpendency && (($denpendency instanceof IWindCacheDependency))){			
			$denpendency->injectDependent($this);
			$data[self::DEPENDENCY] = serialize($denpendency);
			$data[self::DEPENDENCYCLASS] = get_class($denpendency);
		}
		return $data;
	}
}