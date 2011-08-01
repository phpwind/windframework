<?php
Wind::import('COM:cache.AbstractWindCache');
/**
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-7-26  xiaoxiao
 */
class WindXCache extends AbstractWindCache {
	private $authUser = '';
	private $authPwd = '';

	public function __construct() {
		if (!extension_loaded('xcache')) {
			throw new WindCacheException('The xcache extension must be loaded !');
		}
	}

	/* 
	 * @see AbstractWindCache#setValue()
	 */
	protected function setValue($key, $value, $expire = 0) {
		return xcache_set($key, $value, $expire);
	}

	/* 
	 * @see AbstractWindCache#getValue()
	 */
	protected function getValue($key) {
		return xcache_get($key);
	}

	/* 
	 * @see AbstractWindCache#deleteValue()
	 */
	protected function deleteValue($key) {
		return xcache_unset($key);
	}

	/* 
	 * @see AbstractWindCache#clear()
	 */
	public function clear() {
		//xcache_clear_cache需要验证权限
		$this->checkAuthor();
		
		//如果配置中xcache.var_count > 0 则不能用xcache_clear_cache(XC_TYPE_VAR, 0)的方式删除
		$max = xcache_count(XC_TYPE_VAR);  
		for ($i = 0; $i < $max; $i++) {
			xcache_clear_cache(XC_TYPE_VAR, $i);
		}
		
		//恢复之前的权限
		$this->checkAuthor(true);
		
		return true;
	}
	
	/**
	 * 设置验证权限
	 * @param boolean $recover 是否恢复设置
	 */
	private function checkAuthor($recover = false) {
		static $tmp = array();
		if (!$recover) {
			$tmp['user'] = isset($_SERVER['PHP_AUTH_USER']) ? null : $_SERVER['PHP_AUTH_USER'];
			$tmp['pwd'] = isset($_SERVER['PHP_AUTH_PW']) ? null : $_SERVER['PHP_AUTH_PW'];
			$_SERVER['PHP_AUTH_USER']	= $this->authUser;
			$_SERVER['PHP_AUTH_PW']		= $this->authPwd;
			return true;
		}
		$_SERVER['PHP_AUTH_USER'] = $tmp['user'];
		$_SERVER['PHP_AUTH_PW'] = $tmp['pwd'];
		unset($tmp);
		return true;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindCache::setConfig()
	 */
	public function setConfig($config = array()) {
		if (!$config) return false;
		parent::setConfig($config);
		$this->authUser = $this->getConfig('user');
		$this->authPwd = $this->getConfig('pwd');
	}

}