<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.cache.strategy.AWindCache');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindDbCache extends AWindCache {
	/**
	 * @var WindConnectionManager 分布式管理
	 */
	protected $dbHandler = null;
	/**
	 * @var string 缓存表
	 */
	protected $table = 'pw_cache';
	
	/**
	 * @var string 缓存表的键字段
	 */
	protected $keyField = 'key';
	/**
	 * @var string 缓存表的值字段
	 */
	protected $valueField = 'value';
	/**
	 * @var string 缓存表过期时间字段
	 */
	protected $expireField = 'expire';
	
	/**
	 * @var boolean 数据过期策略
	 */
	protected $expirestrage = true;
	
	const CACHETABLE = 'cachetable';
	const NAME = 'name';
	const KEY = 'key';
	const VALUE = 'value';
	const EXPIRE = 'expire';
	const FIELD = 'field';
	const EXPIRESTRAGE = 'expirestrage';
	
	public function __construct(WindConnectionManager $dbHandler = null) {
		$dbHandler && $this->setDbHandler($dbHandler);
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
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function get($key) {echo $key;
		$data = $this->getSlaveConnection()->getSqlBuilder()->from($this->table)->field($this->valueField)->where($this->keyField.' = :key ', array(':key' => $this->buildSecurityKey($key)))->select()->getRow();
		return $this->getDataFromMeta($key, unserialize($data[$this->valueField]));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#batchFetch()
	 */
	public function batchGet(array $keys) {
		foreach ($keys as $key => $value) {
			$keys[$key] = $this->buildSecurityKey($value);
		}
		$data = $this->getSlaveConnection()->getSqlBuilder()->from($this->table)->field($this->valueField, $this->keyField)->where($this->keyField.' in ( :key ) ', array(':key' => $keys))->select()->getAllRow();
		$result = $changed = array();
		foreach ($data as $_data) {
			$_data = unserialize($_data[$this->valueField]);
			if (isset($_data[self::DEPENDENCY]) && $_data[self::DEPENDENCY] instanceof IWindCacheDependency) {
				if ($_data[self::DEPENDENCY]->hasChanged()) {
					$changed[] = $_data[$this->keyField];
				} else {
					$result[$_data[$this->keyField]] = $_data[self::DATA];
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
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->where($this->keyField.' = :key ', array(':key' => $this->buildSecurityKey($key)))->delete();
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#batchDelete()
	 */
	public function batchDelete(array $keys) {
		foreach ($keys as $key => $value) {
			$keys[$key] = $this->buildSecurityKey($value);
		}
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->where($this->keyField.' in (:key) ', array(':key' => $keys))->delete();
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->delete();
	}
	
	/**
	 * 删除过期数据
	 */
	public function deleteExpiredCache() {
		return @$this->getMasterConnection()->getSqlBuilder()->from($this->table)->where($this->expireField.' !=0 AND '.$this->expireField.' < :expires', array(':expires' => time()))->delete();
	}
	
	public function setDbHandler(WindConnectionManager $dbHandler){
		$this->dbHandler = $dbHandler;
	}
	
	/* 
	 * @see wind/core/WindComponentModule#setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$_config = is_object($config) ? $config->getConfig() : $config;
		if(false === isset($_config[self::CACHETABLE])){
			throw new WindException('The cache table is not exist');
		}
		$tableConfig = $_config[self::CACHETABLE];
		$this->table = isset($tableConfig[self::NAME]) ? $tableConfig[self::NAME] : 'pw_cache';
		$field = $tableConfig[self::FIELD];
		$this->keyField = isset($field[self::KEY]) ? $field[self::KEY] : 'key';
		$this->valueField = isset($field[self::VALUE]) ? $field[self::VALUE] : 'value';
		$this->expireField = isset($field[self::EXPIRE]) ? $field[self::EXPIRE] : 'expire';
		$this->expirestrage = isset($field[self::EXPIRESTRAGE]) ? (bool)$field[self::EXPIRESTRAGE] : true;
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
		$data = addslashes($this->storeData($value, $expires, $denpendency));
		if($expires){
			$expires += time();
		}
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->field($this->keyField, $this->valueField, $this->expireField)->data($this->buildSecurityKey($key), $data, $expires)->insert();
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
		if($expires){
			$expires += time();
		}
		return $this->getMasterConnection()->getSqlBuilder()->from($this->table)->set($this->valueField.' = :value,'.$this->expireField.' = :expires', array(':value' => $data, ':expires' => $expires))->where($this->keyField.'=:key', array(':key' => $this->buildSecurityKey($key)))->update();
	}
	/**
	 * 获取写缓存的数据库
	 * @return WindDbAdapter
	 */
	private function getMasterConnection() {
		return $this->dbHandler->getMasterConnection();
	}
	
	/**
	 *  获取读缓存的数据库
	 * @return WindDbAdapter
	 */
	private function getSlaveConnection() {
		return $this->dbHandler->getSlaveConnection();
	}
	
	public function __destruct() {
		$this->deleteExpiredCache();
	}

}