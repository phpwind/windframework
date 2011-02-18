<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 以标准的方式使用不同的数据访问技术,方便不同数据库持久化技术间切换及各种技术中特定的异常
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindDaoSupport {

	/**
	 * @var WindTemplate dao模板
	 */
	protected $template = null;

	/**
	 * @var WindCacheFactory 缓存工厂
	 */
	protected $cacheFactory = null;

	public function __construct() {
		$this->init();
	}

	public abstract function init();

	/**
	 * 取得dao模板
	 * @return WindTemplate
	 */
	public abstract function getTemplate();

	/**
	 * 获取缓存工厂
	 */
	public function getCacheFactory() {
		$class = L::import('WIND:component.cache.WindCacheFactory');
		if (null == $this->cacheFactory) {
			$this->cacheFactory = new $class();
		}
		return $this->cacheFactory;
	}
}
