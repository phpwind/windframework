<?php
Wind::import('WIND:core.factory.proxy.WindClassProxy');
Wind::import('WIND:core.factory.WindFactory');
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
			$className = Wind::import($_path);
			$daoInstance = WindFactory::createInstance($className);
			$daoInstance->setDbHandler($this->createDbHandler($daoInstance));
			if (!$daoInstance->getIsDataCache()) return $daoInstance;
			$daoInstance->setCacheHandler($this->createCacheHandler($daoInstance));
			$daoInstance->setClassProxy(new WindClassProxy());
			$daoInstance = $daoInstance->getClassProxy();
			$this->registerCacheListener($daoInstance);
			return $daoInstance;
		} catch (Exception $exception) {
			throw new WindDaoException($exception->getMessage());
		}
	}

	/**
	 * 注册Dao缓存监听
	 * @param AbstractWindDao daoInstance
	 */
	private function registerCacheListener($daoInstance) {
		$caches = (array) $daoInstance->getCacheMethods();
		foreach ($caches as $classMethod => $classPath) {
			if (!$classMethod) continue;
			if ($classPath === 'default')
				$_className = Wind::import('COM:dao.listener.WindDaoCacheListener');
			else
				$_className = Wind::import($classPath);
			if (!$_className) continue;
			$daoInstance->registerEventListener($classMethod, new $_className($daoInstance));
		}
	}

	/**
	 * 返回DbHandler
	 * @param AbstractWindDao $daoObject
	 * @return WindConnection
	 */
	protected function createDbHandler($daoObject) {
		$_dbClass = $daoObject->getDbClass();
		$_connection = null;
		if (!isset($this->dbConnections[$_dbClass])) {
			$this->createWindFactory();
			$defintion = $daoObject->getDbDefinition();
			$this->windFactory->addClassDefinitions($defintion);
			$this->dbConnections[$_dbClass] = $this->windFactory->getInstance($defintion->getAlias());
		}
		return $this->getHandler($_dbClass);
	}

	/**
	 * @param string $key
	 * @return WindConnection
	 */
	private function getHandler($key) {
		$connection = $this->dbConnections[$key];
		if ($connection === null) return null;
		if ($connection instanceof WindConnectionManager) {
			return $connection->getConnection();
		} elseif ($connection instanceof WindConnection) {
			return $connection;
		}
	}

	/**
	 * 返回Cache对象
	 * @param AbstractWindDao $daoObject
	 * @return AbstractWindCache
	 */
	protected function createCacheHandler($daoObject) {
		$_cacheClass = $daoObject->getCacheClass();
		if (!isset($this->caches[$_cacheClass])) {
			$this->createWindFactory();
			$defintion = $daoObject->getCacheDefinition();
			$this->windFactory->addClassDefinitions($defintion);
			$this->caches[$_cacheClass] = $this->windFactory->getInstance($defintion->getAlias());
		}
		return $this->caches[$_cacheClass];
	}

	/**
	 * @return WindFactory
	 */
	private function createWindFactory() {
		if ($this->windFactory === null) {
			Wind::import('WIND:core.factory.WindComponentFactory');
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