<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-1
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.exception.WindSqlException');
L::import('WIND:component.db.base.IWindDbConfig');
/**
 * 实现分步式数据库操作管理及由静态工厂返回相应的数据库适配器
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindConnectionManager extends WindComponentModule {
	/**
	 * @var array 数据库配置
	 */
	private $config = array();
	/**
	 * @var array 数据库连接池
	 */
	private $linked = array();
	/**
	 * @var WindDbAdapter
	 */
	private $connection = null;
	public function __construct(array $config = array()) {
		if ($config) {
			$this->checkDbConfig($config);
			$this->config = $config;
		}
	}
	
	/**
	 * 取得数据库服务器
	 * @param string $identify 数据库标识
	 * @param string $type 是否是主从
	 * @return resource
	 */
	public function getConnection($identify = '', $type = IWindDbConfig::CONN_MASTER) {
		$this->getDbConfig();
		$connections = $this->config[IWindDbConfig::CONNECTIONS];
		$drivers = $this->config[IWindDbConfig::DRIVERS];
		$builders = $this->config[IWindDbConfig::BUILDERS];
		if ($identify && empty($connections[$identify])) {
			throw new WindSqlException($identify, WindSqlException::DB_CONNECT_NOT_EXIST);
		}
		$identify = $identify ? $identify : $this->getRandomDbDriverIdentify($type);
		if (empty($this->linked[$identify])) {
			$dbConfig = $connections[$identify];
			$driver = $drivers[$dbConfig[IWindDbConfig::CONN_DRIVER]];
			$driverPath = $driver[IWindDbConfig::DRIVER_CLASS];
			$class = L::import($driverPath);
			if (false === class_exists($class)) {
				throw new WindSqlException($class, WindSqlException::DB_DRIVER_NOT_EXIST);
			}
			$builder = $builders[$driver[IWindDbConfig::DRIVER_BUILDER]];
			$this->linked[$identify] = new $class($dbConfig, $driver, $builder);
		}
		return $this->connection = $this->linked[$identify];
	}
	
	/**
	 * 取得主服务器
	 * @return WindDbAdapter
	 */
	public function getMasterConnection() {
		return 1 < count($this->config[IWindDbConfig::CONNECTIONS]) ? $this->getConnection('', IWindDbConfig::CONN_MASTER) : $this->getConnection('', '');
	}
	
	/**
	 * 取得从服务器
	 * @return WindDbAdapter
	 */
	public function getSlaveConnection() {
		return 1 < count($this->config[IWindDbConfig::CONNECTIONS]) ? $this->getConnection('', IWindDbConfig::CONN_SLAVE) : $this->getConnection('', '');
	}
	
	/**
	 * 取得当前正在执行的数据库操作句柄
	 * @return WindDbAdapter
	 */
	public function getCurrentConnection(){
		return $this->connection;
	}
	
	public function getDbConfig() {
		if (empty($this->config)) {
			$config = $this->getConfig()->getConfig();
			$this->checkConfig($config);
			$this->config = $config;
		}
		return $this->config;
	}
	/**
	 * 设置db配置
	 * @param array $config
	 */
	private function checkDbConfig(array $config) {
		if ((!isset($config[IWindDbConfig::CONNECTIONS]) || !isset($config[IWindDbConfig::DRIVERS]) || !isset($config[IWindDbConfig::BUILDERS]))) {
			throw new WindSqlException('', WindSqlException::DB_CONN_FORMAT);
		}
	}
	
	/**
	 * 随机取得数据库配置
	 * @param string $type 是否是主从服务器
	 * @return array
	 */
	private function getRandomDbDriverIdentify($type = '') {
		$masterSlave = $this->getMasterSlave();
		$connections = $this->config[IWindDbConfig::CONNECTIONS];
		$connections = (empty($masterSlave) || empty($type)) ? $connections : $masterSlave[$type];
		return $this->getConfigIdentifyByPostion($connections, mt_rand(0, count($connections) - 1));
	}
	
	/**
	 * 查看是是否要主从数据库设置，并按主从配置返回数据库配置信息
	 * @return array
	 */
	private function getMasterSlave() {
		$array = array();
		foreach ($this->config[IWindDbConfig::CONNECTIONS] as $key => $value) {
			if (isset($value[IWindDbConfig::CONN_TYPE]) && in_array($value[IWindDbConfig::CONN_TYPE], array(
				IWindDbConfig::CONN_MASTER, IWindDbConfig::CONN_SLAVE))) {
				$array[$value[IWindDbConfig::CONN_TYPE]][$key] = $value;
			}
		}
		return $array;
	}
	
	/**
	 *根据config的pos返回key
	 * @param array $config 数据库配置
	 * @param int $pos config的位置
	 * @return string 返回config的key
	 */
	private function getConfigIdentifyByPostion($config, $pos = 0) {
		$i = 0;
		foreach ((array) $config as $key => $value) {
			if ($pos === $i) return $key;
			$i++;
		}
		return '';
	}

}