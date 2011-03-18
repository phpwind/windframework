<?php
L::import('WIND:core.WindComponentModule');
/**
 * 缓存接口及通用方法定义
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class AbstractWindCache extends WindComponentModule {

	/**
	 * key的安全码
	 * @var string
	 */
	private $securityCode = '';

	/**
	 * 缓存前缀
	 * @var sting 
	 */
	private $keyPrefix = '';

	/**
	 * 缓存过期时间
	 * @var int
	 */
	private $expire = '';

	/**
	 * 缓存依赖的类名称
	 * @var string
	 */
	const DEPENDENCYCLASS = 'dependencyclass';

	/**
	 * 标志存储时间
	 * @var string
	 */
	const STORETIME = 'store';

	/**
	 * 标志存储数据
	 * @var string 
	 */
	const DATA = 'data';

	/**
	 * 配置文件中标志过期时间名称定义(也包含缓存元数据中过期时间 的定义)
	 * @var string 
	 */
	const EXPIRE = 'expires';

	/**
	 * 配置文件中标志缓存依赖名称的定义
	 * @var string 
	 */
	const DEPENDENCY = 'dependency';

	/**
	 * 配置文件中缓存安全码名称的定义
	 * @var string 
	 */
	const SECURITY = 'security';

	/**
	 * 配置文件中缓存键的前缀名称的定义
	 * @var string 
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
	public function batchGet(array $keys) {
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
	public function batchDelete(array $keys) {
		foreach ($keys as $key) {
			$this->delete($key);
		}
		return true;
	}

	/**
	 * 清空所有缓存
	 */
	public abstract function clear();

	/**
	 * 如果缓存中有数据，则检查缓存依赖是否已经变更，如果变更则删除缓存
	 * @param string $key 键
	 * @param array  $data 缓存中的数据
	 * @return boolean true表示缓存依赖已变更，false表示缓存依赖未变改
	 */
	protected function checkDependencyChanged($key, array $data) {
		if (isset($data[self::DEPENDENCY]) && isset($data[self::DEPENDENCYCLASS])) {
			L::import('Wind:component.cache.dependency.' . $data[self::DEPENDENCYCLASS]);
			/* @var $dependency IWindCacheDependency*/
			$dependency = unserialize($data[self::DEPENDENCY]);
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

	/**
	 * 获取存储的数据
	 * @param string $value
	 * @param string $expires
	 * @param IWindCacheDependency $denpendency
	 * @return string
	 */
	protected function storeData($value, $expires = null, $denpendency = null) {
		$data = array(
			self::DATA => $value, 
			self::EXPIRE => $expires, 
			self::STORETIME => time());
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
		return $this->getKeyPrefix() ? $this->getKeyPrefix() . '_' . $key : $key;
	}

	/**
	 * @return the $securityCode
	 */
	protected function getSecurityCode() {
		return $this->getConfig()->getConfig(self::SECURITY, WIND_CONFIG_VALUE, '', $this->securityCode);
	}

	/**
	 * 返回缓存Key值前缀，默认值为null无任何前缀添加
	 * @return the $prefix
	 */
	protected function getKeyPrefix() {
		return $this->keyPrefix;
	}

	/**
	 * 返回过期时间设置,默认值为0永不过期
	 * @return the $expire
	 */
	public function getExpire() {
		if ('' === $this->expire) {
			$this->expire = $this->getConfig()->getConfig(self::EXPIRE, WIND_CONFIG_VALUE, '', '0');
		}
		return $this->expire;
	}

	/**
	 * @param string $securityCode
	 */
	public function setSecurityCode($securityCode) {
		$this->securityCode = $securityCode;
	}

	/**
	 * @param sting $keyPrefix
	 */
	public function setKeyPrefix($keyPrefix) {
		$this->keyPrefix = $keyPrefix;
	}

	/**
	 * @param int $expire
	 */
	public function setExpire($expire) {
		$this->expire = $expire;
	}

}