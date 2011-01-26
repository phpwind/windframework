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
	 * @return IWindCache
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
	 * @return Cache
	 */
	public function viewFactory($cache,$config =array(),$reload = false){
		return $this->cacheFactory($cache,'view',$config,$reload);
	}
	
	/**
	 * 获取缓存依赖
	 * @param string $dependency 缓存依赖名称
	 * @param array $extra 构造函数参数
	 * @param boolean $reload 是否重新加载缓存类
	 * @return IWindCacheDependency
	 */
	public function dependencyFactory($dependency, $extra = array(),$reload = false){
		return $this->cacheFactory($dependency,'dependency',$extra,$reload);
	}
	
	/**
	 * 缓存工厂
	 * @param string $name 类名称
	 * @param string $type 缓存类别
	 * @param array  $params 构造函数参数
	 * @param boolean $reload 是否重新加载缓存类
	 * @return IWindCache|Cache|IWindCacheDependency
	 */
	public function cacheFactory($name,$type='stored',array $params = array(),$reload = false){
		$class = L::import('WIND:component.cache.'.$type.$name);
		if(false === class_exists($class)){
			throw new WindException($class.' is not exists');
		}
		if($reload){
			return $params ? new $class($params) : new $class();
		}
		if(isset(self::$cache[$class])){
			return self::$cache[$class];
		}
		return self::$cache[$class] = $params ? new $class($params) : new $class();
	}
	
	
}