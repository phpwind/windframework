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
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expire = 0, IWindCacheDependency $denpendency = null) {
		return $this->writeData($this->getRealCacheKey($key), $this->storeData($value, $expire, $denpendency), $expire);
	}
	/*
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, $this->readData($this->getRealCacheKey($key)));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#delete()
	 */
	public function delete($key) {
		$cacheFile = $this->getRealCacheKey($key);
		if (WindFile::isFile($cacheFile)) {
			return WindFile::deleteFile($cacheFile);
		}
		return false;
	}
	
	/**
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		if (WindFile::clearByPath($this->cacheDir, false)) {
			!WindFile::fileExists($this->cacheDir) && WindFile::createDir($this->cacheDir, 0777, true);
		}
	}
	
	/**
	 * 删除过期缓存
	 */
	public function deleteExpiredCache() {
		return WindFile::clearByPath($this->cacheDir, true);
	}
	/**
	 * 获取缓存文件名。
	 * @param string $key
	 * @return string
	 */
	public function getRealCacheKey($key) {
		$filename = $this->buildSecurityKey($key) . '.' . ltrim($this->cacheFileSuffix, '.');
		if (0 < $this->cacheDirectoryLevel) {
			$root = $this->cacheDir;
			for ($i = $this->cacheDirectoryLevel; $i > 0; --$i) {
				if (false === isset($key[$i])) {
					continue;
				}
				$root .= $key[$i] . DIRECTORY_SEPARATOR;
			}
			if (false === WindFile::isDir($root)) {
				WindFile::createDir($root, 0777, true);
			}
			return $root . $filename;
		}
		return $this->cacheDir . $filename;
	}
	/* 
	 * @see wind/core/WindComponentModule#setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$_config = is_object($config) ? $config->getConfig() : $config;
		$this->setCacheDir($_config[self::CACHEDIR]);
		if (isset($_config[self::SUFFIX])) {
			$this->cacheFileSuffix = $_config[self::SUFFIX];
		}
		if (isset($_config[self::LEVEL])) {
			$this->cacheDirectoryLevel = (int) $_config[self::LEVEL];
		}
		
	}
	/**
	 * 写入文件缓存
	 * @param string $file 缓存文件名
	 * @param string $data 缓存数据
	 * @param int $mtime 缓存文件的修改时间，即缓存的过期时间
	 * @return boolean
	 */
	protected function writeData($file, $data, $mtime = 0) {
		if (WindFile::writeover($file, $data) == strlen($data)) {
			$mtime += $mtime ? time() : 0;
			WindFile::setFileRight($file, 0777);
			return WindFile::setFileModifyTime($file, $mtime);
		}
		return false;
	}
	
	/**
	 * 从文件中读取缓存内容
	 * @param string $file 缓存文件名
	 * @return mixed
	 */
	protected function readData($file) {
		if (false === WindFile::isFile($file)) {
			return null;
		}
		if (($mtime = WindFile::getFileModifyTime($file)) > time() || !$mtime) {
			return unserialize(WindFile::readover($file));
		} else if (0 < $mtime) {
			WindFile::deleteFile($file);
		}
		return null;
	}
	/**
	 * 设置缓存目录
	 * @param string $dir
	 */
	private function setCacheDir($dir) {
		if (false === WindFile::isDir($dir)) {
			throw new WindException('cache dir must be a directory');
		}
		if (false === WindFile::isWrite($dir)) {
			throw new WindException('cache dir must be a writable directory');
		}
		$this->cacheDir = rtrim(realpath($dir), '\\/') . DIRECTORY_SEPARATOR;
	}
	
	public function __destruct() {
		$this->deleteExpiredCache();
	}

}