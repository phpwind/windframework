<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.exception.WindSqlException');
L::import('WIND:component.db.base.IWindDbConfig');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindSqlBuilder {
	
	/**
	 * @var array 运算表达式
	 */
	protected static $compare = array ('gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'eq' => '=', 'neq' => '!=', 'in' => 'IN', 'notin' => 'NOT IN', 'notlike' => 'NOT LIKE', 'like' => 'LIKE' );
	/**
	 * @var array 逻辑运算符
	 */
	protected static $logic = array ('and' => self::SQL_AND, 'or' => self::SQL_OR, 'xor' => self::SQL_XOR );
	/**
	 * @var array 分组条件
	 */
	protected static $group = array (self::LG => '(', self::RG => ')' );
	
	protected static $joinType = array(
        self::INNER=>self::SQL_INNER,
        self::LEFT=>self::SQL_LEFT,
        self::RIGHT=>self::SQL_RIGHT,
        self::FULL=>self::SQL_FULL,
        self::CROSS=>self::SQL_CROSS,
    );
    
    const LG = '(';
    const RG = ')';
	
    const DISTINCT = 'distinct';
    const FIELD   = 'field';
    const SET = 'set';
    const FROM    = 'from';
    const JOIN 	  = 'join';
    const WHERE   = 'where';
    const GROUP   = 'group';
    const HAVING  = 'having';
    const ORDER   = 'order';
    const LIMIT   = 'limit';
    const OFFSET   = 'offset';
	const INNER    = 'inner';
    const LEFT     = 'left';
    const RIGHT     = 'right';
    const FULL      = 'full';
    const CROSS    = 'cross';
    const DATA	= 'data';
    
    
    const SQL_SELECT     = 'SELECT ';
    const SQL_INSERT     = 'INSERT ';
    const SQL_UPDATE     = 'UPDATE ';
    const SQL_DELETE     = 'DELETE ';
    const SQL_REPLACE	 = 'REPLACE ';
    const SQL_FROM       = 'FROM ';
    const SQL_INNER    = 'INNER ';
    const SQL_LEFT     = 'LEFT ';
    const SQL_RIGHT     = 'RIGTH ';
    const SQL_FULL      = 'FULL ';
    const SQL_CROSS    = 'CROSS ';
    const SQL_JOIN       = 'JOIN ';
    const SQL_WHERE      = 'WHERE ';
    const SQL_DISTINCT   = 'DISTINCT ';
    const SQL_GROUP   = 'GROUP BY ';
    const SQL_ORDER   = 'ORDER BY ';
    const SQL_HAVING     = 'HAVING ';
    const SQL_AND        = 'AND ';
    const SQL_IN		 = 'IN ';
    const SQL_AS         = 'AS ';
    const SQL_OR         = 'OR ';
    const SQL_XOR         = 'XOR ';
    const SQL_ON         = 'ON ';
    const SQL_SET		 = 'SET ';
    const SQL_VALUES	 = 'VALUES ';
    const SQL_LIMIT	     = 'LIMIT ';
    const SQL_OFFSET	 = 'OFFSET ';
    const SQL_ASC        = 'ASC ';
    const SQL_DESC       = 'DESC ';
    const SQL_ALLFIELD   = '* ';

	/**
	 * @var array sql语句组装器
	 */
	protected $sql = array();
	/**
	 * @var WindDbAdapter db操作
	 */
	public $connection = null;
	
	public function __construct($config = array(),$driverConfig=array()){
		if($config && (is_array($config) || is_string($config))){
			$config = is_array($config) ? $config : C::getDatabaseConnection($config);
			$driverConfig = $driverConfig ? $driverConfig : C::getDataBaseDriver($config[IWindDbConfig::CONN_DRIVER]);
			$driverClass = $driverConfig[IWindDbConfig::DRIVER_CLASS]; 
			if(empty($driverClass)){
				throw new WindSqlException(WindSqlException::DB_DRIVER_NOT_EXIST);
			}
			if(strtolower(str_replace(array('Wind','Builder'),'',get_class($this))) != strtolower($config[IWindDbConfig::CONN_DRIVER])){
				throw new WindSqlException(WindSqlException::DB_DRIVER_BUILDER_NOT_MATCH);
			}
			L::import ($driverClass);
			$class = substr ( $driverClass, strrpos ( $driverClass, '.' ) + 1 );
			$this->connection = new $class($config);

		}
		if($config && is_object($config)){
			if(str_replace('Builder','',get_class($this)) != get_class($config)){
				throw new WindSqlException(WindSqlException::DB_DRIVER_BUILDER_NOT_MATCH);
			}
			$this->connection = $config;
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
	public abstract function from($table,$table_alias='',$fields='',$schema = '');
	/**
	 * 是否包含重复的值
	 * @param boolean $flag
	 * @return WindSqlBuilder
	 */
	public abstract function distinct($flag = true);
	/**
	 * 要查询的字段
	 * @param mixed $field
	 * @return WindSqlBuilder
	 */
	public abstract function field($field);
	/**
	 * 联表查询（内联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询内联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public abstract function join($table,$joinWhere,$alias='',$fields='',$schema ='');
	/**
	 * 联表查询（内联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询内联表的字段
	 * @param string $schema 数据库
	 * @see wind/component/db/base/WindSqlBuilder#join()
	 * @return WindSqlBuilder
	 */
	public abstract function innerJoin($table,$joinWhere,$alias='',$fields='',$schema ='');
	/**
	 * 联表查询（左联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询左联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public abstract function leftJoin($table,$joinWhere,$alias='',$fields='',$schema ='');
	/**
	 * 联表查询（右联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询右联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public abstract function rightJoin($table,$joinWhere,$alias='',$fields='',$schema ='');
	/**
	 * 联表查询（全联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询全联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public abstract function fullJoin($table,$joinWhere,$alias='',$fields='',$schema ='');
	/**
	 * 联表查询（交叉联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询交叉联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public abstract function crossJoin($table,$joinWhere,$alias='',$fields='',$schema ='');
	/**
	 * 与查询条件，支持占位符
	 * @param string|array $where 查询条件
	 * @param string|array $value 条件对应的值
	 * @param boolean $group  是否启用分组
	 * @return WindSqlBuilder
	 */
	public abstract function where($where,$value=array(),$group=false);
	/**
	 * 或查询条件，支持占位符
	 * @param string|array $where 查询条件
	 * @param string|array $value 条件对应的值
	 * @param boolean $group 是否启用分组
	 * @return WindSqlBuilder
	 */
	public abstract function orWhere($where,$value=array(),$group=false);
	/**
	 * 查询分组
	 * @param string|array $group 要分组的字段名
	 * @return WindSqlBuilder
	 */
	public abstract function group($group);
	/**
	 * 过滤分组
	 * @param string|array $having 过滤条件
	 * @param string|array $value  条件对应的值
	 * @param string|array $group  是否启用分组
	 * @return WindSqlBuilder
	 */
	public abstract function having($having,$value=array(),$group=false);
	/**
	 * 过滤分组
	 * @param unknown_type $having 过滤条件
	 * @param unknown_type $value  条件对应的值
	 * @param unknown_type $group  是否启用分组
	 * @return WindSqlBuilder
	 */
	public abstract function orHaving($having,$value=array(),$group=false);
	/**
	 * 对查询结果排序
	 * @param string|array $field 排序的字段
	 * @param boolean $type 升序还是倒序
	 * @return boolean
	 */
	public abstract function order($field,$type = true);
	/**
	 * 分页查询
	 * @param unknown_type $limit  偏移量
	 * @param unknown_type $offset 起始值 
	 * @return WindSqlBuilder
	 */
	public abstract function limit($limit,$offset = '');
	
	/**
	 * 解析insert值
	 * @param string $data
	 * @return WindSqlBuilder
	 */
	public abstract function data($data);
	/**
	 * 解析update值
	 * @param string|array $field
	 * @param string|array $value
	 * @return WindSqlBuilder
	 */
	public abstract function set($field,$value=array());
	
	/**
	 * 表解析
	 * @return string;
	 */
	protected abstract function buildFrom();
	/**
	 * 解析是否有重复的值
	 * @return string
	 */
	protected abstract function buildDistinct();
	/**
	 * 解析查询字段
	 * @return string
	 */
	protected abstract function buildField();
	/**
	 * 解析连接查询
	 * @return string
	 */
	protected abstract function buildJoin();
	/**
	 * 解析查询条件
	 * @return string
	 */
	protected abstract function buildWhere();
	/**
	 * 解析分组
	 * @return string
	 */
	protected abstract function buildGroup();
	/**
	 * 解析排序
	 * @return string
	 */
	protected abstract function buildOrder();
	/**
	 * 解析对分组的过滤语句
	 * @return string
	 */
	protected abstract function buildHaving();
	/**
	 * 解析分页查询
	 * @return string
	 */
	protected abstract function buildLimit();
	/**
	 * 解析更新数据
	 * @return string
	 */
	protected abstract function buildSet();
	/**
	 * 解析添加数据
	 * @return string
	 */
	protected abstract function buildData();
	
	/**
	 *返回影响行数的sql语句
	 *@param $ifquery 是否是select 语句
	 *@return string 
	 */
	public abstract function getAffected($ifquery);
	
	/**
	 *返回取得最后新增的sql语句
	 *@return string 
	 */
	public abstract function getLastInsertId();
	
	/**
	 * 对字符串转义
	 * @param string $value
	 * @return string
	 */
	public abstract function escapeString(&$value,$key='');
	
	/**
	 * @param strint $schema 数据库名
	 */
	public abstract function getMetaTableSql($schema);
	
	/**
	 * @param string $table  表名
	 */
	public abstract function getMetaColumnSql($table);
	/**
	 * 解析新增SQL语句
	 * @param array $sql
	 * @return string
	 */
	public function getInsertSql() {
		$sql = sprintf ( self::SQL_INSERT.'%s(%s)'.self::SQL_VALUES.'%s', 
			$this->buildFrom (), 
			$this->buildField (), 
			$this->buildData () 
		);
		$this->reset();
		return $sql;
	}
	/**
	 * 解析更新QL语句
	 * @param array $sql
	 * @return string
	 */
	public function getUpdateSql() {
		$sql = sprintf ( self::SQL_UPDATE.'%s'.self::SQL_SET.'%s%s%s%s', 
			$this->buildFrom (), 
			$this->buildSet (), 
			$this->buildWhere (), 
			$this->buildOrder (), 
			$this->buildLimit () 
		);
		$this->reset();
		return $sql;
	}
	/**
	 * 解析删除SQL语句
	 * @param array $sql
	 * @return string
	 */
	public function getDeleteSql() {
		$sql = sprintf ( self::SQL_DELETE.' '.self::FROM.'%s%s%s%s', 
			$this->buildFrom (), 
			$this->buildWhere (), 
			$this->buildOrder (), 
			$this->buildLimit () 
		);
		$this->reset();
		return $sql;
	}
	/**
	 * 解析查询SQL语句
	 * @param array $sql
	 * @return string
	 */
	public function getSelectSql() {
		$sql = sprintf ( self::SQL_SELECT.'%s%s'.self::SQL_FROM.'%s%s%s%s%s%s%s', 
			$this->buildDistinct (), 
			$this->buildField (), 
			$this->buildFROM (), 
			$this->buildJoin (), 
			$this->buildWhere (), 
			$this->buildGroup (), 
			$this->buildHaving (), 
			$this->buildOrder (), 
			$this->buildLimit () 
			);
		$this->reset();
		return $sql;
	}
	
	/**
	 * 解析replace SQL语句
	 * @param array $sql
	 * @return string
	 */
	public function getReplaceSql(){
		$sql = sprintf ( self::SQL_REPLACE.'%s(%s)'.self::SQL_SET.'%s', 
			$this->buildFROM (), 
			$this->buildField (), 
			$this->buildData () 
		);
		$this->reset();
		return $sql;
	}
	
	public function getAffectedSql($ifquery){
		return sprintf ("SELECT%s",$this->buildAffected($ifquery));
	}
	
	public function getLastInsertIdSql(){
		return sprintf ("SELECT%s",$this->buildLastInsertId());
	}
	
	/**
	 * 执行数据库delete操作
	 * @return boolean
	 */
	public function delete(){
		$this->verifyAdapter();
		$this->connection->delete($this->getDeleteSql());
		return $this;
	}
	
	/**
	 * 执行数据库update操作
	 * @return boolean
	 */
	public function update(){
		$this->verifyAdapter();
		$this->connection->update($this->getUpdateSql());
		return $this;
	}
	
	/**
	 * 执行数据库select操作
	 * @return boolean
	 */
	public function select(){
		$this->verifyAdapter();
		$this->connection->select($this->getSelectSql());
		return $this;
	}
	
	/**
	 * 执行数据库insert操作
	 * @return boolean
	 */
	public function insert(){
		$this->verifyAdapter();
		$this->connection->insert($this->getInsertSql());
		return $this;
	}
	
	/**
	 * 执行数据库replace操作
	 * @return boolean
	 */
	public function replace(){
		$this->verifyAdapter();
		$this->connection->insert($this->getReplaceSql());
		return $this;
	}
	
	/**
	 * 取得结果集
	 * @param int $fetch_type 类型
	 * @return array
	 */
	public function getAllRow($fetch_type){
		$this->verifyAdapter();
		return $this->connection->getAllRow($fetch_type);
	}
	
	
	public function getRow($fetch_type){
		$this->verifyAdapter();
		return $this->connection->getRow($fetch_type);
	}
	
	
	
	private function verifyAdapter(){
		if(empty($this->connection)){
			throw new WindSqlException(WindSqlException::DB_ADAPTER_NOT_EXIST);
		}
		return true;
	}
	
	/**
	 * 判断是否是二维数组
	 * @param array $array
	 * @return number
	 */
	public function getDimension($array = array()) {
		$dim = 0;
		foreach ($array as $value ) {
			return  is_array($value) ? $dim+=2 : ++$dim;
		}
		return $dim;
	}
	
	/**
	 * 要解析的一维数组，单条添加数据
	 * @param array $data 要解析的数据
	 * @return string
	 */
	public function buildSingleData($data) {
		foreach ( $data as $key => $value ) {
			$data [$key] = $this->escapeString ( $value );
		}
		return $this->sqlFillSpace('(' . implode ( ',', $data ) . ')');
	}
	
	/**
	 * 解析二维数组，批量添加
	 * @param array $multiData 要解析的数据
	 * @return string
	 */
	public function buildMultiData($multiData) {
		$iValue = '';
		foreach ( $multiData as $data ) {
			$iValue .= $this->buildSingleData ( $data );
		}
		return $iValue;
	}
	
	/**
	 * 在字符串头尾添加空格或空白字符
	 * @param string $value  字符串
	 * @return string
	 */
	public function sqlFillSpace($value) {
		return str_pad ( $value, strlen ( $value ) + 2, " ", STR_PAD_BOTH );
	}
	
	public function reset($type=''){
		if($type){
			unset($this->sql[$type]); 
		}else{
			$this->sql = array();
		}
	}
}