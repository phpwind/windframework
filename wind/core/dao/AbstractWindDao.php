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

	protected $cacheClass = 'WIND:component.cache.stored.WindFileCache';

	protected $isDataCache = true;

	protected $dbHandler = null;

	/* (non-PHPdoc)
	 * @see WindModule::getWriteTableForGetterAndSetter()
	 */
	protected function getWriteTableForGetterAndSetter() {
		return array('dbClass', 'dbConfig', 'cacheClass', 'isDataCache', 'dbHandler');
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
	 * 获得需要缓存的处理的方法名称数组
	 * 
	 * @return multitype:
	 */
	public function getCacheMethods() {
		return array();
	}

}

?>