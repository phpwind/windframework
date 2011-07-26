<?php
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@alibaba-inc.com> 2011-07-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindCache extends WindModule {
	
	private $caches = array();
	
	const DB = 'db';
	const APC = 'apc';
	const FILE = 'file';
	const EAC = 'eaccelerator';
	const MEMCACHE = 'mem';
	const WINCACHE = 'win';
	const XCACHE = 'XCache';
	const ZEND = 'ZendCache';
	
	/**
	 * 保存数据
	 * 
	 * @param string $type   缓存类型
	 * @param string $key
	 * @param mixed $value
	 * @param int $expires
	 * @param AbstractWindCacheDependency $denpendency
	 * @return mixed
	 */
	public function set($type, $key, $value, $expires = 0, AbstractWindCacheDependency $denpendency = null) {
		$cache = $this->getCache($type);
		return $cache->set($key, $value, $expires, $denpendency);
	}
	
	/**
	 * 获取数据
	 * 
	 * @param string $type   缓存类型
	 * @param string $key
	 * @return mixed
	 */
	public function get($type, $key) {
		$cache = $this->getCache($type);
		return $cache->get($key);
	}
	
	/**
	 * 删除缓存数据
	 * 
	 * @param string $type 缓存类型
	 * @param string $key 获取缓存数据的标识，即键
	 * @return mixed
	 */
	public function delete($type, $key) {
		$cache = $this->getCache($type);
		return $cache->delete($key);
	}
	
	/**
	 * 批量获取数据
	 * 
	 * @param string $type
	 * @param string $keys
	 * @return array
	 */
	public function batchGet($type, array $keys) {
		$cache = $this->getCache($type);
		return $cache->batchGet($keys);
	}
	
	/**
	 * 通过key批量删除缓存数据
	 * 
	 * @param string $type
	 * @param array $keys
	 * @return boolean
	 */
	public function batchDelete($type, array $keys) {
		$cache = $this->getCache($type);
		return $cache->batchDelete($keys);
	}
	
	/**
	 * 通过key批量删除缓存数据
	 * 
	 * @param string $type
	 * @return boolean
	 */
	public function clear($type = '') {
		if ($type) {
			$cache = $this->getCache($type);
			return $cache->clear();
		}
		foreach($this->caches as $key => $cache) {
			$cache->clear();
		}
		return true;
	}
	
	
	/**
	 * 获得缓存类型
	 * 
	 * @param string $type
	 * @return AbstractWindCache
	 */
	public function getCache($type) {
		$className = $this->getCacheMap($type);
		if (!$className) throw new WindException('The cache strategy \'' . $type . '\' is not exists!');
		if (isset($this->caches[$className])) return $this->caches[$className];
		Wind::import('WIND:component.cache.strategy.' . $className);
		$cache = new $className();
		$config = $this->getSystemConfig()->getCacheConfig($type);
		if ($config) $cache->setConfig($config);
		$this->caches[$className] = $cache;
		return $cache;
	}
	
	/**
	 * 对应类型对应的缓存
	 * 
	 * @param string $type
	 */
	private function getCacheMap($type) {
		$types = array(self::DB => 'WindDbCache', self::APC => 'WindApcCache', self::FILE => 'WindFileCache',
		self::EAC => 'WindEacceleratorCache', self::MEMCACHE => 'WindMemCache', self::WINCACHE => 'WindWinCache',
		self::XCACHE => 'WindXCache', self::ZEND => 'WindZendCache');
		return isset($types[$type]) ? $types[$type] : null;
	}
}