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

	protected $dbConfig = '';

	protected $dbTemplateClass = 'WIND:core.dao.dbtemplate.WindConnectionManagerBasedDbTemplate';

	protected $dbDefinition = null;

	protected $cacheClass = '';

	protected $cacheConfig = '';

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
		$definition->setConfig(array(WindComponentDefinition::RESOURCE => $this->dbConfig));
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
		$definition->setConfig(array(WindComponentDefinition::RESOURCE => $this->cacheConfig));
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
	 * array(cache=>array('methodName1','methodName2','methodName3'),
	 * clear=>array('methodName1','methodName2','methodName3'))
	 * @return multitype:
	 */
	public function getCacheMethods() {
		return array('cache' => array());
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