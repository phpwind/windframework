<?php
Wind::import('WIND:cache.IWindCacheDependency');
/**
 * db缓存方式的依赖实现
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind.cache.dependency
 */
class WindDbDependency implements IWindCacheDependency {

	private $sql = '';

	private $timeOut = '';

	private $key = '';

	public function __construct($sql, $timeOut = 0) {
		$this->sql = $sql;
		$this->timeOut = (int)$timeOut + time();
	}

	/* (non-PHPdoc)
	 * @see IWindCacheDependency::injectDependent()
	 */
	public function injectDependent($expires) {
		if ($this->timeOut > 0) return;
		$this->timeOut = $expires > 0 ? 0.8 * $expires + time() : 0;
	}

	/* (non-PHPdoc)
	 * @see IWindCacheDependency::hasChanged()
	 */
	public function hasChanged($cache, $key, $expires) {
		if (0 == $this->timeOut) return false;
		if ($this->timeOut <= time()) {
			$lock = $key . '_lock_';
	        if ($cache->add($lock, 3 * 60 * 1000) == true) {
	        	//TODO 不要将数据组件的获取方式直接耦合进方法体,建议改成callback的方式
	            $db = Wind::getApp()->getComponent('db');
	            $data = $db->query($this->sql)->fetchAll();
	            $cache->set($key, $data, $expires, $this);
	            $cache->delete($lock);
	        }
	    }
	    return false;
	}
}