<?php
/**
 * @author xiaoxiao <x_824@sina.com>  2011-7-25
 * @link http://www.cnblogs.com/xiaoyaoxia/
 * @copyright Copyright &copy; 2011-2012  xiaoxiao
 * @license
 * @package
 */
Wind::import('WIND:component.cache.AbstractWindCacheDependency');
class WindFileCacheDependency extends AbstractWindCacheDependency {
	private $fileName = '';

	public function __construct($fileName = null) {
		$this->fileName = $fileName;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindCacheDependency::notifyDependencyChanged()
	 */
	protected function notifyDependencyChanged() {
		clearstatcache();//删除文件信息的缓存
		if ($this->fileName) {
			return @filemtime($this->fileName);
		}
	}
}