<?php
Wind::import('WIND:core.WindModule');
/**
 * 抽象DAO接口
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindDao extends WindModule {

	protected $dbClass = 'WIND:component.db.WindConnection';

	protected $dbConfig = '';

	protected $cacheClass = '';

	protected $cacheConfig = '';

	protected $isDataCache = false;
	
	protected $dbDefinition = null;

	/**
	 * @var WindConnection 数据库链接兑现
	 */
	protected $dbHandler = null;

	/**
	 * @var AbstractWindCache 缓存操作句柄
	 */
	protected $cacheHandler = null;

	/* 
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
	 * @return multitype:
	 */
	public function getCacheMethods() {
		return array();
	}

	/**
	 * @return PDO $dbHandler
	 */
	public function getDbHandler() {
		return $this->dbHandler;
	}

	/**
	 * @param WindConnection $windConnection
	 */
	public function setDbHandler($windConnection) {
		$this->dbHandler = $windConnection->getDbHandle();
	}
}

?>