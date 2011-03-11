<?php

L::import('WIND:component.cache.strategy.AbstractWindCache');
L::import('WIND:component.utility.WindFile');

/**
 * 文件缓存类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindFileCache extends AbstractWindCache {

	/**
	 * @var string 缓存目录
	 */
	protected $cacheDir;

	protected $cacheType = '';

	/**
	 * @var string 缓存后缀
	 */
	protected $cacheFileSuffix = '.bin';

	/**
	 * @var int 缓存多级目录。最好不要超3层目录
	 */
	protected $cacheDirectoryLevel = 0;

	const CACHEDIR = 'cache-dir';

	const SUFFIX = 'cache-suffix';

	const LEVEL = 'cache-level';

	/* (non-PHPdoc)
	 * @see AbstractWindCache::set()
	 */
	public function set($key, $value, $expire = null, IWindCacheDependency $denpendency = null) {
		$expire = $expire === null ? $this->getExpire() : $expire;
		return $this->writeData($this->getRealCacheKey($key), $this->storeData($value, $expire, $denpendency), $expire);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::get()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, $this->readData($this->getRealCacheKey($key)));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::delete()
	 */
	public function delete($key) {
		$cacheFile = $this->getRealCacheKey($key);
		if (is_file($cacheFile)) {
			return WindFile::delFile($cacheFile);
		}
		return false;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::clear()
	 */
	public function clear($isExpired = false) {
		return WindFile::clearDir($this->getCacheDir(), $isExpired);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::clearByType()
	 */
	public function clearByType($key, $type) {
		return WindFile::clearDir($this->getRealCacheKey($key, $type, true));
	}

	/**
	 * 获取缓存文件名。
	 * @param string $key
	 * @return string
	 */
	protected function getRealCacheKey($key, $fileType = '', $getDir = false) {
		$filename = $this->buildSecurityKey($key) . '.' . ltrim($this->getCacheFileSuffix(), '.');
		$fileType = $fileType ? $fileType : $this->getCacheType();
		$_tmp = $this->getCacheDir();
		if (0 < $this->getCacheDirectoryLevel()) {
			for ($i = $this->getCacheDirectoryLevel(); $i > 0; --$i) {
				if (false === isset($key[$i])) continue;
				$_tmp .= $key[$i] . DIRECTORY_SEPARATOR;
			}
			if (!is_dir($_tmp)) mkdir($_tmp, 0777, true);
		}
		if ($fileType) $_tmp .= $fileType . DIRECTORY_SEPARATOR;
		if (!is_dir($_tmp)) mkdir($_tmp, 0777, true);
		return $getDir ? $_tmp : $_tmp . $filename;
	}

	/**
	 * 写入文件缓存
	 * @param string $file 缓存文件名
	 * @param string $data 缓存数据
	 * @param int $mtime 缓存文件的修改时间，即缓存的过期时间
	 * @return boolean
	 */
	protected function writeData($file, $data, $mtime = 0) {
		if (WindFile::write($file, $data) == strlen($data)) {
			$mtime += $mtime ? time() : 0;
			chmod($file, 0777);
			return touch($file, $mtime);
		}
		return false;
	}

	/**
	 * 从文件中读取缓存内容
	 * @param string $file 缓存文件名
	 * @return null|string
	 */
	protected function readData($file) {
		if (false === is_file($file)) return null;
		$mtime = filemtime($file);
		if ($mtime === 0 || ($mtime && $mtime > time()))
			return unserialize(WindFile::read($file));
		elseif (0 < $mtime)
			WindFile::delFile($file);
		return null;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->setCacheDir($this->getConfig()->getConfig(self::CACHEDIR, WIND_CONFIG_VALUE));
	}

	/**
	 * 设置缓存目录
	 * @param string $dir
	 */
	private function setCacheDir($dir) {
		$this->cacheDir = L::getRealPath($dir) . DIRECTORY_SEPARATOR;
	}

	/**
	 * @return the $cacheDir
	 */
	public function getCacheDir() {
		if (!is_dir($this->cacheDir)) mkdir($this->cacheDir, 0777, true);
		return $this->cacheDir;
	}

	/**
	 * @return the $cacheFileSuffix
	 */
	protected function getCacheFileSuffix() {
		return $this->getConfig()->getConfig(self::SUFFIX, WIND_CONFIG_VALUE, '', $this->cacheFileSuffix);
	
	}

	/**
	 * @return the $cacheDirectoryLevel
	 */
	protected function getCacheDirectoryLevel() {
		return $this->getConfig()->getConfig(self::LEVEL, WIND_CONFIG_VALUE, '', $this->cacheDirectoryLevel);
	}

	/**
	 * 垃圾回收，清理过期缓存
	 */
	public function __destruct() {
		$this->clear(true);
	}

	/**
	 * @return the $fileType
	 */
	public function getCacheType() {
		return $this->cacheType;
	}

	/**
	 * @param field_type $fileType
	 */
	public function setCacheType($cacheType) {
		$this->cacheType = $cacheType;
	}

}