<?php

/**
 * 抽象DAO接口
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindDao extends WindModule {

	protected $dbClass = 'WIND:component.db.WindConnectionManager';

	protected $dbConfig = 'WIND:config.db_config';

	protected $dbTemplateClass = 'WIND:core.dao.dbtemplate.WindConnectionManagerBasedDbTemplate';

	protected $dbConfigSuffix = 'xml';

	protected $dbDefinition = null;

	protected $cacheClass = 'WIND:component.cache.strategy.WindDbCache';

	protected $cacheConfig = 'WIND:config.cache_config';

	protected $cacheConfigSuffix = 'xml';

	protected $isDataCache = true;

	/**
	 * @var IWindDbTemplate 分布式管理与数据库驱动工厂
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
		L::import('WIND:core.factory.WindComponentDefinition');
		$definition = new WindComponentDefinition();
		$definition->setPath($this->dbClass);
		$definition->setScope(WindComponentDefinition::SCOPE_SINGLETON);
		$definition->setAlias($this->dbClass);
		$definition->setConfig(array(WindComponentDefinition::RESOURCE => $this->dbConfig, 
			WindComponentDefinition::SUFFIX => $this->dbConfigSuffix));
		return $definition;
	}

	/**
	 * 获得Cache类定义
	 * @return WindComponentDefinition
	 */
	public function getCacheDefinition() {
		L::import('WIND:core.factory.WindComponentDefinition');
		$definition = new WindComponentDefinition();
		$definition->setPath($this->cacheClass);
		$definition->setScope(WindComponentDefinition::SCOPE_SINGLETON);
		$definition->setAlias($this->cacheClass);
		$definition->setConfig(array(WindComponentDefinition::RESOURCE => $this->cacheConfig, 
			WindComponentDefinition::SUFFIX => $this->cacheConfigSuffix));
		return $definition;
	}

	/**
	 * @return the $dbTemplateClass
	 */
	public function getDbTemplateClass() {
		return $this->dbTemplateClass;
	}

	/**
	 * 获得需要缓存的处理的方法名称数组
	 * 
	 * @return multitype:
	 */
	public function getCacheMethods() {
		return array();
	}

	/**
	 * @return IWindDbTemplate $dbHandler
	 */
	public function getDbHandler() {
		return $this->dbHandler;
	}

	/**
	 * @param IWindDbTemplate $dbHandler
	 */
	public function setDbHandler($dbHandler) {
		$this->dbHandler = $dbHandler;
	}

}

?>