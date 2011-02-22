<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
L::import('WIND:component.cache.base.IWindCache');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindDbCache extends WindComponentModule implements IWindCache {
	/**
	 * @var WindConnectionManager 分布式管理
	 */
	protected $distributed = null;
	/**
	 * @var string 安全code
	 */
	protected $securityCode = '';
	/**
	 * @var string 缓存表
	 */
	protected $table = 'pw_cache';
	
	const SECURITY = 'security';
	const CACHETABLE = 'cachetable';
	
	public function __construct(array $config = array(),WindConnectionManager $distributed = null) {
		if($distributed){
			$this->setDistributed($distributed);
		}
		if($config){
			$this->setCacheConfig($config);
		}
	}
	
	
	/* 
	 * @see wind/component/cache/base/IWindCache#add()
	 */
	public function add($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		if ($this->fetch($key)) {
			$this->error("The cache already exists");
		}
		$this->store($key, $value, $expires, $denpendency);
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		if ($this->fetch($key)) {
			return $this->update($key, $value, $expires, $denpendency);
		} else {
			return $this->store($key, $value, $expires, $denpendency);
		}
		return true;
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#replace()
	 */
	public function replace($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		if ($this->fetch($key)) {
			return $this->update($key, $value, $expires, $denpendency);
		} else {
			$this->error("The cache does not exist");
			return false;
		}
		return true;
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function fetch($key) {
		$data = $this->getSlaveConnection()->getSqlBuilder()->from($this->table)->field('value')->where('key = :key ', array(':key' => $this->buildSecurityKey($key)))->select()->getRow();
		$data = unserialize($data['value']);
		if(isset($data[self::DEPENDENCY]) && isset($data[self::DEPENDENCYCLASS])){
			L::import('Wind:component.cache.dependency.'.$data[self::DEPENDENCYCLASS]);
			$dependency = unserialize($data[self::DEPENDENCY]);/* @var $dependency IWindCacheDependency*/
			if(($dependency instanceof IWindCacheDependency) && $dependency->hasChanged()){
				$this->delete($key);
				return null;
			}
		}
		return isset($data[self::DATA]) ? $data[self::DATA] : '';
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#batchFetch()
	 */
	public function batchFetch(array $keys) {
		foreach ($keys as $key => $value) {
			$keys[$key] = $this->buildSecurityKey($value);
		}
		$data = $this->getSlaveConnection()->getSqlBuilder()->from($this->table)->field('value', 'key')->where('key = :key ', array(':key' => $keys))->select()->getAllRow();
		$result = $changed = array();
		foreach ($data as $_data) {
			$_data = unserialize($_data['value']);
			if (isset($_data[self::DEPENDENCY]) && $_data[self::DEPENDENCY] instanceof IWindCacheDependency) {
				if ($_data[self::DEPENDENCY]->hasChanged()) {
					$changed[] = $_data['key'];
				} else {
					$result[$_data['key']] = $_data[self::DATA];
				}
			}
		}
		$changed && $this->batchDelete($changed);
		return $result;
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#delete()
	 */
	public function delete($key) {
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->where('key = :key ', array(':key' => $this->buildSecurityKey($key)))->delete();
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#batchDelete()
	 */
	public function batchDelete(array $keys) {
		foreach ($keys as $key => $value) {
			$keys[$key] = $this->buildSecurityKey($value);
		}
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->where('key in (:key) ', array(':key' => $keys))->delete();
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->delete();
	}
	
	public function deleteExpiredCache() {
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->where('expires !=0 AND expires < :expires', array(':expires' => time()))->delete();
	}
	
	public function setDistributed(WindConnectionManager $distributed){
		$this->distributed = $distributed;
	}
	
	/* 
	 * @see wind/core/WindComponentModule#setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$config = $config->getConfig();
		if(isset($config[self::SECURITY])){
			$this->securityCode = $config[self::SECURITY];
		}
		if(isset($config[self::cachetable])){
			$this->table = $config[self::cachetable];
		}
	}
	/**
	 * 错误处理
	 * @param string $message
	 * @param int $type
	 */
	public function error($message, $type = E_USER_ERROR) {
		trigger_error($message, $type);
	}
	
	/**
	 * 存储数据
	 * @param string $key
	 * @param string $value
	 * @param int $expires
	 * @param IWindCacheDependency $denpendency
	 * @return boolean
	 */
	protected function store($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$data = $this->storeData($value, $expires, $denpendency);
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->field('key', 'value', 'expires')->data($key, $data, $expires)->insert();
	}
	
	/**
	 * 更新数据
	 * @param string $key
	 * @param int $value
	 * @param int $expires
	 * @param IWindCacheDependency $denpendency
	 * @return boolean
	 */
	protected function update($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$data = $this->storeData($value, $expires, $denpendency);
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->set('value = :value,expires = :expires', array(':value' => $data, ':expires' => $expires))->where('key=:key', array(':key' => $this->buildSecurityKey($key)))->update();
	}
	
	/* 
	 * 获取存储的数据
	 * @see wind/component/cache/stored/IWindCache#set()
	 * @return string
	 */
	protected function storeData($value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$data = array(self::DATA => $value, self::EXPIRES => $expires, self::STORETIME => time());
		if ($denpendency && (($denpendency instanceof IWindCacheDependency))) {
			$denpendency->injectDependent($this);
			$data[self::DEPENDENCY] = serialize($denpendency);
			$data[self::DEPENDENCYCLASS] = get_class($denpendency);
		}
		return serialize($data);
	}
	
	/**
	 * 获取写缓存的数据库
	 * @return WindDbAdapter
	 */
	private function getMasterConnection() {
		return $this->distributed->getMasterConnection();
	}
	
	/**
	 *  获取读缓存的数据库
	 * @return WindDbAdapter
	 */
	private function getSlaveConnection() {
		return $this->distributed->getSlaveConnection();
	}
	
	/**
	 * 生成安全的key
	 * @param string $key
	 * @return string
	 */
	private function buildSecurityKey($key) {
		return substr(md5($key . $this->securityCode), 0, 5);
	}
	
	public function __destruct() {
		$this->deleteExpiredCache();
	}

}