<?php
Wind::import("WIND:component.db.exception.WindDbException");
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindConnection {
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
	public function __construct($dsn, $username, $password) {
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
		$this->_init();
		return $this->_dbHandle;
	}

	/**
	 * @param int $attribute
	 * @return string
	 * */
	public function getAttribute($attribute) {
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
	public function setAttribute($attribute, $value) {
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
			return $this->_dbHandle->getAttribute(PDO::ATTR_DRIVER_NAME);
		} elseif (($pos = strpos($this->_dsn, ':')) !== false) {
			return strtolower(substr($this->_dsn, 0, $pos));
		}
	}

	/**
	 * @return the $enableLog
	 */
	public function getEnableLog() {
		return $this->_enableLog;
	}

	/**
	 * @return the $tablePrefix
	 */
	public function getTablePrefix() {
		return $this->_tablePrefix;
	}

	/**
	 * @return the $charset
	 */
	public function getCharset() {
		return $this->_charset;
	}

	/**
	 * @param boolean $enableLog
	 */
	public function setEnableLog($enableLog) {
		$this->_enableLog = $enableLog;
	}

	/**
	 * @param string $tablePrefix
	 */
	public function setTablePrefix($tablePrefix) {
		$this->_tablePrefix = $tablePrefix;
	}

	/**
	 * @param string $charset
	 */
	public function setCharset($charset) {
		$this->_charset = $charset;
	}

	/**
	 * 初始化DB句柄
	 * @throws Exception
	 * @return 
	 */
	private function _init() {
		if ($this->_dbHandle === null) {
			if (empty($this->_dsn)) throw new WindDbException('WindConnection._connectionString is required.');
			try {
				$driverName = $this->getDriverName();
				if ($driverName) {
					$dbHandleClass = "WIND:component.db." . $driverName . ".Wind" . ucfirst($driverName) . "PdoAdapter";
					$dbHandleClass = Wind::import($dbHandleClass);
				} else
					$dbHandleClass = 'PDO';
				if (!$dbHandleClass) {
					throw new WindDbException('The db handle class path \'' . $dbHandleClass . '\' is not exist.');
				}
				$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->_dbHandle = new $dbHandleClass($this->_dsn, $this->_user, $this->_pwd, (array) $this->_attributes);
				$this->_dbHandle->setCharset($this->_charset);
			} catch (PDOException $e) {
				throw new WindDbException($e->getMessage());
			}
		}
	}
}
?>