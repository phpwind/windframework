<?php
/**
 * 监视特定的数据库表，以便在该表发生更改时，自动从 Cache 中删除与该表关联的项。数据库表发生更改时，将自动删除缓存项
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
Wind::import('WIND:component.cache.dependency.AbstractWindCacheDependency');
class WindDbCacheDependency extends AbstractWindCacheDependency{
	private $sql = '';
	private $config = '';
	private $connection = null;
	
	public function __construct($sql, $config = '') {
		$sql && $this->sql = $sql;
		$config && $this->config = $this->config;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see WindCacheDependency::notifyDependencyChanged()
	 */
	protected function notifyDependencyChanged() {
		if (!$this->sql) return null;
		return $this->getConnection()->query($this->sql)->fetchAll();
	}
	
	/**
	 * 获得链接对象
	 * //TODO DB链接对象～获取全局统一。。
	 */
	private function getConnection() {
		if ($this->connection != null ) return $this->connection;
		if ($this->config) {
			$this->connection = new WindConnection();
			$this->connection->setConfig($this->dbConfig);
			$this->connection->init();
			return $this->connection;
		}
	}
	
	
}