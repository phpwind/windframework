<?php
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@alibaba-inc.com> 2011-07-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindCache {
	
	private static $caches = array();
	
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
	public static function set($type, $key, $value, $expires = 0, AbstractWindCacheDependency $denpendency = null) {
		$cache = self::getCache($type);
		return $cache->set($key, $value, $expires, $denpendency);
	}
	
	/**
	 * 获取数据
	 * 
	 * @param string $type   缓存类型
	 * @param string $key
	 * @return mixed
	 */
	public static function get($type, $key) {
		$cache = self::getCache($type);
		return $cache->get($key);
	}
	
	/**
	 * 删除缓存数据
	 * 
	 * @param string $type 缓存类型
	 * @param string $key 获取缓存数据的标识，即键
	 * @return mixed
	 */
	public static function delete($type, $key) {
		$cache = self::getCache($type);
		return $cache->delete($key);
	}
	
	/**
	 * 批量获取数据
	 * 
	 * @param string $type
	 * @param string $keys
	 * @return array
	 */
	public static function batchGet($type, array $keys) {
		$cache = self::getCache($type);
		return $cache->batchGet($keys);
	}
	
	/**
	 * 通过key批量删除缓存数据
	 * 
	 * @param string $type
	 * @param array $keys
	 * @return boolean
	 */
	public static function batchDelete($type, array $keys) {
		$cache = self::getCache($type);
		return $cache->batchDelete($keys);
	}
	
	/**
	 * 通过key批量删除缓存数据
	 * 
	 * @param string $type
	 * @return boolean
	 */
	public static function clear($type = '') {
		if ($type) {
			$cache = self::getCache($type);
			return $cache->clear();
		}
		foreach(self::caches as $key => $cache) {
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
	private static function getCache($type) {
		$type = self::getCacheMap($type);
		if (!$type) throw new WindException('The cache strategy \'' . $type . '\' is not exists!');
		if (isset(self::$caches[$type])) return self::$caches[$type];
		$class = Wind::import('WIND:component.cache.strategy.' . $type);
		self::$caches[$type] = new $class();
		return self::$caches[$type];
	}
	
	/**
	 * 对应类型对应的缓存
	 * 
	 * @param string $type
	 */
	private static function getCacheMap($type) {
		$types = array(self::DB => 'WindDbCache', self::APC => 'WindApcCache', self::FILE => 'WindFileCache',
		self::EAC => 'WindEacceleratorCache', self::MEMCACHE => 'WindMemCache', self::WINCACHE => 'WindWinCache',
		self::XCACHE => 'WindXCache', self::ZEND => 'WindZendCache');
		return isset($types[$type]) ? $types[$type] : null;
	}
}