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
	public function find($condition = array(), $table = '', $config = array());
   
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
	public function findAll($condition = array(), $ifCount = false, $table = '', $config = array());
	
	/**
	 * 通过某一字段查询
	 * @param string $table 查询的数据表
	 * @param string $filed	字段名
	 * @param string $value	该字段的值
	 * @param array $config	独立配置信息
	 * @return array
	 */
	public function findByField($filed, $value, $table = '', $config = array());
	
	/**
	 * 插入一条数据
	 * 
	 * @param string $table 更新的数据表
	 * @param array $data	插入的数据
	 * @param array $field	相关的字段（可选）
	 * @return bool
	 */
	public function insert($data, $field = array(), $table = '') ;
   
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
	public function delete($condition, $table = '');
	
	/**
	 * 通过某个字段删除数据
	 * 
	 * @param string $table 删除的数据表
	 * @param string $filed	所依据的字段名
	 * @param string $value 该字段的值
	 * @return bool
	 */
	public function deleteByField($filed, $value, $table = '');
	
	/**
	 * 更新一条数据
	 * @param string $table 更新的数据表
	 * @param array $data	更新的数据
	 * @param array $field	相关的字段（可选）
	 * @return bool
	 */
	public function replace($data, $field = array(), $table = '');
    
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
	public function update($data, $condition = array(), $table = '');
	
	/**
	 * 通过字段更新
	 * 
	 * @param string $table 更新的数据表
	 * @param array $data	更新的数据
	 * @param string $filed	条件字段
	 * @param string $value	该字段的值
	 * @return bool 
	 */
	public function updateByField($data, $filed, $value, $table = '');
    
	/**
	 * 执行一条sql语句
	 * @param string $sql	sql语句
	 * @param string $type	主从选项  支持两个参数：slave 和  master
	 * @param array $config	独立配置信息
	 * @return resource|bool  数据库句柄
	 */
	public function query($sql, $config = array()) ;
	
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
	public function count($condition, $table = '', $config = array());

	
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