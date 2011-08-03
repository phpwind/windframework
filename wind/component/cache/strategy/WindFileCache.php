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
	private $cacheDirectoryLevel = 0;
	
	/**
	 * 保存操作的路径信息， 存储使用过的key路径
	 * @var array
	 */
	private $cacheFileList = array();

	/* (non-PHPdoc)
	 * @see AbstractWindCache::setValue($key, $value, $expires)
	 */
	protected function setValue($key, $value, $expire = 0) {
		return WindFile::write($key, $value) == strlen($value);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::get()
	 */
	protected function getValue($key) {
		if (!is_file($key)) return null;
		return WindFile::read($key);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::deleteValue()
	 */
	protected function deleteValue($key) {
		return WindFile::write($key, '');
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::clear()
	 */
	public function clear() {
		return WindFile::clearDir($this->getCacheDir());
	}

	/**
	 * 获取缓存文件名。
	 * 
	 * @param string $key
	 * @return string
	 */
	protected function buildSecurityKey($key) {
		if (false !== ($dir = $this->checkCacheDir($key))) return $dir;
		$_dir = $this->getCacheDir();
		if (0 < ($level = $this->getCacheDirectoryLevel())) {
			$_subdir = substr(md5($key), 0, $level);
			$_dir .= DIRECTORY_SEPARATOR . $_subdir;
			if (!is_dir($_dir)) mkdir($_dir, 0777, true);
		}
		$filename = parent::buildSecurityKey($key) . '.' . $this->getCacheFileSuffix();
		$this->cacheFileList[$key] = ($_dir ? $_dir . DIRECTORY_SEPARATOR . $filename : $filename);
		return $this->cacheFileList[$key];
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
	 * 设置缓存目录
	 * @param string $dir
	 */
	public function setCacheDir($dir) {
		$_dir = Wind::getRealDir($dir);
		if (!is_dir($_dir)) mkdir($_dir, 0777, true);
		$this->cacheDir = $_dir;
	}

	/**
	 * @return the $cacheDir
	 */
	private function getCacheDir() {
		return $this->cacheDir;
	}

	/**
	 * @param string $cacheFileSuffix
	 */
	public function setCacheFileSuffix($cacheFileSuffix) {
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
		$this->cacheDirectoryLevel = $cacheDirectoryLevel;
	}

	/**
	 * 返回cache目录级别，默认为0，不分级，最大分级为5
	 * @return the $cacheDirectoryLevel
	 */
	public function getCacheDirectoryLevel() {
		return $this->cacheDirectoryLevel;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindCache::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->setCacheDir($this->getConfig('dir'));
		$this->setCacheFileSuffix($this->getConfig('suffix', '', 'txt'));
		$this->setCacheDirectoryLevel($this->getConfig('dir-level', '', 0));
	}

}