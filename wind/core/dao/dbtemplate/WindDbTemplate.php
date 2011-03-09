<?php
/**
 * @author xiaoxia xu <xiaoxa.xuxx@aliyun-inc.com> 2011-3-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.dao.IWindDbTemplate');
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <xiaoxia.xuxx@aliyun-inc.com>
 * @version $Id$ 2011-3-9
 * @package
 */
class WindDbTemplate implements IWindDbTemplate {

	/**
	 * 链接句柄
	 * 
	 * @var WindDbAdapter
	 */
	private $connection = null;
   
	/**
	 * 设置数据库链接句柄
	 * 
	 * (non-PHPdoc)
	 * @see IWindDbTemplate::setConnection()
	 * @param WindDbAdapter $connection
	 */
	public function setConnection($connection) {
		$this->connection = $connection;
	}
    
	/**
	 * 获得数据库链接句柄
	 * 
	 * (non-PHPdoc)
	 * @see IWindDbTemplate::getConnection()
	 * @return WindDbAdapter $connection
	 */
	public function getConnection() {
		return $this->connection;
	}
   
	/**
	 * 获得数据库链接操作句柄
	 * 
	 * @return WindDbAdapter $connection
	 */
	protected function getDbHandler() {
	    return $this->getConnection();
	}

	/**
	 * 执行一条sql语句
	 * @param string $sql	sql语句
	 * @return array|bool  数据库句柄
	 */
	public function query($sql) {
		return $this->getDbHandler()->query($sql);
	}

	/**
	 * 更新数据
	 * @param string $tableName 更新的数据表
	 * @param array $data	更新的数据
	 * @param srting $condition	更新的条件
	 * array(
	 * 'where' => '',  查询的条件
	 * 'whereValue' => array(),  查询条件中的变量值
	 * 'order' => array(), 排序类型  默认是降序排列，支持多字段排序  array('id'=>true,'name'=>false)
	 * 'limit' => '' 查询的数量
	 * )
	 * @param return $isGetAffectedRows 是否返回影响行数
	 * @return bool
	 */
	public function update($tableName, $data, $condition = array(), $isGetAffectedRows = false) {
		$condition = $this->cookCondition($condition);
		$db = $this->getDbHandler();
		$result = $db->getSqlBuilder()->from($tableName)->set($data)->where($condition['where'], $condition['whereValue'])->order($condition['order'])->limit($condition['limit'])->update();
		return $isGetAffectedRows ? $db->getAffectedRows() : $result;
	}

	/**
	 * 通过字段更新
	 * 
	 * @param string $tableName 更新的数据表
	 * @param array $data	更新的数据
	 * @param string $filed	条件字段
	 * @param string $value	该字段的值
	 * @param return $isGetAffectedRows 是否返回影响行数
	 * @return bool 
	 */
	public function updateByField($tableName, $data, $filed, $value, $isGetAffectedRows = false) {
		if (!$this->checkFiled($filed)) return false;
		return $this->update($tableName, $data, array('where' => "$filed = ?", 'whereValue' => $value), $isGetAffectedRows);
	}

	/**
	 * 插入一条数据
	 * 
	 * @param string $tableName 更新的数据表
	 * @param array $data	插入的数据
	 * @param array $field	相关的字段（可选）
	 * @return bool
	 */
	public function insert($tableName, $data) {
		$db = $this->getDbHandler();
		$db->getSqlBuilder()->from($tableName)->data($data)->insert();
		return $db->getLastInsertId();
	}

	/**
	 * 更新一条数据
	 * @param string $tableName 更新的数据表
	 * @param array $data	更新的数据
	 * @param array $field	相关的字段（可选）
	 * @param return $isGetAffectedRows 是否返回影响行数
	 * @return bool
	 */
	public function replace($tableName, $data, $isGetAffectedRows = false) {
		$db = $this->getDbHandler();
		$result = $db->getSqlBuilder()->from($tableName)->data($data)->replace();
		return $isGetAffectedRows ? $db->getAffectedRows() : $result;
	}

