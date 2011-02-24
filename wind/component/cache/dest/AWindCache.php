<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 缓存接口及通用方法定义
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class AWindCache extends WindComponentModule {
	/**
	 * @var string key的安全码
	 */
	protected $securityCode = '';
	
	/**
	 * @var sting 缓存前缀
	 */
	protected $prefix = '';
	
	/**
	 * @var string 缓存依赖的类名称
	 */
	const DEPENDENCYCLASS = 'dependencyclass';
	/**
	 * @var string 标志存储时间
	 */
	const STORETIME = 'store';
	
	/**
	 * @var string 标志存储数据
	 */
	const DATA = 'data';
	
	/**
	 * @var string 配置文件中标志过期时间名称定义(也包含缓存元数据中过期时间 的定义)
	 */
	const EXPIRES = 'expires';
	/**
	 * @var string 配置文件中标志缓存依赖名称的定义
	 */
	const DEPENDENCY = 'dependency';
	/**
	 * @var string 配置文件中缓存安全码名称的定义
	 */
	const SECURITY = 'security';
	
	/**
	 * @var string 配置文件中缓存键的前缀名称的定义
	 */
	const KEYPREFIX = 'prefix';
	/**
	 * 设置缓存，如果key不存在，设置缓存，否则，替换已有key的缓存。
	 * @param string $key 保存缓存数据的键。
	 * @param string $value 保存缓存数据。
	 * @param int $expires 缓存数据的过期时间,0表示永不过期
	 * @param IWindCacheDependency $denpendency 缓存依赖
	 * @return boolean
	 */
	public abstract function set($key, $value, $expires = 0, IWindCacheDependency $denpendency = null);
	/**
	 * 获取指定缓存
	 * @param string $key 获取缓存数据的标识，即键
	 * @return mixed
	 */
	public abstract function get($key);
	
	/**
	 * 通过key批量获取缓存数据
	 * @param array $keys
	 * @return array
	 */
	public function batchGet(array $keys){
		$data = array();
		foreach ($keys as $key) {
			$data[$key] = $this->get($key);
		}
		return $data;
	}
	
	/**
	 * 删除缓存数据
	 * @param string $key 获取缓存数据的标识，即键
	 * @return string
	 */
	public abstract function delete($key);
	
	/**
	 * 通过key批量删除缓存数据
	 * @param array $keys
	 * @return boolean
	 */
	public function batchDelete(array $keys){
		foreach ($keys as $key) {
			$this->delete($key);
		}
		return true;
	}
	
	/**
	 * 清空所有缓存
	 */
	public abstract function flush();
	
	/**
	 * @param WindConfig $config
	 */
	public function setConfig($config){
		parent::setConfig($config);
		$_config = is_object($config) ? $config->getConfig() : $config;
		if (isset($_config[self::SECURITY])) {
			$this->securityCode = $_config[self::SECURITY];
		}
		if (isset($_config[self::KEYPREFIX])) {
			$this->prefix = $_config[self::KEYPREFIX];
		}
	}
	
	/**
	 * 如果缓存中有数据，则检查缓存依赖是否已经变更，如果变更则删除缓存
	 * @param string $key 键
	 * @param array  $data 缓存中的数据
	 * @return boolean true表示缓存依赖已变更，false表示缓存依赖未变改
	 */
	protected function checkDependencyChanged($key, array $data) {
		if (isset($data[self::DEPENDENCY]) && isset($data[self::DEPENDENCYCLASS])) {
			L::import('Wind:component.cache.dependency.' . $data[self::DEPENDENCYCLASS]);
			$dependency = unserialize($data[self::DEPENDENCY]); /* @var $dependency IWindCacheDependency*/
			if (($dependency instanceof IWindCacheDependency) && $dependency->hasChanged()) {
				$this->delete($key);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 从缓存元数据中取得真实的数据
	 * @param string $key
	 * @param mixed $data
	 * @return mixed
	 */
	protected function getDataFromMeta($key, $data) {
		if (is_array($data)) {
			if ($this->checkDependencyChanged($key, $data)) {
				return null;
			}
			return isset($data[self::DATA]) ? $data[self::DATA] : null;
		}
		return $data;
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
	 * 生成安全的key
	 * @param string $key
	 * @return string
	 */
	protected function buildSecurityKey($key) {
		return $this->prefix ? $this->prefix . '_' . $key : $key;
	}
	

}