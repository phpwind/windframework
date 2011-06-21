<?php
Wind::import("WIND:component.db.exception.WindDbException");
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindConnection extends WindComponentModule {
	const DSN = 'dsn';
	const USER = 'user';
	const PWD = 'pwd';
	const CHARSET = 'charset';
	const ENABLELOG = 'enablelog';
	const DRIVER = 'driver';
	/**
	 * PDO 链接字符串
	 * @var string
	 */
	private $_dsn;
	private $_driverName;
	private $_user;
	private $_pwd;
	private $_tablePrefix;
	private $_charset;
	private $_enableLog = false;
	/**
	 * @var array
	 */
	private $_attributes = array();
	/**
	 * @var PDO
	 */
	private $_dbHandle = null;

	/**
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($dsn = '', $username = '', $password = '') {
		$this->setDsn($dsn);
		$this->setUser($username);
		$this->setPwd($password);
	}

	/**
	 * 接受一条sql语句，并返回sqlStatement对象
	 * @param string $sql | sql语句
	 * @return WindSqlStatement
	 */
	public function createStatement($sql = null) {
		Wind::import("WIND:component.db.WindSqlStatement");
		return new WindSqlStatement($this, $sql);
	}

	/**
	 * @return PDO
	 */
	public function getDbHandle() {
		$this->_init();
		return $this->_dbHandle;
	}

	/**
	 * @param int $attribute
	 * @return string
	 * */
	public function getAttribute($attribute = '') {
		if (!$attribute) return;
		if ($this->getDbHandle() !== null) {
			return $this->getDbHandle()->getAttribute($attribute);
		} else
			return isset($this->_attributes[$attribute]) ? $this->_attributes[$attribute] : '';
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @return 
	 * */
	public function setAttribute($attribute = '', $value = '') {
		if (!$attribute) return;
		if ($this->_dbHandle !== null && $this->_dbHandle instanceof PDO) {
			$this->_dbHandle->setAttribute($attribute, $value);
		} else
			$this->_attributes[$attribute] = $value;
	}

	/**
	 * 返回DB驱动类型
	 * @return string
	 */
	public function getDriverName() {
		if ($this->_driverName) return $this->_driverName;
		if ($this->_dbHandle !== null) {
			$this->setDriverName($this->_dbHandle->getAttribute(PDO::ATTR_DRIVER_NAME));
		} elseif (($pos = strpos($this->getDsn(), ':')) !== false) {
			$this->setDriverName(strtolower(substr($this->getDsn(), 0, $pos)));
		} else {
			$this->setDriverName($this->getConfig()->getConfig(self::DRIVER));
		}
		return $this->_driverName;
	}

	/**
	 * @param string $driverName
	 */
	public function setDriverName($driverName) {
		$this->_driverName = $driverName;
	}

	/**
	 * @return the $enableLog
	 */
	public function getEnableLog() {
		return $this->_enableLog;
	}

	/**
	 * @param boolean $enableLog
	 */
	public function setEnableLog($enableLog) {
		$this->_enableLog = (boolean) $enableLog;
	}

	/**
	 * @return the $tablePrefix
	 */
	public function getTablePrefix() {
		return $this->_tablePrefix;
	}

	/**
	 * @param string $tablePrefix
	 */
	public function setTablePrefix($tablePrefix) {
		$this->_tablePrefix = $tablePrefix;
	}

	/**
	 * @return the $charset
	 */
	public function getCharset() {
		if ($this->_charset) return $this->_charset;
		$this->setCharset($this->getConfig()->getConfig(self::CHARSET));
		return $this->_charset;
	}

	/**
	 * @param string $charset
	 */
	public function setCharset($charset) {
		$this->_charset = $charset;
	}

	/**
	 * @return the $_dsn
	 */
	public function getDsn() {
		if ($this->_dsn) return $this->_dsn;
		$this->setDsn($this->getConfig()->getConfig(self::DSN));
		return $this->_dsn;
	}

	/**
	 * @param string $dsn
	 */
	public function setDsn($dsn) {
		$this->_dsn = $dsn;
	}

	/**
	 * @return the $_user
	 */
	public function getUser() {
		if ($this->_user) return $this->_user;
		$this->_user = $this->getConfig()->getConfig(self::USER);
		return $this->_user;
	}

	/**
	 * @param string $userName
	 */
	public function setUser($userName) {
		$this->_user = $userName;
	}

	/**
	 * @return the $_pwd
	 */
	public function getPwd() {
		if ($this->_pwd) return $this->_pwd;
		$this->setPwd($this->getConfig()->getConfig(self::PWD));
		return $this->_pwd;
	}

	/**
	 * @param string $_pwd
	 */
	public function setPwd($_pwd) {
		$this->_pwd = $_pwd;
	}

	/**
	 * 初始化DB句柄
	 * @throws Exception
	 * @return 
	 */
	private function _init() {
		if ($this->_dbHandle !== null) return;
		try {
			Wind::log("component.db.WindConnection._init() Initialize DB handle, set default attributes and charset.", WindLogger::LEVEL_INFO);
			$driverName = $this->getDriverName();
			if ($driverName) {
				$dbHandleClass = "WIND:component.db." . $driverName . ".Wind" . ucfirst($driverName) . "PdoAdapter";
				$dbHandleClass = Wind::import($dbHandleClass);
			} else
				$dbHandleClass = 'PDO';
			if (!$dbHandleClass) {
				throw new WindDbException('The db handle class path \'' . $dbHandleClass . '\' is not exist.');
			}
			$this->_dbHandle = new $dbHandleClass($this->getDsn(), $this->getUser(), $this->getPwd(), (array) $this->_attributes);
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_dbHandle->setCharset($this->getCharset());
			Wind::log("component.db.WindConnection._init() \r\n dsn: " . $this->getDsn() . " \r\n username: " . $this->_user . " \r\n  password: " . $this->_pwd . " \r\n tablePrefix: " . $this->_tablePrefix, WindLogger::LEVEL_DEBUG);
		} catch (PDOException $e) {
			Wind::log("component.db.WindConnection._init() Initalize DB handle failed.", WindLogger::LEVEL_TRACE);
			throw new WindDbException($e->getMessage());
		}
	}
}
?>