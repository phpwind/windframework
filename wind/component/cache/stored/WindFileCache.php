<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
L::import ( 'WIND:component.cache.base.IWindCache' );
/**
 * 文件缓存类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindFileCache implements IWindCache {
	
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
	/**
	 * @var string key的安全码
	 */
	private $securityCode = '';
	
	const CACHEDIR = 'cachedir';
	const SUFFIX = 'suffix';
	const LEVEL = 'level';
	const SECURITY = 'security';
	
	/**
	 * 初始化文件缓存配置
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		$this->setCacheDir($config[self::CACHEDIR]);
		if (isset($config[self::SUFFIX])) {
			$this->cacheFileSuffix = $config[self::SUFFIX];
		}
		if (isset($config[self::LEVEL])) {
			$this->cacheDirectoryLevel = (int) $config[self::LEVEL];
		}
		if (isset($config[self::SECURITY])) {
			$this->securityCode = $config[self::SECURITY];
		}
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#add()
	 */
	public function add($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$cacheFile = $this->getCacheFileName($key);
		if (is_file($cacheFile)) {
			$this->error("The cache already exists");
			return false;
		}
		$data = $this->storeData($value, $expires, $denpendency);
		return $this->writeToFile($cacheFile, $data, $expires);
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$cacheFile = $this->getCacheFileName($key);
		$data = $this->storeData($value, $expires, $denpendency);
		return $this->writeToFile($cacheFile, $data, $expires);
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#replace()
	 */
	public function replace($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		$cacheFile = $this->getCacheFileName($key);
		if (false === is_file($cacheFile)) {
			$this->error("The cache does not exist");
			return false;
		}
		$data = $this->storeData($value, $expires, $denpendency);
		return $this->writeToFile($cacheFile, $data, $expires);
	}
	
	/*
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function fetch($key) {
		$cacheFile = $this->getCacheFileName($key);
		$data = $this->getFromFile($cacheFile);
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
		$cacheFile = $this->getCacheFileName($key);
		if (is_file($cacheFile)) {
			return unlink($cacheFile);
		}
		return false;
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
	
	/**
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		return $this->clearByPath($this->cacheDir, false);
	}
	
	/**
	 * 删除过期缓存
	 */
	public function deleteExpiredCache() {
		return $this->clearByPath($this->cacheDir);
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
	 * 获取缓存文件名。
	 * @param string $key
	 * @return string
	 */
	public function getCacheFileName($key) {
		$filename = $key . '_' . substr(sha1($key . $this->securityCode), 0, 5) . '.' . ltrim($this->cacheFileSuffix, '.');
		if ($this->cacheDirectoryLevel > 0) {
			$root = $this->cacheDir;
			for ($i = $this->cacheDirectoryLevel; $i > 0; --$i) {
				if (false === isset($key[$i])) {
					continue;
				}
				$root .= $key[$i] . DIRECTORY_SEPARATOR;
			}
			if (false === is_dir($root)) {
				mkdir($root, 0777, true);
			}
			return $root . $filename;
		}
		return $this->cacheDir . $filename;
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
	
	/**
	 * 写入文件缓存
	 * @param string $file 缓存文件名
	 * @param string $data 缓存数据
	 * @param int $mtime 缓存文件的修改时间，即缓存的过期时间
	 * @return boolean
	 */
	protected function writeToFile($file, $data, $mtime = 0) {
		if($mtime){
			$mtime += time();
		}
		if (file_put_contents($file, $data, LOCK_EX) == strlen($data)) {
			chmod($file, 0777);
			return touch($file, $mtime);
		}
		return false;
	}
	/**
	 * 从文件中读取缓存内容
	 * @param string $file 缓存文件名
	 * @return mixed
	 */
	protected function getFromFile($file) {
		if (false === is_file($file)) {
			$this->error('The cache does not exist');
		}
		if (($mtime = filemtime($file)) > time() || !$mtime) {
			$data = unserialize(file_get_contents($file));
			return $data;
		} else if ($mtime > 0) {
			unlink($file);
		}
		return array();
	}
	
	/**
	 * 按目录删除缓存文件
	 * @param string $path 目录
	 * @param boolean $ifexpiled 是否过期
	 * @return boolean
	 */
	protected function clearByPath($path, $ifexpiled = true) {
		if (false === ($handle = opendir($path))) {
			return false;
		}
		while (false !== ($file = readdir($handle))) {
			if ('.' === $file[0] || '..' === $file[0]) continue;
			$fullPath = $path . DIRECTORY_SEPARATOR . $file;
			if (is_dir($fullPath)) {
				$this->clearByPath($fullPath, $ifexpiled);
			} else if (($ifexpiled && $mtime = filemtime($fullPath) && $mtime < time()) || !$ifexpiled) {
				unlink($fullPath);
			}
		}
		closedir($handle);
		if (false === $ifexpiled) {
			rmdir($path);
			!file_exists($this->cacheDir) && mkdir($this->cacheDir, 0777, true);
		}
		return true;
	}
	
	/**
	 * 设置缓存目录
	 * @param string $dir
	 */
	private function setCacheDir($dir) {
		if (false === is_dir($dir)) {
			$this->error('cache dir must be a directory');
		}
		if (false === is_writable($dir)) {
			$this->error('cache dir must be a writable directory');
		}
		$this->cacheDir = rtrim(realpath($dir), '\\/') . DIRECTORY_SEPARATOR;
	}
	
	public function __destruct() {
		$this->deleteExpiredCache();
	}

}