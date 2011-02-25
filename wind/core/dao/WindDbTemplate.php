<?php

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindDbTemplate implements IWindDbTemplate {

	private $connection = null;

	/**
	 * 获得数据库链接
	 * @return the $connection
	 */
	public function getConnection() {
		return $this->connection->getConnection();
	}

	/**
	 * 设置数据库链接
	 * @param object $connection
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
	}

	/**
	 * 执行一条sql语句
	 * @param string $sql	sql语句
	 * @param string $type	主从选项  支持两个参数：slave 和  master
	 * @param array $config	独立配置信息
	 * @return resource|bool  数据库句柄
	 */
	public function query($sql, $config = array()) {
		return $this->getConnection()->query($sql);
	}

	/**
	 * 更新数据
	 * @param string $table 更新的数据表
	 * @param array $data	更新的数据
	 * @param srting $condition	更新的条件
	 * array(
	 * 'where' => '',  查询的条件
	 * 'whereValue' => array(),  查询条件中的变量值
	 * 'order' => array(), 排序类型  默认是降序排列，支持多字段排序  array('id'=>true,'name'=>false)
	 * 'limit' => '' 查询的数量
	 * )
	 * @return bool
	 */
	public function update($table, $data, $condition = array()) {
		$condition = $this->cookCondition($condition);
		$db = $this->getConnection();
		$result = $db->getSqlBuilder()->from($table)
									  ->set($data)
									  ->where($condition['where'], $condition['whereValue'])
									  ->order($condition['order'])
									  ->limit($condition['limit'])
									  ->update();
		return $result;
	}

	/**
	 * 通过字段更新
	 * 
	 * @param string $table 更新的数据表
	 * @param array $data	更新的数据
	 * @param string $filed	条件字段
	 * @param string $value	该字段的值
	 * @return bool 
	 */
	public function updateByField($table, $data, $filed, $value) {
		if (!$this->checkFiled($filed)) return false;
		return $this->update($table, $data, array('where' => "$filed = ?", 'whereValue' => $value));
	}

	/**
	 * 插入一条数据
	 * 
	 * @param string $table 更新的数据表
	 * @param array $data	插入的数据
	 * @param array $field	相关的字段（可选）
	 * @return bool
	 */
	public function insert($table, $data, $field = array()) {
		empty($field) && list($field, $data) = $this->parseData($data);
		$db = $this->getConnection();
		$db->getSqlBuilder()->from($table)->field($field)->data($data)->insert();
		return $db->getLastInsertId();
	}

	/**
	 * 更新一条数据
	 * @param string $table 更新的数据表
	 * @param array $data	更新的数据
	 * @param array $field	相关的字段（可选）
	 * @return bool
	 */
	public function replace($table, $data, $field = array()) {
		empty($field) && list($field, $data) = $this->parseData($data);
		$db = $this->getConnection();
		$db->getSqlBuilder()->from($table)->field($field)->data($data)->replace();
		return $db->getAffectedRows();
	}

	/**
	 * 删除数据
	 * 
	 * @param string $table 更新的数据表
	 * @param srting $condition	更新的条件
	 * array(
	 * 'where' => '',  查询的条件
	 * 'whereValue' => array(),  查询条件中的变量值
	 * 'order' => array(), 排序类型  默认是降序排列，支持多字段排序  array('id'=>true,'name'=>false)
	 * 'limit' => '' 查询的数量
	 * )
	 * @return bool
	 */
	public function delete($table, $condition) {
		$condition = $this->cookCondition($condition);
		$result = $this->getConnection()->getSqlBuilder()->from($table)
										->where($condition['where'], $condition['whereValue'])
										->order($condition['order'])
										->limit($condition['limit'])
										->delete();
		return $result;
	}

	/**
	 * 通过某个字段删除数据
	 * 
	 * @param string $table 删除的数据表
	 * @param string $filed	所依据的字段名
	 * @param string $value 该字段的值
	 * @return bool
	 */
	public function deleteByField($table, $filed, $value) {
		if (!$this->checkFiled($filed)) return array();
		return $this->delete($table, array('where' => "$filed = ?", 'whereValue' => $value));
	}

	/**
	 * 单条查询
	 * 
	 * @param array $condition
	 * array(
	 * 'field' => '*' 查询的字段
	 * 'where' => '' | array(),  查询的条件
	 * 'whereValue' => array(),  查询条件中的变量值
	 * 'group' => array(),  group关键字的列名
	 * 'having' => '', having关键字的列名
	 * 'havingValue' => array(),  having条件中的变量值
	 * 'order' => array(), 排序类型  默认是降序排列，支持多字段排序  array('id'=>true,'name'=>false)
	 * )
	 * @param array $config	独立配置信息
	 * @return array
	 */
	public function find($table, $condition = array(), $config = array()) {
		$condition = $this->cookCondition($condition);
		$db = $this->getConnection();
		$query = $db->getSqlBuilder()->from($table)
		                             ->field($condition['field'])
		                             ->where($condition['where'], $condition['whereValue'])
		                             ->group($condition['group'])
		                             ->having($condition['having'], $condition['havingValue'])
		                             ->order($condition['order'])
		                             ->limit(1)
		                             ->select();
		return $db->getRow($query);
	}

	/**
	 * 通过某一字段查询
	 * @param string $table 查询的数据表
	 * @param string $filed	字段名
	 * @param string $value	该字段的值
	 * @param array $config	独立配置信息
	 * @return array
	 */
	public function findByField($table, $filed, $value, $config = array()) {
		if (!$this->checkFiled($filed)) return array();
		return $this->find($table, array('where' => "$filed = ?", 'whereValue' => $value), $config);
	}

	/**
	 * 查询多条
	 * @param array $condition
	 * array(
	 * 'field' => '*' 查询的字段
	 * 'where' => '',  查询的条件
	 * 'whereValue' => array(),  查询条件中的变量值
	 * 'group' => array(),  group关键字的列名
	 * 'having' => '', having关键字的列名
	 * 'havingValue' => array(),  having条件中的变量值
	 * 'order' => array(), 排序类型  默认是降序排列，支持多字段排序  array('id'=>true,'name'=>false)
	 * 'limit' => '' 查询的数量
	 * 'offset'=> '' 和limit配合使用
	 * )
	 * @param bool $ifCount	是否需要统计总个数
	 * @param array $config	数据库独立配置信息
	 * @return array() | array($result,$count)
	 */
	public function findAll($table, $condition = array(), $ifCount = false, $config = array()) {
		$condition = $this->cookCondition($condition);
		$db = $this->getConnection();
		$query = $db->getSqlBuilder()->from($table)
									 ->field($condition['field'])
									 ->where($condition['where'], $condition['whereValue'])
									 ->group($condition['group'])
									 ->having($condition['having'], $condition['havingValue'])
									 ->order($condition['order'])
									 ->limit($condition['limit'], $condition['offset'])
									 ->select();
		$result = $db->getAllRow();
		if (!$ifCount) return $result;
		$count = $this->count($table, $condition, $config);
		return array($result, $count);
	}

	/**
	 * 统计个数
	 * @param array $condition
	 * array(
	 * 'field' => '*' 查询的字段
	 * 'where' => '',  查询的条件
	 * 'whereValue' => array(),  查询条件中的变量值
	 * 'group' => array(),  group关键字的列名
	 * 'having' => '', having关键字的列名
	 * 'havingValue' => array(),  having条件中的变量值
	 * 'order' => array(), 排序类型  默认是降序排列，支持多字段排序  array('id'=>true,'name'=>false)
	 * )
	 * @param array $config	独立配置信息
	 * @return int
	 */
	public function count($table, $condition, $config = array()) {
		$condition = $this->cookCondition($condition);
		$condition['field'] = ' COUNT(*) as total';
		$result = $this->find($table, $condition, $config);
		return (int)$result['total'];
	}

	/**
	 * 初始化条件数据
	 * 
	 * @param array $condition	条件
	 * @return array
	 */
	private function cookCondition($condition) {
		$defaultValue = array('field' => '*', 'where' => '', 'whereValue' => array(), 
		                      'group' => array(),  'order' => array(), 'limit' => null, 
		                      'offset' => null, 'having' => '', 'havingValue' => array());
		return array_merge($defaultValue, (array)$condition);
	}

	/**
	 * 解析数据并返回字段名
	 * 
	 * @param array $data	待解析的数据
	 * @return array ($field,$data)
	 */
	private function parseData($data) {
		$key = array_keys($data);
		$tmp_data = $field = array();
		if (!is_string($key[0])) return array($field, $data);
		
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
		return array($field, $data);
	}

	/**
	 * 验证字段的合法性
	 * @param string $filed
	 * @return bool
	 */
	private function checkFiled($filed) {
		return preg_match('/^[A-Za-z]{1}[A-Za-z0-9_]+$/i', $filed);
	}
}

?>