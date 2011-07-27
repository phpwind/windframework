<?php
/**
 * 缓存依赖基类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class AbstractWindCacheDependency extends WindModule {
	
	/**
	 * 依赖的依据
	 * 
	 * @var mixed
	 */
	protected $data = '';
	
	/**
	 * @var string 依赖项的上次更改时间
	 */
	protected $lastModified;

	/**
	 * 初始化设置，设置依赖
	 */
	public function injectDependent() {
		$this->lastModified = time();
		$this->data = $this->notifyDependencyChanged();
	}

	/**
	 * 检查是否有变更
	 * 
	 * @return boolean
	 */
	public function hasChanged() {
		return $this->data != $this->notifyDependencyChanged();
	}
	
	/**
	 * 获得依赖数据
	 * 
	 * @return mixed
	 */
	public function getDenpendencyData() {
		return $this->data;
	}

	/**
	 * 获得最后更新时间
	 * @return int
	 */
	public function getLastModified() {
		return $this->lastModified;
	}

	/**
	 * 是否有变化
	 * 
	 * @return mixed 新的值
	 */
	protected abstract function notifyDependencyChanged();

}