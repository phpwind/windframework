<?php
Wind::import('COM:cache.AbstractWindCache');
Wind::import('COM:utility.WindFile');
/**
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-7-26  xiaoxiao
 */
class WindFileCache extends AbstractWindCache {
	
	/**
	 * 缓存目录
	 * @var string 
	 */
	private $cacheDir;
	
	/**
	 * 缓存后缀
	 * @var string 
	 */
	private $cacheFileSuffix = 'txt';
	
	/**
	 * 缓存多级目录。最好不要超3层目录
	 * @var int 
	 */
	private $cacheDirectoryLevel = '';
	
	/**
	 * 保存操作的路径信息， 存储使用过的key路径
	 * @var array
	 */
	private $cacheFileList = array(); 

	/* (non-PHPdoc)
	 * @see AbstractWindCache::setValue()
	 */
	protected function setValue($key, $value, $expire = 0) {
		return $this->writeData($key, $value, $expire);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::get()
	 */
	protected function getValue($key) {
		return $this->readData($key);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::deleteValue()
	 */
	protected function deleteValue($cacheFile) {
		if (is_file($cacheFile)) return WindFile::delFile($cacheFile);
		return false;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::clear()
	 */
	public function clear($isExpired = false) {
		return WindFile::clearDir($this->getCacheDir(), $isExpired);
	}

	/**
	 * 获取缓存文件名。
	 * @param string $key
	 * @return string
	 */
	protected function buildSecurityKey($key) {
		$key = str_replace(D_S, '', $key);
		if (($dir = $this->checkCacheDir($key)) !== false) return $dir;
		$filename = parent::buildSecurityKey($key) . '.' . trim($this->getCacheFileSuffix(), '.');
		$_tmp = $this->getCacheDir();
		if (0 < $this->getCacheDirectoryLevel()) {
			for ($i = $this->getCacheDirectoryLevel(); $i > 0; --$i) {
				if (false === ($prefix = substr($key, $i+$i, 2))) continue;
				$_tmp .= DIRECTORY_SEPARATOR . $prefix;
			}
		}
		if (!is_dir($_tmp)) mkdir($_tmp, 0777, true);
		$this->cacheFileList[$key] = $_tmp . D_S . $filename;
		return $_tmp . D_S . $filename;
	}
	
	/**
	 * 采用最近最少使用原则算法
	 * 
	 * @param string $key
	 * @return string
	 */
	private function checkCacheDir($key) {
		return isset($this->cacheFileList[$key]) ? $this->cacheFileList[$key] : false;
	}

	/**
	 * 写入文件缓存
	 * @param string $file 缓存文件名
	 * @param string $data 缓存数据
	 * @param int $mtime 缓存文件的修改时间，即缓存的过期时间
	 * @return boolean
	 */
	private function writeData($file, $data, $mtime = 0) {
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
	private function readData($file) {
		if (false === is_file($file)) return null;
		clearstatcache();
		$mtime = filemtime($file);
		if (0 === $mtime || ($mtime && $mtime > time()))
			return WindFile::read($file);
		elseif (0 < $mtime)
			WindFile::delFile($file);
		return null;
	}

	/**
	 * 设置缓存目录
	 * @param string $dir
	 */
	public function setCacheDir($dir) {
		$this->cacheFileList = array();
		$dir = (false === strpos($dir, ':')) ? Wind::getAppName() . ':' . $dir : $dir;
		$this->cacheDir = !is_dir($dir) ? Wind::getRealDir($dir) : $dir;
	}

	/**
	 * @return the $cacheDir
	 */
	private function getCacheDir() {
		if (!is_dir($this->cacheDir)) mkdir($this->cacheDir, 0777, true);
		return $this->cacheDir;
	}


	/**
	 * @param string $cacheFileSuffix
	 */
	public function setCacheFileSuffix($cacheFileSuffix) {
		$this->cacheFileList = array();
		$this->cacheFileSuffix = $cacheFileSuffix;
	}
	
	/**
	 * @return the $cacheFileSuffix
	 */
	private function getCacheFileSuffix() {
		return $this->cacheFileSuffix;
	}

	/**
	 * @param int $cacheDirectoryLevel
	 */
	public function setCacheDirectoryLevel($cacheDirectoryLevel) {
		$cacheDirectoryLevel = intval($cacheDirectoryLevel);
		$this->cacheDirectoryLevel = $cacheDirectoryLevel > 5 ? 5 : ($cacheDirectoryLevel < 0 ? 0 : $cacheDirectoryLevel);
	}
	
	/**
	 * 返回cache目录级别，默认为0，不分级，最大分级为5
	 * @return the $cacheDirectoryLevel
	 */
	private function getCacheDirectoryLevel() {
		return $this->cacheDirectoryLevel;
	}

	/**
	 * 垃圾回收，清理过期缓存
	 */
	public function __destruct() {
//		$this->clear(true);
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->setCacheDir($this->getConfig('dir'));
		$this->setCacheFileSuffix($this->getConfig('suffix', '', 'txt'));
		$this->setCacheDirectoryLevel($this->getConfig('dirLevel', '', '0'));
	}

}