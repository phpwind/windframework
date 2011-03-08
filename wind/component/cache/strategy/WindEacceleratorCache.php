<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.cache.strategy.AbstractWindCache');
L::import('WIND:component.cache.operator.WindEaccelerator');
/**
 * Eaccelerator是一款php加速器、优化器、编码器及动态内容缓存。
 * WindEaccelerator实现Eaccelerator动态内容缓存功能。
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindEacceleratorCache extends AbstractWindCache {
	
	/**
	 * @var WindEaccelerator
	 */
	protected $eaccelerator = null;
	
	public function __construct() {
		$this->eaccelerator = new WindEaccelerator();/* @var WindEaccelerator*/
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#set()
	 */
	public function set($key, $value, $expires = 0, IWindCacheDependency $denpendency = null) {
		return $this->eaccelerator->set($this->buildSecurityKey($key), $this->storeData($value, $expires, $denpendency), $expires);
	}
	/* 
	 * @see wind/component/cache/base/IWindCache#fetch()
	 */
	public function get($key) {
		return $this->getDataFromMeta($key, unserialize($this->eaccelerator->get($this->buildSecurityKey($key))));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#delete()
	 */
	public function delete($key) {
		return $this->eaccelerator->delete($this->buildSecurityKey($key));
	}
	
	/* 
	 * @see wind/component/cache/base/IWindCache#flush()
	 */
	public function flush() {
		$this->eaccelerator->flush();
	}
}