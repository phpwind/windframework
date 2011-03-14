<?php
/**
 * @author Su Qian <weihu@alibaba-inc.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.cache.AbstractWindCache');
L::import('WIND:component.cache.operator.WindEaccelerator');
/**
 * Eaccelerator是一款php加速器、优化器、编码器及动态内容缓存。
 * WindEaccelerator实现Eaccelerator动态内容缓存功能。
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Su Qian <weihu@alibaba-inc.com>
 * @version $Id$ 
 * @package 
 */
class WindCacheEaccelerator extends AbstractWindCache {

	/**
	 * @var WindEaccelerator
	 */
	protected $eaccelerator = null;

	public function __construct() {
		$this->eaccelerator = new WindEaccelerator(); /* @var WindEaccelerator*/
	}

	/* 
	 * @see AbstractWindCache#set()
	 */
	public function set($key, $value, $expire = null, IWindCacheDependency $denpendency = null) {
		$expire = null === $expire ? $this->getExpire() : $expire;
		return $this->eaccelerator->set($this->buildSecurityKey($key), $this->storeData($value, $expire, $denpendency), $expire);
	}

	/* 
	 * @see AbstractWindCache#get()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->eaccelerator->get($this->buildSecurityKey($key))));
	}

	/* 
	 * @see AbstractWindCache#delete()
	 */
	public function delete($key) {
		return $this->eaccelerator->delete($this->buildSecurityKey($key));
	}

	/* 
	 * @see AbstractWindCache#clear()
	 * @return boolean
	 */
	public function clear() {
		return $this->eaccelerator->flush();
	}
}