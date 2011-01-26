<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */


/**
 * 缓存调用工厂
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindCacheFactory{
	/**
	 * @var array 实列化缓存对象
	 */
	public static $cache = array();
	/**
	 * 获取存储类型缓存
	 * @param string $cache 缓存名称
	 * @param string $type 缓存类别
	 * @param array $config 缓存配置
	 * @param boolean $reload 是否重新加载缓存类
	 * @return IWindCache|Cache
	 */
	public function storedFactory($cache,$config = array(),$reload = false){
		return $this->cacheFactory($cache,'stored',$config,$reload);
	}
	
	/**
	 * 获取视图型缓存
	 * @param string $cache 缓存名称
	 * @param string $type 缓存类别
	 * @param array $config 缓存配置
	 * @param boolean $reload 是否重新加载缓存类
	 * @return IWindCache|Cache
	 */
	public function viewFactory($cache,$config =array(),$reload = false){
		return $this->cacheFactory($cache,'view',$config,$reload);
	}
	
	/**
	 * 缓存工厂
	 * @param string $cache 缓存名称
	 * @param string $type 缓存类别
	 * @param array $config 缓存配置
	 * @param boolean $reload 是否重新加载缓存类
	 * @return IWindCache|Cache
	 */
	public function cacheFactory($cache,$type='stored',array $config = array(),$reload = false){
		$class = L::import('WIND:component.cache.'.$type.$cache);
		if(false === class_exists($class)){
			throw new WindException($class.' is not exists');
		}
		if($reload){
			return $config ? new $class($config) : new $class();
		}
		if(isset(self::$cache[$class])){
			return self::$cache[$class];
		}
		return self::$cache[$class] = $config ? new $class($config) : new $class();
	}
}