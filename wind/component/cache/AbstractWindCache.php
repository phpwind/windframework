<?php
/**
 * 缓存接口及通用方法定义
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class AbstractWindCache extends WindModule {
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
	 * 配置文件中标志缓存依赖名称的定义
	 * @var string 
	 */
	const DEPENDENCY = 'dependency';
	/*
	 * 配置项
	 */
	/**
	 * 配置文件中标志过期时间名称定义(也包含缓存元数据中过期时间 的定义)
	 * @var string 
	 */
	const EXPIRE = 'expires';
	/**
	 * 配置文件中缓存安全码名称的定义
	 * @var string 
	 */
	const SECURITYCODE = 'securityCode';
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
	public function set($key, $value, $expires = 0, AbstractWindCacheDependency $denpendency = null) {
		$data = array(self::DATA => $value, self::EXPIRE => $expires, self::STORETIME => time(), self::DEPENDENCY => null, self::DEPENDENCYCLASS => '');
		if (null != $denpendency) {
			$denpendency->injectDependent();
			$data[self::DEPENDENCY] = serialize($denpendency);
			$data[self::DEPENDENCYCLASS] = get_class($denpendency);
		}
		return $this->setValue($this->buildSecurityKey($key), serialize($data), $expires);
	}

	/**
	 * 执行添加操作
	 * 
	 * @param string $key
	 * @param object $value
	 * @param int $expires
	 * @throws WindException
	 */
	protected abstract function setValue($key, $value, $expires = 0);

	/**
	 * 获取指定缓存
	 * @param string $key 获取缓存数据的标识，即键
	 * @return mixed
	 */
	public function get($key) {
		return $this->formatData($key, $this->getValue($this->buildSecurityKey($key)));
	}

	/**
	 * 执行获取操作
	 * 
	 * @param string $key
	 * @throws WindException
	 */
	protected abstract function getValue($key);

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
	 * 
	 * @param string $key 获取缓存数据的标识，即键
	 * @return string
	 */
	public function delete($key) {
		return $this->deleteValue($this->buildSecurityKey($key));
	}

	/**
	 * 需要实现的删除操作
	 * @param string $key
	 */
	protected abstract function deleteValue($key);

	/**
	 * 通过key批量删除缓存数据
	 * @param array $keys
	 * @return boolean
	 */
	public function batchDelete(array $keys) {
		foreach ($keys as $key)
			$this->delete($key);
		return true;
	}

	/**
	 * 清空所有缓存
	 */
	public abstract function clear();

	/**
	 * 格式化输出
	 * @param string $value
	 * @return array
	 */
	protected function formatData($key, $value) {
		$data = unserialize($value);
		if (!is_array($data)) return null;
		if ($this->hasChanged($key, $data)) return null;
		return isset($data[self::DATA]) ? $data[self::DATA] : null;
	}

	/**
	 * 如果缓存中有数据，则检查缓存依赖是否已经变更，如果变更则删除缓存
	 * @param string $key 键
	 * @param array  $data 缓存中的数据
	 * @return boolean true表示缓存依赖已变更，false表示缓存依赖未变改
	 */
	protected function hasChanged($key, array $data) {
		if (null === $data[self::DEPENDENCY] && '' === $data[self::DEPENDENCYCLASS]) return false;
		$dependency = unserialize($data[self::DEPENDENCY]);
		if (($dependency instanceof AbstractWindCacheDependency) && $dependency->hasChanged()) {
			$this->delete($key);
			return true;
		}
		return false;
	}

	/**
	 * 生成安全的key
	 * @param string $key
	 * @return string
	 */
	protected function buildSecurityKey($key) {
		return $this->getKeyPrefix() ? $this->getKeyPrefix() . '_' . $key . $this->getSecurityCode() : $key . $this->getSecurityCode();
	}

	/**
	 * 返回缓存Key值前缀，默认值为null无任何前缀添加
	 * @return the $prefix
	 */
	protected function getKeyPrefix() {
		return $this->keyPrefix;
	}

	/**
	 * @param sting $keyPrefix
	 */
	public function setKeyPrefix($keyPrefix) {
		$this->keyPrefix = $keyPrefix;
	}

	/**
	 * @return the $securityCode
	 */
	protected function getSecurityCode() {
		return $this->securityCode;
	}

	/**
	 * @param string $securityCode
	 */
	public function setSecurityCode($securityCode) {
		$this->securityCode = $securityCode;
	}

	/**
	 * 返回过期时间设置,默认值为0永不过期
	 * @return the $expire
	 */
	public function getExpire() {
		return $this->expire;
	}

	/**
	 * @param int $expire
	 */
	public function setExpire($expire) {
		$this->expire = intval($expire);
	}

	/**
	 * 设置配置信息
	 * @param array $config
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->setSecurityCode($this->getConfig(self::SECURITYCODE, '', ''));
		$this->setKeyPrefix($this->getConfig(self::KEYPREFIX, '', ''));
		$this->setExpire($this->getCofnig(self::EXPIRE, '', 0));
	}
	
	/**
	 * 获得缓存表相关配置
	 * 
	 * @param array $config
	 * @param string $name
	 * @param string $subname
	 * @param string $default
	 * @return string
	 */
	protected function getSubConfig($config, $name, $subname = '', $default = '') {
		$result = (isset($config[$name])) ? $config[$name] : $default;
		if (!$subname || !isset($result[$subname])) return $result;
		return isset($result[$subname]) ? $result[$subname] : $default;
	}
}