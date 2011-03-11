<?php
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindDbTemplate {
    
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
	public function findAll($tableName, $condition = array(), $ifCount = false);
	
	/**
	 * 通过某一字段查询
	 * @param string $table 查询的数据表
	 * @param string $filed	字段名
	 * @param string $value	该字段的值
	 * @return array
	 */
	public function findByField($tableName, $filed, $value) ;
	
	/**
	 * 插入一条数据
	 * 
	 * @param string $tableName 更新的数据表
	 * @param array $data	插入的数据
	 * @return bool
	 */
	public function insert($tableName, $data) ;
   
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
	public function delete($tableName, $condition, $isGetAffectedRows = false);
	
	/**
	 * 通过某个字段删除数据
	 * 
	 * @param string $tableName 删除的数据表
	 * @param string $filed	所依据的字段名
	 * @param string $value 该字段的值
	 * @param return $isGetAffectedRows 是否返回影响行数
	 * @return bool
	 */
	public function deleteByField($tableName, $filed, $value, $isGetAffectedRows = false);
	
	/**
	 * 更新一条数据
	 * @param string $tableName 更新的数据表
	 * @param array $data	更新的数据
	 * @param return $isGetAffectedRows 是否返回影响行数
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
	 * @param return $isGetAffectedRows 是否返回影响行数
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
	 * @param return $isGetAffectedRows 是否返回影响行数
	 * @return bool 
	 */
	public function updateByField($tableName, $data, $filed, $value, $isGetAffectedRows = false);
    
	/**
	 * 执行一条sql语句
	 * @param string $sql	sql语句
	 * @return array|bool  数据库句柄
	 */
	public function query($sql) ;
	
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
	 * 获得数据库链接
	 * @return the $connection
	 */
	public function getConnection();

	/**
	 * 设置数据库链接
	 * @param object $connection
	 */
	public function setConnection($connection);
	
}
?>