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
	const TABLEPREFIX = 'tablePrefix';
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
		$this->_dsn = $dsn;
		$this->_user = $username;
		$this->_pwd = $password;
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
		return $this->_dbHandle;
	}

	/**
	 * @param int $attribute
	 * @return string
	 * */
	public function getAttribute($attribute = '') {
		if (!$attribute) return;
		if ($this->_dbHandle !== null) {
			return $this->_dbHandle->getAttribute($attribute);
		} else
			return isset($this->_attributes[$attribute]) ? $this->_attributes[$attribute] : '';
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @return 
	 * */
	public function setAttribute($attribute, $value = null) {
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
			$this->_driverName = $this->_dbHandle->getAttribute(PDO::ATTR_DRIVER_NAME);
		} elseif (($pos = strpos($this->_dsn, ':')) !== false) {
			$this->_driverName = strtolower(substr($this->_dsn, 0, $pos));
		}
		return $this->_driverName;
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
	 * @param string $sql | SQL statement
	 * @return row count
	 */
	public function execute($sql) {
		try {
			return $this->getDbHandle()->exec($sql);
		} catch (PDOException $e) {
			$this->close();
			Wind::log('component.db.WindConnection.excute.', WindLogger::LEVEL_TRACE, 'component.db');
			throw new WindDbException($e->getMessage());
		}
	}

	/**
	 * @param string $sql | SQL statement 
	 * @return PDOStatement
	 */
	public function query($sql) {
		try {
			$statement = $this->getDbHandle()->query($sql);
			return new WindResultSet($statement);
		} catch (PDOException $e) {
			throw new WindDbException();
		}
	}

	/**
	 * @param array $array
	 */
	public function quoteArray($array) {
		return $this->getDbHandle()->filterArray($array);
	}

	/**
	 * @param string $string
	 */
	public function quote($string) {
		return $this->getDbHandle()->quote($string);
	}

	/**
	 * 关闭数据库连接
	 */
	public function close() {
		$this->_dbHandle = null;
	}

	/**
	 * 初始化DB句柄
	 * @throws Exception
	 * @return 
	 */
	public function init() {
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
			$this->_dbHandle = new $dbHandleClass($this->_dsn, $this->_user, $this->_pwd, (array) $this->_attributes);
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_dbHandle->setCharset($this->_charset);
			Wind::log("component.db.WindConnection._init() \r\n dsn: " . $this->_dsn() . " \r\n username: " . $this->_user . " \r\n password: " . $this->_pwd . " \r\n tablePrefix: " . $this->_tablePrefix, WindLogger::LEVEL_DEBUG);
		} catch (PDOException $e) {
			$this->close();
			Wind::log("component.db.WindConnection._init() Initalize DB handle failed.", WindLogger::LEVEL_TRACE);
			throw new WindDbException($e->getMessage());
		}
	}

	/* (non-PHPdoc)
	 * @see WindComponentModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		if (!$this->_dsn) $this->_dsn = $this->getConfig(self::DSN, '', $this->_dsn);
		if (!$this->_user) $this->_user = $this->getConfig(self::USER, '', $this->_user);
		if (!$this->_pwd) $this->_pwd = $this->getConfig(self::PWD, '', $this->_pwd);
		if (!$this->_enableLog) $this->_enableLog = $this->getConfig(self::ENABLELOG, '', $this->_enableLog);
		if (!$this->_charset) $this->_charset = $this->getConfig(self::CHARSET, '', $this->_charset);
		if (!$this->_tablePrefix) $this->_tablePrefix = $this->getConfig(self::TABLEPREFIX, '', $this->_tablePrefix);
	}
}
?>