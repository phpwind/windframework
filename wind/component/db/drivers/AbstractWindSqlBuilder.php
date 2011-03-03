<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.exception.WindSqlException');
L::import('WIND:component.db.drivers.IWindDbConfig');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class AbstractWindSqlBuilder {
	/**
	 * @var array 分组条件
	 */
	protected static $group = array(self::LG => '(', self::RG => ')');
	
	/**
	 * @var array 连接类型
	 */
	protected static $joinType = array(self::INNER => 'INNER', self::LEFT => 'LEFT', 
		self::RIGHT => 'RIGHT', self::FULL => 'FULL', self::CROSS => 'CROSS');
	
	const LG = '(';
	const RG = ')';
	
	const DISTINCT = 'distinct';
	const FIELD = 'field';
	const SET = 'set';
	const FROM = 'from';
	const JOIN = 'join';
	const WHERE = 'where';
	const GROUP = 'group';
	const HAVING = 'having';
	const ORDER = 'order';
	const LIMIT = 'limit';
	const OFFSET = 'offset';
	const INNER = 'inner';
	const LEFT = 'left';
	const RIGHT = 'right';
	const FULL = 'full';
	const CROSS = 'cross';
	const DATA = 'data';
	/**
	 * @var array sql语句组装器
	 */
	protected $sql = array();
	/**
	 * @var AbstractWindDbAdapter db操作
	 */
	public $connection = null;
	
	/**
	 * @param string $adapter
	 */
	public function __construct($adapter = null) {
		if ($adapter) {
			if (false === ($adapter instanceof AbstractWindDbAdapter) || strtr(get_class($this), array('Builder' => '')) != get_class($adapter)) {
				throw new WindSqlException('', WindSqlException::DB_DRIVER_BUILDER_NOT_MATCH);
			}
			$this->connection = $adapter;
		}
	}
	/**
	 * 要获取查询的表
	 * @param string $table  表名
	 * @param string $table_alias  表别名
	 * @param string|array $fields 表字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public function from($table, $table_alias = '', $fields = '', $schema = '') {
		$fields && $this->assembleFieldByTable($fields, $table, $table_alias);
		return $this->assembleSql(array($table => array($table_alias, $schema)), self::FROM);
	}
	/**
	 * 是否包含重复的值
	 * @param boolean $flag
	 * @return AbstractWindSqlBuilder
	 */
	public function distinct($flag = true) {
		$this->sql[self::DISTINCT] = $flag ? 'DISTINCT ' : '';
		return $this;
	}
	/**
	 * 要查询的字段
	 * @param mixed $field
	 * @return AbstractWindSqlBuilder
	 */
	public function field($field) {
		$params = func_num_args();
		$field = $params > 1 ? func_get_args() : func_get_arg(0);
		return $this->assembleSql($field, self::FIELD);
	}
	/**
	 * 联表查询（内联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询内联表的字段
	 * @param string $schema 数据库
	 * @return AbstractWindSqlBuilder
	 */
	public function join($table, $joinWhere, $alias = '', $fields = '', $schema = '') {
		return $this->assembleJoin(self::INNER, $table, $joinWhere, $alias, $fields, $schema);
	}
	/**
	 * 联表查询（内联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询内联表的字段
	 * @param string $schema 数据库
	 * @see wind/component/db/drivers/WindSqlBuilder#join()
	 * @return AbstractWindSqlBuilder
	 */
	public function innerJoin($table, $joinWhere, $alias = '', $fields = '', $schema = '') {
		return $this->assembleJoin(self::INNER, $table, $joinWhere, $alias, $fields, $schema);
	}
	/**
	 * 联表查询（左联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询左联表的字段
	 * @param string $schema 数据库
	 * @return AbstractWindSqlBuilder
	 */
	public function leftJoin($table, $joinWhere, $alias = '', $fields = '', $schema = '') {
		return $this->assembleJoin(self::LEFT, $table, $joinWhere, $alias, $fields, $schema);
	}
	/**
	 * 联表查询（右联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询右联表的字段
	 * @param string $schema 数据库
	 * @return AbstractWindSqlBuilder
	 */
	public function rightJoin($table, $joinWhere, $alias = '', $fields = '', $schema = '') {
		return $this->assembleJoin(self::RIGHT, $table, $joinWhere, $alias, $fields, $schema);
	}
	/**
	 * 联表查询（全联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询全联表的字段
	 * @param string $schema 数据库
	 * @return AbstractWindSqlBuilder
	 */
	public function fullJoin($table, $joinWhere, $alias = '', $fields = '', $schema = '') {
		return $this->assembleJoin(self::FULL, $table, $joinWhere, $alias, $fields, $schema);
	}
	/**
	 * 联表查询（交叉联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询交叉联表的字段
	 * @param string $schema 数据库
	 * @return AbstractWindSqlBuilder
	 */
	public function crossJoin($table, $joinWhere, $alias = '', $fields = '', $schema = '') {
		return $this->assembleJoin(self::CROSS, $table, $joinWhere, $alias, $fields, $schema);
	}
	/**
	 * 与查询条件，支持占位符
	 * @param string|array $where 查询条件
	 * @param string|array $value 条件对应的值
	 * @param boolean $group  是否启用分组
	 * @return AbstractWindSqlBuilder
	 */
	public function where($where, $value = array(), $group = '') {
		return $this->assembleWhere($where, self::WHERE, $value, true, $group);
	}
	/**
	 * 或查询条件，支持占位符
	 * @param string|array $where 查询条件
	 * @param string|array $value 条件对应的值
	 * @param boolean $group 是否启用分组
	 * @return AbstractWindSqlBuilder
	 */
	public function orWhere($where, $value = array(), $group = '') {
		return $this->assembleWhere($where, self::WHERE, $value, false, $group);
	}
	/**
	 * 查询分组
	 * @param string|array $group 要分组的字段名
	 * @return AbstractWindSqlBuilder
	 */
	public function group($group) {
		$params = func_num_args();
		$group = $params > 1 ? func_get_args() : func_get_arg(0);
		return $this->assembleSql($group, self::GROUP);
	}
	/**
	 * 过滤分组
	 * @param string|array $having 过滤条件
	 * @param string|array $value  条件对应的值
	 * @param boolean $group  是否启用分组
	 * @return AbstractWindSqlBuilder
	 */
	public function having($having, $value = array(), $group = '') {
		return $this->assembleWhere($having, self::HAVING, $value, true, $group);
	}
	/**
	 * 过滤分组
	 * @param string|array $having 过滤条件
	 * @param string|array $value  条件对应的值
	 * @param boolean $group  是否启用分组
	 * @return AbstractWindSqlBuilder
	 */
	public function orHaving($having, $value = array(), $group = '') {
		return $this->assembleWhere($having, self::HAVING, $value, false, $group);
	}
	/**
	 * 对查询结果排序
	 * @param string|array $field 排序的字段
	 * @param boolean $type 升序还是倒序
	 * @return boolean
	 */
	public function order($field, $type = true) {
		$field = is_array($field) ? $field : array($field => $type);
		return $this->assembleSql($field, self::ORDER);
	}
	/**
	 * 分页查询
	 * @param int $limit  偏移量
	 * @param int $offset 起始值 
	 * @return AbstractWindSqlBuilder
	 */
	public function limit($limit, $offset = '') {
		$this->assembleSql((int) $limit, self::LIMIT);
		return $this->assembleSql((int) $offset, self::OFFSET);
	}
	
	/**
	 * 解析insert值
	 * @param string $data
	 * @return AbstractWindSqlBuilder
	 */
	public function data($data) {
		$params = func_num_args();
		$data = $params > 1 ? func_get_args() : func_get_arg(0);
		list($data, $field) = $this->parseData($data);
		$field && $this->field($field);
		return $this->assembleSql($data, self::DATA);
	}
	/**
	 * 解析update值
	 * @param string|array $field
	 * @param string|array $value
	 * @return AbstractWindSqlBuilder
	 */
	public function set($field, $value = array()) {
		$realSet = $this->parsePlaceHolder($field, $value, ',');
		return $this->assembleSql($realSet, self::SET);
	}
	
	/**
	 * 表解析
	 * @return string;
	 */
	protected function buildFrom() {
		if (!isset($this->sql[self::FROM]) || empty($this->sql[self::FROM]) || !is_array($this->sql[self::FROM])) {
			throw new WindSqlException('', WindSqlException::DB_TABLE_EMPTY);
		}
		$tableList = '';
		foreach ($this->sql[self::FROM] as $key => $value) {
			$tableList .= $tableList ? ',' . $this->getAlias($key, $value[0], $value[1]) : $this->getAlias($key, $value[0], $value[1]);
		}
		return $this->sqlFillSpace($tableList);
	}
	/**
	 * 解析是否有重复的值
	 * @return string
	 */
	protected function buildDistinct() {
		return isset($this->sql[self::DISTINCT]) ? $this->sqlFillSpace($this->sql[self::DISTINCT]) : '';
	}
	/**
	 * 解析查询字段
	 * @return string
	 */
	protected function buildField() {
		if (!isset($this->sql[self::FIELD]) || empty($this->sql[self::FIELD]) || !is_array($this->sql[self::FIELD])) {
			throw new WindSqlException('', WindSqlException::DB_QUERY_FIELD_FORMAT);
		}
		$fieldList = '';
		foreach ($this->sql[self::FIELD] as $key => $value) {
			if (is_int($key)) {
				$fieldList .= $fieldList ? ',' . $value : $value;
			}
			if (is_string($key)) {
				$fieldList .= $fieldList ? ',' . $this->getAlias($key, $value) : $this->getAlias($key, $value);
			}
		}
		return $this->sqlFillSpace($fieldList);
	}
	/**
	 * 解析连接查询
	 * @return string
	 */
	protected function buildJoin() {
		if (!isset($this->sql[self::JOIN]) || empty($this->sql[self::JOIN]) || !is_array($this->sql[self::JOIN])) {
			return '';
		}
		$joinContidion = '';
		foreach ($this->sql[self::JOIN] as $table => $config) {
			if (is_string($config) && is_int($table)) {
				throw new WindSqlException('', WindSqlException::DB_QUERY_JOIN_FORMAT);
			}
			if (is_array($config) && is_string($table)) {
				$table = $this->getAlias($table, $config[2], $config[3]);
				$joinWhere = $config[1] ? 'ON ' . $config[1] : '';
				$condition = self::$joinType[$config[0]] . ' JOIN ' . $table . $joinWhere;
				$joinContidion .= $joinContidion ? ' ' . $condition : $condition;
			}
		}
		return $this->sqlFillSpace($joinContidion);
	}
	/**
	 * 解析查询条件
	 * @return string
	 */
	protected function buildWhere() {
		if (!isset($this->sql[self::WHERE])) {
			return '';
		}
		$where = is_array($this->sql[self::WHERE]) ? implode(' ', $this->sql[self::WHERE]) : $this->sql[self::WHERE];
		return $where ? $this->sqlFillSpace('WHERE ' . $where) : '';
	}
	/**
	 * 解析分组
	 * @return string
	 */
	protected function buildGroup() {
		if (!isset($this->sql[self::GROUP])) {
			return '';
		}
		$group = is_array($this->sql[self::GROUP]) ? implode(',', $this->sql[self::GROUP]) : $this->sql[self::GROUP];
		return $group ? $this->sqlFillSpace('GROUP BY ' . $group) : '';
	}
	/**
	 * 解析排序
	 * @return string
	 */
	protected function buildOrder() {
		if (!isset($this->sql[self::ORDER])) {
			return '';
		}
		$orderby = '';
		if (is_array($this->sql[self::ORDER])) {
			foreach ($this->sql[self::ORDER] as $key => $value) {
				$orderby .= ($orderby ? ',' : '') . (is_string($key) ? $key . ' ' . ($value ? 'DESC ' : 'ASC ') : $value);
			}
		} else {
			$orderby = $this->sql[self::ORDER];
		}
		return $orderby ? $this->sqlFillSpace('ORDER BY ' . $orderby) : '';
	}
	/**
	 * 解析对分组的过滤语句
	 * @return string
	 */
	protected function buildHaving() {
		if (!isset($this->sql[self::HAVING])) {
			return '';
		}
		$having = is_array($this->sql[self::HAVING]) ? implode(' ', $this->sql[self::HAVING]) : $this->sql[self::HAVING];
		return $having ? $this->sqlFillSpace('HAVING ' . $having) : '';
	}
	/**
	 * 解析分页查询
	 * @return string
	 */
	protected function buildLimit() {
		if (!isset($this->sql[self::LIMIT]) || empty($this->sql[self::LIMIT])) {
			return '';
		}
		if (is_string($this->sql[self::LIMIT])) {
			return $this->sqlFillSpace($this->sql[self::LIMIT]);
		}
		if (is_array($this->sql[self::LIMIT])) {
			$this->sql[self::LIMIT] = array_pop($this->sql[self::LIMIT]);
		}
		if (isset($this->sql[self::OFFSET]) && is_array($this->sql[self::OFFSET])) {
			$this->sql[self::OFFSET] = array_pop($this->sql[self::OFFSET]);
		}
		return $this->sqlFillSpace(($sql = isset($this->sql[self::LIMIT])  ? 'LIMIT ' . (int)$this->sql[self::LIMIT] . ' ' : '') ? isset($this->sql[self::OFFSET])  ? $sql . 'OFFSET ' . (int)$this->sql[self::OFFSET] : $sql : '');
	}
	/**
	 * 解析更新数据
	 * @return string
	 */
	protected function buildSet() {
		if (!isset($this->sql[self::SET]) || empty($this->sql[self::SET])) {
			throw new WindSqlException('', WindSqlException::DB_QUERY_UPDATE_DATA);
		}
		if (is_string($this->sql[self::SET])) {
			return $this->sql[self::SET];
		}
		foreach ($this->sql[self::SET] as $key => $value) {
			if (is_string($key)) {
				$this->sql[self::SET][$key] = $key . '=' . $this->escapeString($value);
			} else {
				$this->sql[self::SET][$key] = $value;
			}
		}
		return $this->sqlFillSpace(implode(',', $this->sql[self::SET]));
	}
	/**
	 * 解析添加数据
	 * @return string
	 */
	protected function buildData() {
		if (!isset($this->sql[self::DATA]) || empty($this->sql[self::DATA]) || !is_array($this->sql[self::DATA])) {
			throw new WindSqlException('', WindSqlException::DB_QUERY_INSERT_DATA);
		}
		if ($this->getDimension($this->sql[self::DATA]) == 1) {
			return $this->buildSingleData($this->sql[self::DATA]);
		}
		if ($this->getDimension($this->sql[self::DATA]) >= 2) {
			return $this->buildMultiData($this->sql[self::DATA]);
		}
		return array();
	}
	
	/**
	 *返回影响行数的sql语句
	 *@param $ifquery 是否是select 语句
	 *@return string 
	 */
	public abstract function getAffectedSql($ifquery = true);
	
	/**
	 *返回取得最后新增的sql语句
	 *@return string 
	 */
	public abstract function getLastInsertIdSql();
	/**
	 * 返回指定数据库下元数据表
	 * @param strint $schema 数据库名
	 * @return array
	 */
	public abstract function getMetaTableSql($schema);
	
	/**
	 * 返回指定数据表下元数据列
	 * @param string $table  表名
	 * @return array
	 */
	public abstract function getMetaColumnSql($table);
	/**
	 * 解析新增SQL语句
	 * @param array $sql
	 * @return string
	 */
	public abstract function getInsertSql();
	/**
	 * 解析更新QL语句
	 * @param array $sql
	 * @return string
	 */
	public abstract function getUpdateSql();
	/**
	 * 解析删除SQL语句
	 * @param array $sql
	 * @return string
	 */
	public abstract function getDeleteSql();
	/**
	 * 解析查询SQL语句
	 * @param array $sql
	 * @return string
	 */
	public abstract function getSelectSql();
	
	/**
	 * 解析replace SQL语句
	 * @param array $sql
	 * @return string
	 */
	public abstract function getReplaceSql();
	
	/**
	 * 执行数据库delete操作
	 * @return boolean
	 */
	public function delete() {
		$this->verifyAdapter();
		return $this->connection->delete($this->getDeleteSql());
	}
	
	/**
	 * 执行数据库update操作
	 * @return boolean
	 */
	public function update() {
		$this->verifyAdapter();
		return $this->connection->update($this->getUpdateSql());
	}
	
	/**
	 * 执行数据库select操作
	 * @return AbstractWindSqlBuilder
	 */
	public function select() {
		$this->verifyAdapter();
		$this->connection->select($this->getSelectSql());
		return $this;
	}
	
	/**
	 * 执行数据库insert操作
	 * @return boolean
	 */
	public function insert() {
		$this->verifyAdapter();
		return $this->connection->insert($this->getInsertSql());
	}
	
	/**
	 * 执行数据库replace操作
	 * @return boolean
	 */
	public function replace() {
		$this->verifyAdapter();
		return $this->connection->replace($this->getReplaceSql());
	}
	
	/**
	 * 取得结果集
	 * @param int $fetch_type 类型
	 * @return array
	 */
	public function getAllRow($fetch_type = IWindDbConfig::ASSOC) {
		$this->verifyAdapter();
		return $this->connection->getAllRow($fetch_type);
	}
	
	/**
	 * 取得一条结果集
	 * @param int $fetch_type 类型
	 * @return array
	 */
	public function getRow($fetch_type = IWindDbConfig::ASSOC) {
		$this->verifyAdapter();
		return $this->connection->getRow($fetch_type);
	}
	
	public function getAffectedRows(){
		$this->verifyAdapter();
		return $this->connection->getAffectedRows();
	}
	
	public function verifyAdapter() {
		if ($this->connection instanceof AbstractWindDbAdapter) {
			return true;
		}
		throw new WindSqlException('', WindSqlException::DB_ADAPTER_NOT_EXIST);
	}
	
	/**
	 * 取得别名标识
	 * @param string $name 源名称
	 * @param string $alias 别名
	 * @param string $schema 数据库名称
	 * @return string
	 */
	public function getAlias($name, $alias = '', $schema = '') {
		$name = $alias ? $name . ' AS ' . $alias : $name;
		return $this->sqlFillSpace($schema ? $schema . '.' . $name : $name);
	}
	
	/**
	 * 对字符串转义
	 * @param string $value
	 * @return string
	 */
	public function escapeString(&$value, $key = '') {
		if (is_int($value)) {
			$value = (int) $value;
		} elseif (is_string($value)) {
			$value = " '" . $value . "' ";
		} elseif (is_float($value)) {
			$value = (float) $value;
		} elseif (is_object($value)) {
			$value = addslashes(serialize($value));
		}
		return $this->sqlFillSpace($value);
	}
	
	/**
	 * 判断是否是二维数组
	 * @param array $array
	 * @return number
	 */
	public function getDimension($array = array()) {
		$dim = 0;
		foreach ($array as $value) {
			return is_array($value) ? $dim += 2 : ++$dim;
		}
		return $dim;
	}
	
	/**
	 * 要解析的一维数组，单条添加数据
	 * @param array $data 要解析的数据
	 * @return string
	 */
	public function buildSingleData($data) {
		foreach ($data as $key => $value) {
			$data[$key] = $this->escapeString($value);
		}
		return $this->sqlFillSpace('(' . implode(',', $data) . ')');
	}
	
	/**
	 * 解析二维数组，批量添加
	 * @param array $multiData 要解析的数据
	 * @return string
	 */
	public function buildMultiData($multiData) {
		$iValue = '';
		foreach ($multiData as $data) {
			$iValue .= $iValue ? ',' . $this->buildSingleData($data) : $this->buildSingleData($data);
		}
		return $iValue;
	}
	
	/**
	 * 在字符串头尾添加空格或空白字符
	 * @param string $value  字符串
	 * @return string
	 */
	public function sqlFillSpace($value) {
		return str_pad($value, strlen($value) + 2, " ", STR_PAD_BOTH);
	}
	
	/**
	 * 重置sql语句组装条件
	 * @param unknown_type $type
	 */
	public function reset($type = '') {
		if ($type) {
			$this->sql[$type] =  NULL;
			unset($this->sql[$type]);
		} else {
			$this->sql = array();
		}
	}
	
	public function getSql($type = '') {
		return ($type && isset($this->sql[$type])) ? $this->sql[$type] : $this->sql;
	}
	
	private function parseData($data = array()) {
		if (empty($data) || !is_array($data)) {
			throw new WindSqlException('', WindSqlException::DB_QUERY_FIELD_EMPTY);
		}
		$key = array_keys($data);
		if (!is_string($key[0])) {
			return array($data, array());
		}
		$tmp_data = $field = array();
		$rows = count($data[$key[0]]);
		for ($i = 0; $i < $rows; $i++) {
			foreach ($data as $key => $value) {
				$fvalues = array_values($field);
				if (!in_array($key, $fvalues)) {
					$field[] = $key;
				}
				if (is_array($value)) {
					$tmp_data[$i][] = $value[$i];
					unset($data[$key][$i]);
				} else {
					$tmp_data[] = $value;
				}
			}
		}
		$data = $tmp_data ? $tmp_data : $data;
		return array($data, $field);
	}
	
	/**
	 * 返回真实的where条件
	 * @param mixed $where 包含占位符的where条件
	 * @param mixed $value where条件中占位符对应的值
	 * @param boolean $logic 逻辑条件
	 * @return mixed
	 */
	private function trueWhere($where, $value = array(), $logic = true) {
		return $this->parsePlaceHolder($where, $value, $this->sqlFillSpace($logic ? 'AND ' : 'OR '));
	}
	
	/**
	 * 组装sql语句
	 * @param mixed $assembleValue 组装条件
	 * @param mixed $assembleType  组装类型
	 * @return WindMySqlBuilder
	 */
	private function assembleSql($assembleValue, $assembleType) {
		if (empty($assembleValue)) {
			return $this;
		}
		if (!isset($this->sql[$assembleType]) || empty($this->sql[$assembleType]) || !is_array($this->sql[$assembleType])) {
			$this->sql[$assembleType] = array();
		}
		$assembleValue = is_array($assembleValue) ? $assembleValue : array($assembleValue);
		foreach ($assembleValue as $key => $value) {
			if (is_string($key)) {
				$this->sql[$assembleType][$key] = $value;
			}
			if (is_int($key)) {
				$this->sql[$assembleType][] = $value;
			}
		}
		return $this;
	}
	
	/**
	 * 组装where语句
	 * @param mixed $where 条件
	 * @param mixed $whereType 类型（where or having）
	 * @param mixed $value  值
	 * @param mixed $logic  是否是逻辑条件
	 * @param mixed $group  是否提供分组
	 * @return WindMySqlBuilder
	 */
	private function assembleWhere($where, $whereType = self::WHERE, $value = array(), $logic = true, $group = '') {
		$_where = '';
		if (!in_array($whereType, array(self::WHERE, self::HAVING))) {
			throw new WindSqlException($whereType, WindSqlException::DB_QUERY_WHERE_ERROR);
		}
		$where = $this->trueWhere($where, $value, $logic);
		if ($group && in_array($group, self::$group)) {
			$_where = self::$group[$group];
		}
		if (isset($this->sql[$whereType]) && $this->sql[$whereType]) {
			if ($logic) {
				$_where .= 'AND ' . $where;
			} else {
				$_where .= 'OR ' . $where;
			}
		} else {
			$_where[] = $where;
		}
		return $this->assembleSql($_where, $whereType);
	}
	
	/**
	 * 组装要对指定表进行操作的字段
	 * @param mixed $fields 表字段
	 * @param mixed $table 表名
	 * @param mixed $table_alias 表别名
	 * @return WindMySqlBuilder
	 */
	private function assembleFieldByTable($fields, $table, $table_alias = '') {
		if ($fields && (is_string($fields) || is_array($fields))) {
			$fields = is_array($fields) ? $fields : explode(',', $fields);
			foreach ($fields as $key => $field) {
				$fields[$key] = (false === ($pos = strpos('.', $field))) ? $table_alias ? $table_alias . '.' . $field : $table . '.' . $field : $field;
			}
			$this->assembleSql($fields, self::FIELD);
		}
		return $this;
	}
	
	/**
	 * 组装联接sql语句
	 * @param mixed $type 联接类型
	 * @param mixed $table 表名
	 * @param mixed $joinWhere 联接接条件
	 * @param mixed $table_alias 表别名
	 * @param mixed $fields 字段
	 * @param mixed $schema 数据库
	 * @return WindMySqlBuilder
	 */
	private function assembleJoin($type, $table, $joinWhere, $table_alias = '', $fields = '', $schema = '') {
		if (!in_array($type, array_keys(self::$joinType))) {
			throw new WindSqlException($type, WindSqlException::DB_QUERY_JOIN_TYPE_ERROR);
		}
		$fields && $this->assembleFieldByTable($fields, $table, $table_alias);
		return $this->assembleSql(array($table => array($type, $joinWhere, $table_alias, $schema)), self::JOIN);
	}
	
	/**
	 * 解析占位符
	 * @param mixed $text 包含占位符的文本
	 * @param mixed $replace 将占位符替换成指定的值
	 * @param mixed $separators 分隔符
	 * @return mixed 返回解析后的文本
	 * @todo 重构占位符
	 */
	private function parsePlaceHolder($text, $replace = array(), $separators = ',') {
		if ($text && is_array($text)) {
			return $this->parseArrayPlaceHolder($text, $replace, $separators);
		}
		if ($text && is_string($text)) {
			list($ifmatch, $text) = $this->parseUnFixedPlaceHolder($text, $replace);
			if ($ifmatch) {
				return $text;
			} else {
				list(, $text) = $this->parseFixedPlaceHolder($text, $replace);
			}
			return $text;
		}
		return $text;
	}
	/**
	 * 按数组方式解析占位符
	 * @param mixed $text 包含占位符的文本
	 * @param mixed $replace 将占位符替换成指定的值
	 * @param mixed $separators 分隔符
	 * @return mixed 返回解析后的文本
	 */
	private function parseArrayPlaceHolder($text, $replace = array(), $separators = ',') {
		if (!is_array($text)) {
			return $text;
		}
		foreach ($text as $key => $_where) {
			if (is_int($key)) {
				$text[$key] = strpos($_where, '?') ? str_replace('?', $this->escapeString($replace[$key]), $_where) : $_where;
			}
			if (is_string($key)) {
				$value = $key . '=' . $this->escapeString($_where);
				$text[] = $value;
				unset($text[$key]);
			}
		}
		return implode($separators ? $separators : ',', $text);
	}
	/**
	 * 按固定的方式解析占位符
	 * @param mixed $text 包含占位符的文本
	 * @param mixed $replace 将占位符替换成指定的值
	 * @return mixed 返回解析后的文本
	 */
	private function parseFixedPlaceHolder($text, $replace = array()) {
		if (0 < (int) ($ifmatch = preg_match_all('/([\w\d_\.`]+[\t ]*(>|<|!=|<>|>=|<=|=|like|in|not[\t ]+in)[\t ]*)(\?)/i', $text, $matches))) {
			$replace = is_array($replace) ? $replace : array($replace);
			foreach ($matches[1] as $key => $match) {
				if (in_array(strtoupper(trim($matches[2][$key])), array('IN', 'NOT IN'))) {
					$replace[$key] = is_array($replace[$key]) ? $replace[$key] : array($replace[$key]);
					array_walk($replace[$key], array($this, 'escapeString'));
					$_replace = $match . self::LG . implode(', ', $replace[$key]) . self::RG;
				} else {
					$_replace = $match . $this->escapeString($replace[$key]);
				}
				$text = strtr($text, array($matches[0][$key] => $_replace));
			}
		}
		return array($ifmatch, $text);
	}
	/**
	 * 按灵活的方式解析占位符
	 * @param mixed $text 包含占位符的文本
	 * @param mixed $replace 将占位符替换成指定的值
	 * @return mixed 返回解析后的文本
	 */
	private function parseUnFixedPlaceHolder($text, $replace = array()) {
		if (0 < (int) ($ifmatch = preg_match_all('/([\w\d_\.`]+[\t ]*(>|<|!=|<>|>=|<=|=|like|in|not[\t ]+in)[\t ]*)(:[\w\d_\.]+)/i', $text, $matches))) {
			if (!is_array($replace)) {
				$tmp = explode('=', $replace);
				$replace = array($tmp[0] => $tmp[1]);
			}
			foreach ((array)$matches[1] as $key => $match) {
				$_trueKey = $matches[3][$key];
				if (in_array(strtoupper(trim($matches[2][$key])), array('IN', 'NOT IN'))) {
					array_walk($replace[$_trueKey], array($this, 'escapeString'));
					$_replace = $match . self::LG . implode(', ', $replace[$_trueKey]) . self::RG;
				} else {
					$_replace = $match . $this->escapeString($replace[$_trueKey]);
				}
				$text = strtr($text, array($matches[0][$key] => $_replace));
			}
		}
		return array($ifmatch, $text);
	}
}