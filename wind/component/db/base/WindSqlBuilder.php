<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

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
    const SQL_ALLFIELD   = '*';
    
   
	
	protected $sql = array();
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
	 * @param string|array $where
	 * @param string|array $value
	 * @param boolean $group
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
	 * @param unknown_type $having
	 * @param unknown_type $value
	 * @param unknown_type $group
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
	 * 表解析
	 * @example array('tablename'=>'alais') or array('tablename'),tablename as alais
	 * @param array|string $table 表
	 * @return string;
	 */
	protected abstract function buildFrom();
	/**
	 * 是否查找相同的列
	 * @param boolean $distinct
	 * @return string
	 */
	protected abstract function buildDistinct();
	/**
	 * 解析表的列名
   	 * @example array('filedname'=>'alais') or array('filedname'),filedname as alais
	 * @param array|string $field 查询的字段
	 * @return string
	 */
	protected abstract function buildField();
	/**
	 * 解析连接查询
	 * @example array('tablename'=>array(jointype,onwhere,alias)) or array('left join tablename as a on a.id=b.id') 
	 * 			'left join tablename as a on a.id=b.id'
	 * @param string|array $join 连接条件
	 * @return string
	 */
	protected abstract function buildJoin();
	/**
	 * 解析查询条件
	 * @example array('lg','gt'=>('age',2),and,'lt'=>array('age',23),'gt',or,like=>array('name','suqian%')) or 
	 * 			( age > 2 and age < 23) or name like 'suqian%'
	 * @param array $where 查询条件
	 * @return string
	 */
	protected abstract function buildWhere();
	/**
	 * 解析分组
	 * @example array('field1','field2') or 'group by field1,field2'
	 * @param string|array $group 分组条件
	 * @return string
	 */
	protected abstract function buildGroup();
	/**
	 * 解析排序
	 * @example array('field1'=>'desc','field2'=>'asc') or 'order by field1 desc,field2 asc'
	 * @param array|string $order 排序条件
	 * @return string
	 */
	protected abstract function buildOrder();
	/**
	 * 解析对分组的过滤语句
	 * @param string $having
	 * @return string
	 */
	protected abstract function buildHaving();
	/**
	 * 解析查询limit语句
	 * @param int $limit  取得条数
	 * @param int $offset 偏移量
	 * @return string
	 */
	protected abstract function buildLimit();
	/**
	 * 解析更新数据
	 * @example array('field'=>'value');
	 * @param array $data 
	 * @return string
	 */
	protected abstract function buildSet($data);
	/**
	 * 解析添加数据
	 * @example array('field1','field2') or array(array('field1','field2'),array('field1','field2'))
	 * @param array $setData
	 * @return string
	 */
	protected abstract function buildData($setData);
	
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
	public function getInsertSql($table,$data) {
		return sprintf ( self::SQL_INSERT.'%s%s'.self::SQL_VALUES.'%s', 
			$this->buildFrom ($table), 
			$this->buildField ( array_keys($data)), 
			$this->buildData ( $data ) 
		);
	}
	/**
	 * 解析更新QL语句
	 * @param array $sql
	 * @return string
	 */
	public function getUpdateSql($sql = array()) {
		$sql = $sql ? $sql : $this->sql;
		return sprintf ( self::SQL_UPDATE.'%s'.self::SQL_SET.'%s%s%s%s', 
			$this->buildFrom (), 
			$this->buildSet (), 
			$this->buildWhere (), 
			$this->buildOrder (), 
			$this->buildLimit () 
		);
	}
	/**
	 * 解析删除SQL语句
	 * @param array $sql
	 * @return string
	 */
	public function getDeleteSql($sql = array()) {
		$sql = $sql ? $sql : $this->sql;
		return sprintf ( self::SQL_DELETE.' '.self::FROM.'%s%s%s%s', 
			$this->buildFrom (), 
			$this->buildWhere (), 
			$this->buildOrder (), 
			$this->buildLimit () 
		);
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
	public function getReplaceSql($table,$data){
		return sprintf ( self::SQL_REPLACE.'%s%s'.self::SQL_SET.'%s', 
			$this->buildTable ( $table ), 
			$this->buildField (array_keys($data)), 
			$this->buildData ($data) 
		);
	}
	
	public function getAffectedSql($ifquery){
		return sprintf ("SELECT%s",$this->buildAffected($ifquery));
	}
	
	public function getLastInsertIdSql(){
		return sprintf ("SELECT%s",$this->buildLastInsertId());
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