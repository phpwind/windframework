<?php
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindResultSet {
	/**
	 * @var PDOStatement
	 */
	private $_statement = null;
	/**
	 * PDO fetchMode, default fetchMode PDO::FETCH_ASSOC
	 * @var number
	 */
	private $_fetchMode = PDO::FETCH_ASSOC;
	/**
	 * PDO fetchType, default fetchType PDO::FETCH_ORI_FIRST
	 * @var number
	 */
	private $_fetchType = PDO::FETCH_ORI_FIRST;
	private $_columns = array();

	/**
	 * @param WindSqlStatement $sqlStatement
	 */
	public function __construct($sqlStatement, $fetchMode = 0, $fetchType = 0) {
		$this->_statement = $sqlStatement->getStatement();
		$this->_columns = $sqlStatement->getColumns();
		if ($fetchMode != 0) $this->_fetchMode = $fetchMode;
	}

	/**
	 * @param $fetchMode
	 * @return 
	 */
	public function setFetchMode($fetchMode) {
		$fetchMode = func_get_args();
		call_user_func_array(array($this->_statement, 'setFetchMode'), $fetchMode);
	}

	/**
	 * 返回最后一条Sql语句的影响行数
	 */
	public function rowCount() {
		return $this->_statement->rowCount();
	}

	/**
	 * 返回结果集中的列数
	 * @return number
	 */
	public function columnCount() {
		return $this->_statement->columnCount();
	}

	/**
	 * Fetches the next row from a result set 
	 * @param int $fetchType 
	 * @return array
	 */
	public function fetch($fetchMode = PDO::FETCH_BOTH) {
		return $this->_fetch($fetchMode, $this->_fetchType);
	}

	/**
	 * @param $fetchMode
	 * @param $fetchType
	 * @return array
	 */
	private function _fetch($fetchMode, $fetchType) {
		if (!empty($this->_columns)) $fetchMode = PDO::FETCH_BOUND;
		$result = array();
		if ($row = $this->_statement->fetch($fetchMode, $fetchType)) {
			if (empty($this->_columns)) return $row;
			foreach ($this->_columns as $key => $value) {
				$result[$key] = $value;
			}
			return $result;
		}
		return array();
	}

	/**
	 * Some database servers support stored procedures that return more than one rowset 
	 * (also known as a result set). PDOStatement::nextRowset() enables you to access the second 
	 * and subsequent rowsets associated with a PDOStatement object. Each rowset can have a 
	 * different set of columns from the preceding rowset. 
	 * 
	 * @return boolean | 成功返回true失败返回false
	 */
	public function nextRowset() {
		return $this->_statement->nextRowset();
	}

	/**
	 * 返回所有的查询结果
	 * @param int $fetchType 设置返回的方式
	 * @return array
	 */
	public function fetchAll($fetchType = PDO::FETCH_BOTH) {
		if (empty($this->_columns))
			return $this->_statement->fetchAll($fetchType);
		else {
			$result = array();
			while ($row = $this->fetch($fetchType)) {
				$result[] = $row;
			}
			return $result;
		}
	}

	/**
	 * 从下一行记录中获得下标使$index的值，如果获取失败则返回false
	 * 
	 * @param int $index
	 * @return string|bool
	 */
	public function fetchColumn($index = 0) {
		return $this->_statement->fetchColumn($index);
	}

	/**
	 * Fetches the next row and returns it as an object
	 * @param string $className
	 * @param array $ctor_args
	 * @return array
	 */
	public function fetchObject($className = '', $ctor_args = array()) {
		if ($className === '')
			return $this->_statement->fetchObject();
		else
			return $this->_statement->fetchObject($className, $ctor_args);
	}
}
?>