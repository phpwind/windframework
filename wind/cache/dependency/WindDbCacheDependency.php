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
	private $configName = '';
	private $connection = null;
	
	public function __construct($sql, $configName = '') {
		$sql && $this->sql = $sql;
		$configName && $this->configName = $configName;
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
	 */
	private function getConnection() {
		if ( null != $this->connection) return $this->connection;
		$alias = 'db_' . $this->configName;
	    if (!$this->getSystemFactory()->checkAlias($alias)) {
			$config = $this->getSystemConfig()->getDbConfig($this->configName);
			$definition = array(
				'path' => $this->getConfig('class', '', 'COM:db.WindConnection', $config),
				'alias' => $alias,
				'config' => $config,
				'initMethod' => 'init',
				'scope' => 'application',
			);
			$this->getSystemFactory()->addClassDefinitions($alias, $definition);
		}
		$this->connection = $this->getSystemFactory()->getInstance($alias);
		return $this->connection;
	}
	
	
}