	/**
	 * 删除数据
	 * 
	 * @param string $tableName 更新的数据表
	 * @param srting $condition	更新的条件
	 * array(
	 * 'where' => '',  查询的条件
	 * 'whereValue' => array(),  查询条件中的变量值
	 * 'order' => array(), 排序类型  默认是降序排列，支持多字段排序  array('id'=>true,'name'=>false)
	 * 'limit' => '' 查询的数量
	 * )
	 * @param return $isGetAffectedRows 是否返回影响行数
	 * @return bool
	 */
	public function delete($tableName, $condition, $isGetAffectedRows = false) {
		$condition = $this->cookCondition($condition);
		$db = $this->getDbHandler();
		$result = $db->getSqlBuilder()->from($tableName)->where($condition['where'], $condition['whereValue'])->order($condition['order'])->limit($condition['limit'])->delete();
		return $isGetAffectedRows ? $db->getAffectedRows() : $result;
	}

	/**
	 * 通过某个字段删除数据
	 * 
	 * @param string $tableName 删除的数据表
	 * @param string $filed	所依据的字段名
	 * @param string $value 该字段的值
	 * @param return $isGetAffectedRows 是否返回影响行数
	 * @return bool
	 */
	public function deleteByField($tableName, $filed, $value, $isGetAffectedRows = false) {
		if (!$this->checkFiled($filed)) return array();
		return $this->delete($tableName, array('where' => "$filed = ?", 'whereValue' => $value), $isGetAffectedRows);
	}

	/**
	 * 单条查询
	 * 
	 * @param string $tableName 数据库表明
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
	 * @return array
	 */
	public function find($tableName, $condition = array()) {
		$condition = $this->cookCondition($condition);
		$db = $this->getDbHandler();
		$query = $db->getSqlBuilder()->from($tableName)->field($condition['field'])->where($condition['where'], $condition['whereValue'])->group($condition['group'])->having($condition['having'], $condition['havingValue'])->order($condition['order'])->limit(1)->select();
		return $db->getRow($query);
	}

	/**
	 * 通过某一字段查询
	 * @param string $table 查询的数据表
	 * @param string $filed	字段名
	 * @param string $value	该字段的值
	 * @return array
	 */
	public function findByField($tableName, $filed, $value) {
		if (!$this->checkFiled($filed)) return array();
		return $this->find($tableName, array('where' => "$filed = ?", 'whereValue' => $value));
	}

	/**
	 * 查询多条
	 * 
	 * @param string $tableName 数据库表名
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
	 * @return array() | array($result,$count)
	 */
	public function findAll($tableName, $condition = array(), $ifCount = false) {
		$condition = $this->cookCondition($condition);
		$db = $this->getDbHandler();
		$query = $db->getSqlBuilder()->from($tableName)->field($condition['field'])->where($condition['where'], $condition['whereValue'])->group($condition['group'])->having($condition['having'], $condition['havingValue'])->order($condition['order'])->limit($condition['limit'], $condition['offset'])->select();
		$result = $db->getAllRow();
		if (!$ifCount) return $result;
		$count = $this->count($tableName, $condition);
		return array($result, $count);
	}

	/**
	 * 统计个数
	 * 
	 * @param string $tableName 数据库表
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
	 * @return int
	 */
	public function count($tableName, $condition) {
		$condition = $this->cookCondition($condition);
		$condition['field'] = ' COUNT(*) as total';
		$result = $this->find($tableName, $condition);
		return (int) $result['total'];
	}

	/**
	 * 初始化条件数据
	 * 
	 * @param array $condition	条件
	 * @return array
	 */
	private function cookCondition($condition) {
		$defaultValue = array('field' => '*', 'where' => '', 'whereValue' => array(), 'group' => array(), 'order' => array(), 
			'limit' => null, 'offset' => null, 'having' => '', 'havingValue' => array());
		return array_merge($defaultValue, (array) $condition);
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