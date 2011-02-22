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

	protected $dbConfig = 'WIND:component.db.db_config';

	protected $dbConfigSuffix = 'xml';

	protected $dbDefinition = null;

	protected $cacheClass = 'WIND:component.cache.stored.WindMemcache';

	protected $cacheConfig = 'WIND:component.cache.cache_config';

	protected $cacheConfigSuffix = 'xml';

	protected $cacheDefinition = null;

	protected $isDataCache = true;

	/**
	 * @var WindConnectionManager 分布式管理与数据库驱动工厂
	 */
	protected $dbHandler = null;

	protected $cacheHandler = null;

	/* (non-PHPdoc)
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
		if ($this->dbDefinition === null) {
			L::import('WIND:core.factory.WindComponentDefinition');
			$definition = new WindComponentDefinition();
			$definition->setPath($this->dbClass);
			$definition->setScope(WindComponentDefinition::SCOPE_SINGLETON);
			$definition->setAlias($this->dbClass);
			$definition->setConfig(array(WindComponentDefinition::RESOURCE => $this->dbConfig, 
				WindComponentDefinition::SUFFIX => $this->dbConfigSuffix));
			$this->dbDefinition = $definition;
		}
		return $this->dbDefinition;
	}

	/**
	 * 获得Cache类定义
	 * @return WindComponentDefinition
	 */
	public function getCacheDefinition() {
		if ($this->cacheDefinition === null) {
			L::import('WIND:core.factory.WindComponentDefinition');
			$definition = new WindComponentDefinition();
			$definition->setPath($this->cacheClass);
			$definition->setScope(WindComponentDefinition::SCOPE_SINGLETON);
			$definition->setAlias($this->cacheClass);
			$definition->setConfig(array(WindComponentDefinition::RESOURCE => $this->cacheConfig, 
				WindComponentDefinition::SUFFIX => $this->cacheConfigSuffix));
			$this->cacheDefinition = $definition;
		}
		return $this->cacheDefinition;
	}

	/**
	 * 获得需要缓存的处理的方法名称数组
	 * 
	 * @return multitype:
	 */
	public function getCacheMethods() {
		return array();
	}

}

?>