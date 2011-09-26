<?php
Wind::import('WIND:cache.IWindCacheDependency');
/**
 * db缓存方式的依赖实现
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: WindResolvedCrashDependency.php 2849 2011-09-26 02:18:30Z xiaoxia.xuxx $
 * @package wind.cache.dependency
 */
class WindResolvedCrashDependency implements IWindCacheDependency {

	private $timeOut = '';
	
	private $callBack = array();
	
	private $args = array();
	
	public function __construct($callBack, $args  = array(), $timeOut = 0) {
		$this->timeOut = (int)$timeOut + time();
		$this->callBack = serialize($callBack);
		$this->args = serialize($args);
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
	        	$callBack = unserialize($this->callBack);
	        	$data = call_user_func_array($callBack, unserialize($this->args));
	            $cache->set($key, $data, $expires, $this);
	            $cache->delete($lock);
	        }
	    }
	    return false;
	}
}