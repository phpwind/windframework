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
	private $_typeMap = array('boolean' => PDO::PARAM_BOOL, 'integer' => PDO::PARAM_INT, 'string' => PDO::PARAM_STR, 'NULL' => PDO::PARAM_NULL);
	private $_columns = array();

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
	 * @param string $dataType
	 * @param int $length
	 * @param $driverOptions
	 * @return 
	 */
	public function bindParam($parameter, &$variable, $dataType = null, $length = null, $driverOptions = null) {
		try {
			Wind::log("component.db.WindSqlStatement.bindParam. parameter:" . $parameter . " variable:" . $variable, WindLogger::LEVEL_INFO, "component.db");
			$this->init();
			if ($dataType === null) {
				$dataType = $this->_getPdoDataType($variable);
			}
			if ($length === null)
				$this->getStatement()->bindParam($parameter, $variable, $dataType);
			else
				$this->getStatement()->bindParam($parameter, $variable, $dataType, $length, $driverOptions);
			return $this;
		} catch (PDOException $e) {
			Wind::log("component.db.WindSqlStatement.bindParam. exception message:" . $e->getMessage(), WindLogger::LEVEL_TRACE, "component.db");
			throw new WindDbException($e->getMessage());
		}
	}

	/**
	 * 批量绑定变量
	 * 如果是一维数组，则使用key=>value的形式，key代表变量位置，value代表替换的值，而替换值需要的类型则通过该值的类型来判断---不准确
	 * 如果是一个二维数组，则允许，key=>array(0=>value, 1=>data_type, 2=>length, 3=>driver_options)的方式来传递变量。
	 * @param array $parameters 
	 * @return WindSqlStatement
	 */
	public function bindParams(&$parameters) {
		foreach ($parameters as $key => $value) {
			if (is_array($value)) {
				$dataType = isset($value[1]) ? $value[1] : null;
				$length = isset($value[2]) ? $value[2] : null;
				$driverOptions = isset($value[3]) ? $value[3] : null;
				$this->bindParam($key, $parameters[$key][0], $dataType, $length, $driverOptions);
			} else {
				$this->bindParam($key, $parameters[$key], $this->_getPdoDataType($value));
			}
		}
		return $this;
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
			throw new WindDbException($e->getMessage());
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
	 * 将列值绑定到php变量
	 * @param $column
	 * @param $param
	 * @param $type
	 * @param $maxlen
	 * @param $driverdata
	 * @throws WindDbException
	 */
	public function bindColumn($column, &$param = '', $type = null, $maxlen = null, $driverdata = null) {
		try {
			Wind::log("component.db.WindSqlStatement.bindColumn.", WindLogger::LEVEL_INFO, "component.db");
			$this->init();
			if ($type == null) $type = $this->_getPdoDataType($param);
			if ($type == null)
				$this->getStatement()->bindColumn($column, $param);
			elseif ($maxlen == null)
				$this->getStatement()->bindColumn($column, $param, $type);
			else
				$this->getStatement()->bindColumn($column, $param, $type, $maxlen, $driverdata);
			$this->_columns[$column] = & $param;
			return $this;
		} catch (PDOException $e) {
			Wind::log("component.db.WindSqlStatement.bindColumn. exception message" . $e->getMessage(), WindLogger::LEVEL_TRACE, "component.db");
			throw new WindDbException($e->getMessage());
		}
	}

	public function bindColumns($columns, &$param = array()) {
		$int = 0;
		foreach ($columns as $value) {
			$this->bindColumn($value, $param[$int++]);
		}
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $columns
	 */
	public function setColumns($columns) {}

	/**
	 * 执行SQL语句，并返回更新影响行数
	 * @param array $params
	 * @param boolean $rowCount 是否返回影响行数
	 * @throws WindDbException
	 */
	public function update($params = array(), $rowCount = false) {
		return $this->execute($params, $rowCount);
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
	 * 执行SQL语句，并返回查询结果
	 * @param array $params
	 * @param string $index 索引
	 * @param int $fetchMode
	 * @param int $fetchType
	 * @return array
	 */
	public function queryAll($params = array(), $index = '', $fetchMode = 0, $fetchType = 0) {
		$this->execute($params, false);
		$rs = new WindResultSet($this, $fetchMode, $fetchType);
		if (!$index) return $rs->fetchAll();
		$result = array();
		while ($one = $rs->fetch()) {
			isset($one[$index]) ? $result[$one[$index]] = $one : $result[] = $one;
		}
		return $result;
	}

	/**
	 * 执行SQL语句，并返回查询结果
	 * @param array $params
	 * @param int $fetchMode
	 * @param int $fetchType
	 * @return string
	 */
	public function getValue($params = array(), $column = 0) {
		$this->execute($params, false);
		$rs = new WindResultSet($this, PDO::FETCH_NUM, 0);
		return $rs->fetchColumn($column);
	}

	/**
	 * 执行SQL语句，并返回查询结果
	 * @param array $params
	 * @param int $fetchMode
	 * @param int $fetchType
	 * @return array
	 */
	public function getOne($params = array(), $fetchMode = 0, $fetchType = 0) {
		$this->execute($params, false);
		$rs = new WindResultSet($this, $fetchMode, $fetchType);
		return $rs->fetch();
	}

	/**
	 * 返回最后一条插入数据ID
	 * @param $name
	 */
	public function lastInsterId($name = '') {
		if ($name)
			return $this->getDbHandle()->lastInsertId($name);
		else
			return $this->getDbHandle()->lastInsertId();
	}

	/**
	 * 执行sql，$params为变量信息,并返回结果集
	 * @param array $params  -- 注意：绑定的变量数组下标将从0开始索引，
	 * @param boolean $rowCount
	 * @return rowCount
	 */
	public function execute($params = array(), $rowCount = true) {
		try {
			$this->init();
			Wind::log("component.db.WindSqlStatement.execute.", WindLogger::LEVEL_INFO, "component.db");
			$this->bindValues($params);
			$this->getStatement()->execute();
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
	 * @return the $_columns
	 */
	public function getColumns() {
		return $this->_columns;
	}

	/**
	 * @return
	 */
	public function init() {
		if ($this->_statement === null) {
			try {
				Wind::log("component.db.WindSqlStatement._init Initialize DBStatement. ", WindLogger::LEVEL_INFO, "component.db");
				Wind::profileBegin("component.db.WindSqlStatement._init", " SQL: " . $this->getQueryString(), "component.db");
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