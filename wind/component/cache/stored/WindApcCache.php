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
 * php加速器缓存
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindApcCache implements IWindCache{
	
	/* 
	 * @see wind/component/cache/base/IWindCache#add()
	 */
	public function add($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$cacheData = $this->fetch($key);
		if(false === empty($cacheData)){
			$this->error("The cache already exists");
		}
		return apc_store($key,$this->storeData($value,$expires,$denpendency),$expires);
	}

	
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		return apc_store($key,$this->storeData($value,$expires,$denpendency),$expires);
	}

	
	/* 
	 * @see wind/component/cache/base/IWindCache#replace()
	 */
	public function replace($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$cacheData = $this->fetch($key);
		if(empty($cacheData)){
			$this->error("The cache does not exist");
		}
		return apc_store($key,$this->storeData($value,$expires,$denpendency),$expires);
	}

	
	/* 
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function fetch($key) {
		$data = unserialize(apc_fetch($key));
		if (empty($data) || !is_array($data)) {
			return $data;
		}
		if (isset($data[self::DEPENDENCY]) && $data[self::DEPENDENCY] instanceof IWindCacheDependency) {
			if ($data[self::DEPENDENCY]->hasChanged()) {
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
		foreach ($keys as $key) {
			if ('' != ($value = $this->fetch($key))) {
				$data[$key] = $value;
			}
		}
		return $data;
	}

	
	/* 
	 * @see wind/component/cache/base/IWindCache#delete()
	 */
	public function delete($key) {
		return apc_delete($key);
	}

	
	/* 
	 * @see wind/component/cache/base/IWindCache#batchDelete()
	 */
	public function batchDelete(array $keys) {
		foreach ($keys as $key) {
			$this->delete($key);
		}
		return true;
	}

	/* 
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		apc_clear_cache();
		return apc_clear_cache('user');
	}
	
	/**
	 * 错误处理
	 * @param string $message
	 * @param int $type
	 */
	public function error($message, $type = E_USER_ERROR) {
		trigger_error($message, $type);
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
			$data[self::DEPENDENCY] = $denpendency;
		}
		return serialize($data);
	}
}