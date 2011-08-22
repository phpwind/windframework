<?php
Wind::import('COM:dao.exception.WindDaoException');
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
class WindDaoFactory extends WindModule {
	/**
	 * dao 实例数组
	 *
	 * @var array
	 */
	private $daos = array();
	/**
	 * dao路径信息
	 *
	 * @var string
	 */
	protected $daoResource = '';

	/**
	 * 返回Dao类实例
	 * $className接受两种形式呃参数如下
	 * 'namespace:path'
	 * 'className'
	 * 
	 * @param string $className
	 * @return WindDao
	 */
	public function getDao($className) {
		try {
			if (strpos($className, ":") === false && strpos($className, ".") === false) {
				$className = $this->getDaoResource() . '.' . $className;
			}
			if (isset($this->daos[$className])) return $this->daos[$className];
			
			$className = Wind::import($className);
			$daoInstance = WindFactory::createInstance($className);
			$this->createDbConnection($daoInstance);
			$this->createCacheHandler($daoInstance);
			return $daoInstance;
		} catch (Exception $exception) {
			throw new WindDaoException(
				'[component.dao.WindDaoFactory] create dao ' . $className . ' fail. Error message:' . $exception->getMessage());
		}
	}

	/**
	 * 获得dao存放的目录
	 * @return string $daoResource
	 */
	public function getDaoResource() {
		return $this->daoResource;
	}

	/**
	 * 设置dao的获取目录
	 * @param string $daoResource
	 */
	public function setDaoResource($daoResource) {
		$this->daoResource = $daoResource;
	}

	/**
	 * 注册Dao缓存监听
	 * @param WindDao daoInstance
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
	 * 创建db链接句柄
	 * 
	 * @param WindDao $daoObject
	 */
	protected function createDbConnection($daoObject) {
		$configName = $daoObject->getDBName();
		$config = $this->getSystemConfig()->getDbConfig($configName);
		if (!$config) throw new WindDbException(
			'[component.dao.WindDaoFactory.createDbConnection] (' . $configName . ')', 
			WindDbException::DB_CONN_NOT_EXIST);
		
		$path = $this->getConfig('class', '', 'COM:db.WindConnection', $config);
		$alias = $configName ? $path . $configName : $path . get_class($this);
		$definition = array('path' => $path, 'alias' => $alias, 'config' => $config, 'initMethod' => 'init', 'scope' => 'application');
		$this->getSystemFactory()->addClassDefinitions($alias, $definition);
		$daoObject->setDelayAttributes(array('connection' => array('ref' => $alias)));
	}

	/**
	 * 创建cache句柄
	 * 
	 * @param WindDao $daoObject
	 */
	protected function createCacheHandler($daoObject) {
		if (!($_className = $daoObject->getCacheClass())) return;
		$_classConfig = $daoObject->getCacheConfig();
		$_alias = $_className . '_' . md5((is_string($_classConfig) ? $_classConfig : serialize($_classConfig)));
		if (!$this->getSystemFactory()->checkAlias($_alias)) {
			$definition = array('path' => $_className, 'alias' => $_alias, 'initMethod' => 'init', 'scope' => 'singleton');
			$definition['config'] = is_array($_classConfig) ? $_classConfig : array('resource' => $_classConfig);
			$this->getSystemFactory()->addClassDefinitions($_alias, $definition);
		}
		$daoObject->setDelayAttributes(array('cacheHandler' => array('ref' => $_alias)));
		$daoObject = new WindClassProxy($daoObject);
		$this->registerCacheListener($daoObject);
	}
}
?>