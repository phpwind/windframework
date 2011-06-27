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

	/**
	 * @param WindSqlStatement $sqlStatement
	 */
	public function __construct($sqlStatement, $fetchMode = 0, $fetchType = 0) {
		$this->_statement = $sqlStatement->getStatement();
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
	 * 返回结果集的条数
	 * @return number
	 */
	public function columnCount() {
		return $this->_statement->columnCount();
	}

	/**
	 * Fetches the next row from a result set 
	 * @return array
	 */
	public function fetch() {
		return $this->_fetch($this->_fetchMode, $this->_fetchType);
	}

	/**
	 * @param $fetchMode
	 * @param $fetchType
	 * @return array
	 */
	private function _fetch($fetchMode, $fetchType) {
		if (!$result = $this->_statement->fetch($fetchMode, $fetchType))
			return array();
		else
			return $result;
	}

	/**
	 * @return boolean | 成功返回true失败返回false
	 */
	public function nextRowset() {
		return $this->_statement->nextRowset();
	}

	/**
	 * 返回所有的查询结果
	 * @return array
	 */
	public function fetchAll() {
		return $this->_statement->fetchAll();
	}

	/**
	 * @param int $index
	 * @return string
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