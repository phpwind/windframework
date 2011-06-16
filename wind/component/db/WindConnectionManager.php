<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-1
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:component.exception.WindSqlException');
Wind::import('WIND:component.db.drivers.IWindDbConfig');
Wind::import('WIND:core.WindComponentModule');
/**
 * 实现分步式数据库操作管理及由静态工厂返回相应的数据库适配器
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindConnectionManager extends WindComponentModule {
	/**
	 * @var array 数据库连接池
	 */
	public $linked = array();
	/**
	 * @var AbstractWindDbAdapter
	 */
	private $connection = null;
	/**
	 * 取得数据库服务器
	 * @param string $identify 数据库标识
	 * @param string $type 是否是主从
	 * @return AbstractWindDbAdapter
	 */
	public function getConnection($identify = '', $type = IWindDbConfig::MASTER) {
		$connections = $this->getConnections();
		if ($identify && empty($connections[$identify])) {
			throw new WindSqlException($identify, WindSqlException::DB_CONNECT_NOT_EXIST);
		}
		$identify = $identify ? $identify : $this->getRandomDbDriverIdentify($type);
		if (empty($this->linked[$identify])) {
			$config = $connections[$identify];
			$drivers = array('mssql'=>'WIND:component.db.drivers.mssql.WindMsSql','mysql'=>'WIND:component.db.drivers.mysql.WindMySql');
			$class = Wind::import($drivers[strtolower($config[IWindDbConfig::DRIVER])]);
			if (false === class_exists($class)) {
				throw new WindSqlException($class, WindSqlException::DB_DRIVER_NOT_EXIST);
			}
			$this->linked[$identify] = new $class($config);
		}
		return $this->connection = $this->linked[$identify];
	}
	/**
	 * 取得主服务器
	 * @return WindDbAdapter
	 */
	public function getMasterConnection() {
		return 1 < count($this->getConnections()) ? $this->getConnection('', IWindDbConfig::MASTER) : $this->getConnection('', '');
	}
	/**
	 * 取得从服务器
	 * @return WindDbAdapter
	 */
	public function getSlaveConnection() {
		return 1 < count($this->getConnections()) ? $this->getConnection('', IWindDbConfig::SLAVE) : $this->getConnection('', '');
	}

	/**
	 * 取得当前正在执行的数据库操作句柄
	 * @return WindDbAdapter
	 */
	public function getCurrentConnection(){
		return $this->connection;
	}
	
	/**
	 * 取得数据库配置
	 * @param string $name
	 * @param string $subname
	 * @return array|string
	 */
	public function getConnections($name='',$subname=''){
		$connections = $this->getConfig()->getConfig();;
		if(empty($connections) || false === is_array($connections)){
			throw new WindSqlException('', WindSqlException::DB_CONN_FORMAT);
		}
		if($name){
			return $subname ? $connections[$subname] : $connections[$name];
		}
		return $connections;
	}
	/**
	 * 随机取得数据库配置
	 * @param string $type 是否是主从服务器
	 * @return array
	 */
	private function getRandomDbDriverIdentify($type = '') {
		$masterSlave = $this->getMasterSlave();
		$connections = $this->getConnections();
		$connections = (empty($masterSlave) || empty($type)) ? $connections : $masterSlave[$type];
		return $this->getConfigIdentifyByPostion($connections, mt_rand(0, count($connections) - 1));
	}
	/**
	 * 查看是是否要主从数据库设置，并按主从配置返回数据库配置信息
	 * @return array
	 */
	private function getMasterSlave() {
		$array = array();
		foreach ($this->getConnections() as $key => $value) {
			if (isset($value[IWindDbConfig::TYPE]) && in_array($value[IWindDbConfig::TYPE], array(
				IWindDbConfig::MASTER, IWindDbConfig::SLAVE))) {
				$array[$value[IWindDbConfig::TYPE]][$key] = $value;
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