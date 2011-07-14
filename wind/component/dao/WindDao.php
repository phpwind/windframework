<?php
/**
 * 抽象DAO接口
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindDao extends WindModule {
	protected $dbClass = 'WIND:component.db.WindConnection';
	/**
	 * 链接配置文件或是配置数组
	 * @var string|array
	 */
	protected $dbConfig = '';
	protected $cacheClass = '';
	protected $cacheConfig = '';
	protected $isDataCache = false;
	protected $dbDefinition = null;
	/**
	 * @var WindConnection 数据库链接对象
	 */
	private $connection = null;
	/**
	 * @var AbstractWindCache 缓存操作句柄
	 */
	private $cacheHandler = null;

	/**
	 * @see WindModule::getWriteTableForGetterAndSetter()
	 */
	protected function getWriteTableForGetterAndSetter() {
		return array('dbClass', 'dbConfig', 'cacheClass', 'isDataCache', 'dbHandler', 'cacheConfig', 'cacheHandler');
	}

	/**
	 * 获得DB类定义
	 * @return WindComponentDefinition
	 */
	public function getDbDefinition() {
		Wind::import('WIND:core.factory.WindComponentDefinition');
		$definition = new WindComponentDefinition();
		$definition->setPath($this->dbClass);
		$definition->setScope(WindComponentDefinition::SCOPE_SINGLETON);
		$definition->setAlias($this->dbClass);
		$definition->setInitMethod('init');
		if (is_array($this->dbConfig))
			$definition->setConfig($this->dbConfig);
		else
			$definition->setConfig(array(WindComponentDefinition::RESOURCE => $this->dbConfig));
		return $definition;
	}

	/**
	 * 获得Cache类定义
	 * @return WindComponentDefinition
	 */
	public function getCacheDefinition() {
		Wind::import('WIND:core.factory.WindComponentDefinition');
		$definition = new WindComponentDefinition();
		$definition->setPath($this->cacheClass);
		$definition->setScope(WindComponentDefinition::SCOPE_SINGLETON);
		$definition->setAlias($this->cacheClass);
		$definition->setConfig(array(WindComponentDefinition::RESOURCE => $this->cacheConfig));
		return $definition;
	}

	/**
	 * 获得需要缓存的处理的方法名称数组
	 * array('methodName1'=>'WIND:component')
	 * @return array
	 */
	public function getCacheMethods() {
		return array();
	}

	/**
	 * 获得链接对象
	 * @return WindConnection $connection
	 */
	public function getConnection() {
		return $this->connection;
	}

	/**
	 * 设置链接对象
	 * @param WindConnection $windConnection
	 */
	public function setConnection($windConnection) {
		$this->connection = $windConnection;
	}
}
?>