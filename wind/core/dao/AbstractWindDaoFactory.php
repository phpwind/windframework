<?php

L::import('WIND:core.factory.proxy.WindClassProxy');
/**
 * Dao工厂
 * 
 * 职责：
 * 创建DAO实例
 * 数据缓存部署实现
 * 创建数据访问连接对象
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindDaoFactory {

	protected $windFactory = null;

	protected $daoResource = '';

	protected $dbConnections = array();

	protected $caches = array();

	/**
	 * 返回Dao类实例
	 * 
	 * @param string $className
	 */
	public function getDao($className) {
		try {
			$_path = '';
			if (strpos($className, ":") !== false || strpos($className, ".") !== false) {
				$_path = $className;
			} elseif ($this->getDaoResource()) {
				$_path = $this->getDaoResource() . '.' . $className;
			} else {
				$_path = $className;
			}
			$className = L::import($_path);
			
			$daoInstance = WindFactory::createInstance($className);
			$daoInstance->setDbHandler($this->createDbHandler($daoInstance));
			if (!$daoInstance->getIsDataCache()) return $daoInstance;
			
			$daoInstance->setCacheHandler($this->createCacheHandler($daoInstance));
			$daoInstance->setClassProxy(new WindClassProxy());
			$daoInstance = $daoInstance->getClassProxy();
			$listener = new WindDaoCacheListener($daoInstance);
			foreach ($daoInstance->getCacheMethods() as $classMethod) {
				$daoInstance->registerEventListener($classMethod, $listener);
			}
			return $daoInstance;
		} catch (Exception $exception) {
			throw new WindDaoException($exception->getMessage());
		}
	}

	/**
	 * 获取DBHandler
	 * @param AbstractWindDao $daoObject
	 */
	protected function createDbHandler($daoObject) {
		$_dbClass = $daoObject->getDbClass();
		if (!isset($this->dbConnections[$_dbClass])) {
			$this->createFactory();
			$defintion = $daoObject->getDbDefinition();
			$this->windFactory->addClassDefinitions($defintion);
			$this->dbConnections[$_dbClass] = $this->windFactory->getInstance($defintion->getAlias());
		}
		return $this->dbConnections[$_dbClass];
	}

	/**
	 * 返回Cache对象
	 * @param AbstractWindDao $daoObject
	 * @return multitype:
	 */
	protected function createCacheHandler($daoObject) {
		$_cacheClass = $daoObject->getCacheClass();
		if (!isset($this->caches[$_cacheClass])) {
			$this->createFactory();
			$defintion = $daoObject->getCacheDefinition();
			$this->windFactory->addClassDefinitions($defintion);
			$cacheHander = $this->windFactory->getInstance($defintion->getAlias());
			$this->caches[$_cacheClass] = $cacheHander;
		}
		return $this->caches[$_cacheClass];
	}

	/**
	 * Enter description here ...
	 */
	private function createFactory() {
		if ($this->windFactory === null) {
			L::import('WIND:core.factory.WindComponentFactory');
			$this->windFactory = new WindComponentFactory();
		}
		return $this->windFactory;
	}

	/**
	 * @return the $daoResource
	 */
	public function getDaoResource() {
		return $this->daoResource;
	}

	/**
	 * @param field_type $daoResource
	 */
	public function setDaoResource($daoResource) {
		$this->daoResource = $daoResource;
	}

}

?>