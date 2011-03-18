<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2011-3-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <xiaoxia.xuxx@aliyun-inc.com>
 * @version $Id$ 2011-3-11
 * @package
 */
interface IWindDbTemplate {
    const FIELD = 'field';
    const WHERE = 'where';
    const WHEREVALUE = 'whereValue';
    const ORDER = 'order';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const GROUP = 'group';
    const HAVING = 'having';
    const HAVINGVALUE = 'havingValue';
    const RESULTTINDEXKEY = 'resultIndexKey';
	
	/**
	 * 获得数据库链接
	 * @return WindConnectionManager|WindDbAdapter  $connection
	 */
	public function getConnection();

	/**
	 * 设置数据库链接
	 * @param WindConnectionManager|WindDbAdapter $connection
	 */
	public function setConnection($connection);
    
	/**
	 * 执行sql语句
	 * 
	 * @param string $sql sql语句
	 * @return bool  执行结果
	 */
	public function query($sql);

	/**
	 * 执行sql语句并且返回所有结果集
	 * 
	 * @param string $sql	sql语句
	 * @param string $resultIndexKey 返回结果集数组的索引字段
	 * @return array 执行结果数组 
	 */
	public function findAllBySql($sql, $resultIndexKey = '');

	/**
	 * 执行sql语句,并且返回一行结果集
	 * 
	 * @param string $sql	sql语句
	 * @return array 执行结果数组 
	 */
	public function findBySql($sql);

	/**
	 * 插入一条数据
	 * 
	 * @param string $tableName 更新的数据表
	 * @param array $data	插入的数据
	 * @return bool
	 */
	public function insert($tableName, $data);
	
	/**
	 * 批量插入数据
	 *
	 * @param string $tableName 待插入的表名
	 * @param array $field  插入的字段
	 * @param array $data   插入的数据
	 */
	public function batchInsert($tableName, array $field, array $data);
	
	/**
	 * 更新一条数据
	 * @param string $tableName 更新的数据表
	 * @param array $data	更新的数据
	 * @param boolean $isGetAffectedRows 是否返回影响行数
	 * @return bool
	 */
	public function replace($tableName, $data, $isGetAffectedRows = false);
	
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
	 * @param boolean $isGetAffectedRows 是否返回影响行数
	 * @return bool
	 */
	public function update($tableName, $data, $condition = array(), $isGetAffectedRows = false);

	/**
	 * 通过字段更新
	 * 
	 * @param string $tableName 更新的数据表
	 * @param array $data	更新的数据
	 * @param string $filed	条件字段
	 * @param string $value	该字段的值
	 * @param boolean $isGetAffectedRows 是否返回影响行数
	 * @return bool 
	 */
	public function updateByField($tableName, $data, $filed, $value, $isGetAffectedRows = false);

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
	 * @param boolean $isGetAffectedRows 是否返回影响行数
	 * @return bool
	 */
	public function delete($tableName, $condition, $isGetAffectedRows = false);

	/**
	 * 通过某个字段删除数据
	 * 
	 * @param string $tableName 删除的数据表
	 * @param string $filed	所依据的字段名
	 * @param string $value 该字段的值
	 * @param boolean $isGetAffectedRows 是否返回影响行数
	 * @return bool
	 */
	public function deleteByField($tableName, $filed, $value, $isGetAffectedRows = false);
	
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
	public function find($tableName, $condition = array());
	
	/**
	 * 通过某一字段查询
	 * @param string $table 查询的数据表
	 * @param string $filed	字段名
	 * @param string $value	该字段的值
	 * @return array
	 */
	public function findByField($tableName, $filed, $value);
	
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
	 * 'resultIndexKey' => '' 返回的结果集数组的索引字段
	 * )
	 * @param bool $ifCount	是否需要统计总个数
	 * @return array() | array($result,$count)
	 */
	public function findAll($tableName, $condition = array(), $ifCount = false);
	
	/**
	 * 通过一个字段查询多条
	 * 
	 * @param string $tableName 数据库表名
	 * @param string $field 查询的字段
	 * @param string $value 字段值
	 * @param bool $ifCount	是否需要统计总个数
	 * @return array() | array($result,$count)
	 */
	public function findAllByField($tableName, $field, $value, $ifCount = false);

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
	public function count($tableName, $condition);

	/**
	 * 判断字段是否存在 不存在返回false，存在返回true
	 * 
	 * @param string $tableName 表名
	 * @param  string $field 字段
	 * @return bool
	 */
	public function isExistField($tableName, $field);

	/**
	 * 获取表字段名
	 * 
	 * @param string $tableName 待获取的表名
	 * @return array 
	 */
	public function getTableFields($tableName);


	/**
	 * 创建数据库表
	 * @param string $tableName 待创建的表名
	 * @param string $statement 创建的语句体
	 * @param string $engine 创建的引擎
	 * @param string $charset 创建的字符集
	 * @param int $auto_increment 自动增号开始
	 * @return boolean
	 */
	public function createTable($tableName, $statement, $engine = 'MyISAM', $charset = 'GBK', $auto_increment = '');
    
	/**
	 * 删除数据表
	 * @param string $tableName 待删除的表名
	 * @return boolean
	 */
	public function dropTable($tableName);
}
?>