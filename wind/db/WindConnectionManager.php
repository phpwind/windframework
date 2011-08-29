<?php
Wind::import("COM:db.WindConnection");
/**
 * 配置格式为：
 * <WIND>
 * <connection name='master'>
 * <dsn>mysql:host=localhost;dbname=test</dsn>
 * <user>root</user>
 * <pwd>root</pwd>
 * <charset>utf8</charset>
 * <tablePrefix>pw_</tablePrefix>
 * </connection>
 * <connection name='slave'>
 * <dsn>mysql:host=localhost;dbname=test</dsn>
 * <user>root</user>
 * <pwd>root</pwd>
 * <charset>utf8</charset>
 * <tablePrefix>pw_</tablePrefix>
 * </connection>
 * </WIND>
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindConnectionManager extends WindConnection {
	/**
	 * 链接池
	 * @var array
	 */
	private $connPool = array();

	/* (non-PHPdoc)
	 * @see WindConnection::init()
	 */
	public function init() {
		try {
			$driverName = $this->getDriverName();
			$dbHandleClass = "WIND:component.db." . $driverName . ".Wind" . ucfirst($driverName) . "PdoAdapter";
			$dbHandleClass = Wind::import($dbHandleClass);
			$this->_dbHandle = new $dbHandleClass($this->_dsn, $this->_user, $this->_pwd, 
				(array) $this->_attributes);
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_dbHandle->setCharset($this->_charset);
			if (WIND_DEBUG & 2) {
				$reflection = new ReflectionClass(get_class($this));
				$properties = $reflection->getProperties();
				Wind::getApp()->getComponent('windLogger')->info(
					"component.db.WindConnection.init() Initialize db connection success." . WindString::varToString(
						$properties), 'component.db');
			}
		} catch (PDOException $e) {
			$this->close();
			throw new WindDbException($e->getMessage());
		}
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		if ($config) {
			if (is_string($config))
				$config = Wind::getApp()->getComponent('configParser')->parse($config);
			if (!empty($this->_config)) {
				$this->_config = array_merge($this->_config, (array) $config);
			} else
				$this->_config = $config;
		}
	}
}