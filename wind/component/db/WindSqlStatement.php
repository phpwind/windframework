<?php
Wind::import("WIND:component.db.WindResultSet");
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindSqlStatement {
	/**
	 * @var WindConnection
	 */
	private $_connection;
	/**
	 * @var PDOStatement
	 */
	private $_statement = null;
	/**
	 * sql语句字符串
	 *
	 * @var string
	 */
	private $_queryString;
	/**
	 * PDO类型映射
	 *
	 * @var array
	 */
	private $_typeMap = array(
		'boolean' => PDO::PARAM_BOOL, 
		'integer' => PDO::PARAM_INT, 
		'string' => PDO::PARAM_STR, 
		'NULL' => PDO::PARAM_NULL);

	/**
	 * @param WindConnection $connection
	 * @param string $query
	 */
	public function __construct($connection, $query) {
		$this->_connection = $connection;
		$this->setQueryString($query);
	}

	/**
	 * 参数绑定
	 * @param string $parameter
	 * @param string $variable
	 * @param string $data_type
	 * @param int $length
	 * @param $driver_options
	 * @return 
	 */
	public function bindParam($parameter, &$variable, $data_type = null, $length = null, $driver_options = null) {
		try {
			Wind::log("component.db.WindSqlStatement.bindParam. parameter:" . $parameter . " variable:" . $variable, WindLogger::LEVEL_INFO, "component.db");
			$this->init();
			if ($data_type === null) {
				$data_type = $this->_getPdoDataType($variable);
			}
			if ($length === null)
				$this->getStatement()->bindParam($parameter, $variable, $data_type);
			else
				$this->getStatement()->bindParam($parameter, $variable, $data_type, $length, $driver_options);
			return $this;
		} catch (PDOException $e) {
			Wind::log("component.db.WindSqlStatement.bindParam. exception message:" . $e->getMessage(), WindLogger::LEVEL_TRACE, "component.db");
			throw new WindDbException($e->getMessage());
		}
	}

	/**
	 * @param string $parameter
	 * @param string $value
	 * @param int $data_type
	 * @return
	 */
	public function bindValue($parameter, $value, $data_type = null) {
		try {
			Wind::log("component.db.WindSqlStatement.bindValue. parameter:" . $parameter . " variable:" . $value, WindLogger::LEVEL_INFO, "component.db");
			$this->init();
			if ($data_type === null) {
				$data_type = $this->_getPdoDataType($value);
			}
			$this->getStatement()->bindValue($parameter, $value, $data_type);
			return $this;
		} catch (PDOException $e) {
			Wind::log("component.db.WindSqlStatement.bindValue. exception message:" . $e->getMessage(), WindLogger::LEVEL_TRACE, "component.db");
			throw new WindException($e->getMessage());
		}
	}

	/**
	 * @param array $values
	 */
	public function bindValues($values) {
		foreach ($values as $key => $value) {
			$this->bindValue($key, $value, $this->_getPdoDataType($value));
		}
		return $this;
	}

	/**
	 * 执行SQL语句，并返回查询结果
	 * @param array $params
	 * @param int $fetchMode
	 * @param int $fetchType
	 * @return WindResultSet
	 */
	public function query($params = array(), $fetchMode = 0, $fetchType = 0) {
		$this->execute($params, false);
		return new WindResultSet($this, $fetchMode, $fetchType);
	}

	/**
	 * 执行sql，$params为变量信息,并返回结果集
	 * @param array $params
	 * @param boolean $rowCount
	 * @return rowCount
	 */
	public function execute($params = array(), $rowCount = true) {
		try {
			$this->init();
			Wind::log("component.db.WindSqlStatement.execute.", WindLogger::LEVEL_INFO, "component.db");
			if (empty($params)) {
				$this->getStatement()->execute();
			} else
				$this->getStatement()->execute($params);
			$_result = $rowCount ? $this->getStatement()->rowCount() : true;
			Wind::log("component.db.WindSqlStatement.execute return value:" . $_result, WindLogger::LEVEL_DEBUG, "component.db");
			return $_result;
		} catch (PDOException $e) {
			Wind::log("component.db.WindSqlStatement.execute throw exception,exception message: " . $e->getMessage(), WindLogger::LEVEL_TRACE, "component.db");
			throw new WindDbException($e->getMessage());
		}
	}

	/**
	 * @param string $queryString
	 * @return WindSqlStatement
	 */
	public function setQueryString($queryString) {
		if (!$queryString) return $this;
		if ($_prefix = $this->getConnection()->getTablePrefix()) {
			$queryString = preg_replace('/{{(.*?)}}/', $_prefix . '\1', $queryString);
		}
		$this->_queryString = $queryString;
		return $this;
	}

	/**
	 * @return the $_queryString
	 */
	public function getQueryString() {
		return $this->_queryString;
	}

	/**
	 * @return WindConnection
	 */
	public function getConnection() {
		return $this->_connection;
	}

	/**
	 * @return PDOStatement
	 */
	public function getStatement() {
		return $this->_statement;
	}

	/**
	 * @return
	 */
	public function init() {
		if ($this->_statement === null) {
			try {
				Wind::log("component.db.WindSqlStatement._init Initialize DBStatement. ", WindLogger::LEVEL_INFO, "component.db");
				Wind::profileBegin("component.db.WindSqlStatement._init", " SQL: " . $this->getQueryString(), "component.db");
				$this->getConnection()->init();
				$this->_statement = $this->getConnection()->getDbHandle()->prepare($this->getQueryString());
				Wind::profileEnd('component.db.WindSqlStatement._init');
				Wind::log("component.db.WindSqlStatement._init Initialize DBStatement. This statement is " . get_class($this->_statement), WindLogger::LEVEL_DEBUG, "component.db");
			} catch (PDOException $e) {
				Wind::log("Component.db.WindSqlStatement._init Initialize DBStatement 
					failed.", WindLogger::LEVEL_TRACE, "component.db");
				throw new WindDbException("Initialization WindSqlStatement failed.");
			}
		}
	}

	/**
	 * @param variable
	 * @param data_type
	 */
	private function _getPdoDataType($variable) {
		return isset($this->_typeMap[gettype($variable)]) ? $this->_typeMap[gettype($variable)] : PDO::PARAM_STR;
	}
}
?>