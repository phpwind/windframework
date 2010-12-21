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
	
	/**
	 * @var array 连接类型
	 */
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
	public  function from($table,$table_alias='',$fields='',$schema = ''){
		$fields && $this->assembleFieldByTable($fields,$table,$table_alias);
		return $this->assembleSql(array($table=>array($table_alias,$schema)),self::FROM);
	}
	/**
	 * 是否包含重复的值
	 * @param boolean $flag
	 * @return WindSqlBuilder
	 */
	public  function distinct($flag = true){
		$this->sql[self::DISTINCT] = $flag ? self::SQL_DISTINCT : '';
		return $this;
	}
	/**
	 * 要查询的字段
	 * @param mixed $field
	 * @return WindSqlBuilder
	 */
	public  function field($field){
		$params = func_num_args();
		$field = $params >1 ? func_get_args() : func_get_arg(0);
		return $this->assembleSql($field,self::FIELD);
	}
	/**
	 * 联表查询（内联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询内联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public function join($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::INNER,$table,$joinWhere,$alias,$fields,$schema);
	}
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
	public function innerJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::INNER,$table,$joinWhere,$alias,$fields,$schema);
	}
	/**
	 * 联表查询（左联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询左联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public function leftJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::LEFT,$table,$joinWhere,$alias,$fields,$schema);
	}
	/**
	 * 联表查询（右联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询右联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public function rightJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::RIGHT,$table,$joinWhere,$alias,$fields,$schema);
	}
	/**
	 * 联表查询（全联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询全联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public function fullJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::FULL,$table,$joinWhere,$alias,$fields,$schema);
	}
	/**
	 * 联表查询（交叉联接）
	 * @param string $table 表名
	 * @param string $joinWhere 联接条件
	 * @param string $alias 表别名
	 * @param string|array $fields 要查询交叉联表的字段
	 * @param string $schema 数据库
	 * @return WindSqlBuilder
	 */
	public function crossJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::CROSS,$table,$joinWhere,$alias,$fields,$schema);
	}
	/**
	 * 与查询条件，支持占位符
	 * @param string|array $where 查询条件
	 * @param string|array $value 条件对应的值
	 * @param boolean $group  是否启用分组
	 * @return WindSqlBuilder
	 */
	public function  where($where,$value=array(),$group=false){
		return $this->assembleWhere($where,self::WHERE,$value,true,$group);
	}
	/**
	 * 或查询条件，支持占位符
	 * @param string|array $where 查询条件
	 * @param string|array $value 条件对应的值
	 * @param boolean $group 是否启用分组
	 * @return WindSqlBuilder
	 */
	public  function orWhere($where,$value=array(),$group=false){
		return $this->assembleWhere($where,self::WHERE,$value,false,$group);
	}
	/**
	 * 查询分组
	 * @param string|array $group 要分组的字段名
	 * @return WindSqlBuilder
	 */
	public  function group($group){
		$params = func_num_args();
		$group = $params >1 ? func_get_args() : func_get_arg(0);
		return $this->assembleSql($group,self::GROUP);
	}
	/**
	 * 过滤分组
	 * @param string|array $having 过滤条件
	 * @param string|array $value  条件对应的值
	 * @param string|array $group  是否启用分组
	 * @return WindSqlBuilder
	 */
	public  function having($having,$value=array(),$group=false){
		return $this->assembleWhere($having,self::HAVING,$value,true,$group);
	}
	/**
	 * 过滤分组
	 * @param unknown_type $having 过滤条件
	 * @param unknown_type $value  条件对应的值
	 * @param unknown_type $group  是否启用分组
	 * @return WindSqlBuilder
	 */
	public function orHaving($having,$value=array(),$group=false){
		return $this->assembleWhere($having,self::HAVING,$value,false,$group);
	}
	/**
	 * 对查询结果排序
	 * @param string|array $field 排序的字段
	 * @param boolean $type 升序还是倒序
	 * @return boolean
	 */
	public  function order($field,$type = true){
		$field = is_array($field) ? $field : array($field=>$type);
		return $this->assembleSql($field,self::ORDER);
	}
	/**
	 * 分页查询
	 * @param unknown_type $limit  偏移量
	 * @param unknown_type $offset 起始值 
	 * @return WindSqlBuilder
	 */
	public  function limit($limit,$offset = ''){
		$this->assembleSql((int)$limit,self::LIMIT);
		return $this->assembleSql((int)$offset,self::OFFSET);
	}
	
	/**
	 * 解析insert值
	 * @param string $data
	 * @return WindSqlBuilder
	 */
	public function data($data){
		$params = func_num_args();
		$data = $params >1 ? func_get_args() : func_get_arg(0);
		$key = array_keys ( $data );
		$tmp_data = $field = array ();
		if (is_string ( $key [0] )) {
			$rows = count ( $data [$key [0]] );
			for($i = 0; $i < $rows; $i ++) {
				foreach ( $data as $key => $value ) {
					$fvalues = array_values($field);
					if(!in_array($key,$fvalues)){
						$field [] = $key;
					}
					if(is_array($value)){
						$tmp_data [$i] [] = $value [$i];
						unset ( $data [$key] [$i] );
					}else{
						$tmp_data [] = $value;
					}
				}
			}
		};
		$data = $tmp_data ? $tmp_data : $data;
		$field && $this->field($field);
		return $this->assembleSql($data,self::DATA);
	}
	/**
	 * 解析update值
	 * @param string|array $field
	 * @param string|array $value
	 * @return WindSqlBuilder
	 */
	public function set($field,$value=array()){
		$realSet = $this->parsePlaceHolder($field,$value,',');
		return $this->assembleSql($realSet,self::SET);
	}
	
	
/**
	 * 组装sql语句
	 * @param mixed $assembleValue 组装条件
	 * @param mixed $assembleType  组装类型
	 * @return WindMySqlBuilder
	 */
	private function assembleSql($assembleValue,$assembleType){
		if(empty($assembleValue)){
			return $this;
		}
		if(!isset($this->sql[$assembleType]) || empty($this->sql[$assembleType]) || !is_array($this->sql[$assembleType])){
			$this->sql[$assembleType] = array();
		}
		$assembleValue = is_array($assembleValue) ? $assembleValue : array($assembleValue);
		foreach($assembleValue as $key=>$value){
			if(is_string($key)){
				$this->sql[$assembleType][$key] = $value;
			}
			if(is_int($key)){
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
	private function assembleWhere($where,$whereType=self::WHERE,$value=array(),$logic = true,$group = false){
		$_where = '';
		if(!in_array($whereType,array(self::WHERE,self::HAVING))){
			throw new WindSqlException(WindSqlException::DB_WHERE_ERROR);
		}
		$where = $this->trueWhere($where,$value,$logic);
		if($group && in_array($group,self::$group)){
			$_where = self::$group[$group];
		}
		if($this->sql[$whereType]){
			if($logic){
				$_where .= self::SQL_AND.$where;
			}else{
				$_where .= self::SQL_OR.$where;
			}
		}else{
			$_where[] = $where;
		}
		return $this->assembleSql($_where,$whereType);
	}
	
	/**
	 * 组装要对指定表进行操作的字段
	 * @param mixed $fields 表字段
	 * @param mixed $table 表名
	 * @param mixed $table_alias 表别名
	 * @return WindMySqlBuilder
	 */
	private function assembleFieldByTable($fields,$table,$table_alias=''){
		if($fields && (is_string($fields) || is_array($fields))){
			$fields = is_array($fields) ? $fields : explode(',',$fields);
			foreach($fields as $key=>$field){
				$fields[$key] = (false === ($pos = strpos('.',$field))) ? $table_alias ? $table_alias.'.'.$field : $table.'.'.$field :$field;
			}
			$this->assembleSql($fields,self::FIELD);
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
	private  function assembleJoin($type,$table,$joinWhere,$table_alias='',$fields='',$schema =''){
		if(!in_array($type,array_keys(self::$joinType))){
			throw new WindSqlException(WindSqlException::DB_JOIN_TYPE_ERROR);
		}
		$fields && $this->assembleFieldByTable($fields,$table,$table_alias);
		return $this->assembleSql(array($table=>array($type,$joinWhere,$table_alias,$schema)),self::JOIN);
	}
	
	/**
	 * 解析占位符
	 * @param mixed $text 包含占位符的文本
	 * @param mixed $replace 将占位符替换成指定的值
	 * @param mixed $separators 分隔符
	 * @return mixed 返回解析后的文本
	 */
	private function parsePlaceHolder($text,$replace=array(),$separators=','){
		if($text  && is_array($text)){
			foreach($text as $key=>$_where){
				if(is_int($key)){
					$text[$key] = strpos($_where,'?') ? str_replace('?',$this->escapeString($replace[$key]),$_where) : $_where;
				}
				if(is_string($key)){
					$value = $key.'='. $this->escapeString($_where);
					$text[] = $value;
					unset($text[$key]);
				}
			}
			$text = implode($separators ? $separators : ',',$text);
		}
		if($text && $replace && is_string($text)){
			if(preg_match_all('/([\w\d_\.`]+[\t ]*(>|<|!=|>=|<=|=|in|not[\t ]+in)[\t ]*)(\?)/i',$text,$matches)){
				$replace = is_array($replace) ? $replace : array($replace);
				foreach($matches[1] as $key=>$match){
					if(in_array(strtoupper(trim($matches[2][$key])),array('IN','NOT IN'))){
						$replace[$key] = is_array($replace[$key]) ? $replace[$key] : array($replace[$key]);
						array_walk ( $replace[$key], array ($this, 'escapeString' ) );
						$_replace = $match.self::LG.implode ( ',', $replace[$key] ).self::RG;
					}else{
						$_replace = $match.$this->escapeString($replace[$key]);
					}
					$text = str_replace($matches[0][$key],$_replace,$text);
				}
			}
			if(preg_match_all('/([\w\d_\.`]+[\t ]*(>|<|!=|>=|<=|=|in|not[\t ]+in)[\t ]*)(:[\w\d_\.]+)/i',$text,$matches)){
				if(is_string($replace)){
					$tmp = explode('=',$replace);
					$replace = array($tmp[0]=>$tmp[1]);
				}
				foreach($matches[1] as $key=>$match){
					$_trueKey = $matches[3][$key];
					if(in_array(strtoupper(trim($matches[2][$key])),array('IN','NOT IN'))){
						array_walk ( $replace[$_trueKey], array ($this, 'escapeString' ) );
						$_replace = $match.self::LG.implode ( ',', $replace[$_trueKey] ).self::RG;
					}else{
						$_replace = $match.$this->escapeString($replace[$_trueKey]);
					}
					$text = str_replace($matches[0][$key],$_replace,$text);
				}
			}
		}
		return $text;
	}
	
	/**
	 * 返回真实的where条件
	 * @param mixed $where 包含占位符的where条件
	 * @param mixed $value where条件中占位符对应的值
	 * @param boolean $logic 逻辑条件
	 * @return mixed
	 */
	private function trueWhere($where,$value = array(),$logic = true){
		return $this->parsePlaceHolder($where,$value, $this->sqlFillSpace ($logic ? self::SQL_AND : self::SQL_OR));
	}
	
	/**
	 * 表解析
	 * @return string;
	 */
	protected function buildFrom() {
		if (empty ( $this->sql[self::FROM] ) ||   !is_array ( $this->sql[self::FROM] )) {
			throw new WindSqlException (WindSqlException::DB_TABLE_EMPTY);
		}
		$tableList = '';
		foreach ( $this->sql[self::FROM] as $key => $value ) {
			$tableList .= $tableList ? ',' . $this->getAlias ( $key,$value[0],$value[1] ) : $this->getAlias ( $key,$value[0],$value[1] );
		}
		return $this->sqlFillSpace ( $tableList );
	}
	/**
	 * 解析是否有重复的值
	 * @return string
	 */
	protected function buildDistinct() {
		return $this->sqlFillSpace ($this->sql[self::DISTINCT]);
	}
	/**
	 * 解析查询字段
	 * @return string
	 */
	protected function buildField() {
		if (empty ( $this->sql[self::FIELD] ) ||  !is_array ( $this->sql[self::FIELD] )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_FIELD_FORMAT);
		}
		$fieldList = '';
		foreach ( $this->sql[self::FIELD] as $key => $value ) {
			if (is_int ( $key )) {
				$fieldList .= $fieldList ? ',' . $value : $value;
			}
			if (is_string ( $key )) {
				$fieldList .= $fieldList ? ',' . $this->getAlias ( $key,$value ) : $this->getAlias ( $key,$value );
			}
		}
		return $this->sqlFillSpace ( $fieldList );
	}
	/**
	 * 解析连接查询
	 * @return string
	 */
	protected function buildJoin() {
		if (empty ( $this->sql[self::JOIN] ) ||  !is_array ( $this->sql[self::JOIN] )) {
			return '';
		}
		$joinContidion = '';
		foreach ( $this->sql[self::JOIN] as $table => $config ) {
			if (is_string ( $config ) && is_int($table)) {
				throw new WindSqlException (WindSqlException::DB_QUERY_JOIN_FORMAT);
			}
			if (is_array ( $config ) && is_string($table)) {
				$table = $this->getAlias ( $table, $config [2],$config [3]);
				$joinWhere = $config [1] ? self::SQL_ON . $config [1] : '';
				$condition = self::$joinType[$config [0]] . self::SQL_JOIN . $table . $joinWhere;
				$joinContidion .= $joinContidion ? ' ' . $condition : $condition;
			}
		}
		return $this->sqlFillSpace ( $joinContidion );
	}
	/**
	 * 解析查询条件
	 * @return string
	 */
	protected function buildWhere() {
		$where = is_array($this->sql[self::WHERE]) ? implode(' ',$this->sql[self::WHERE]) : $this->sql[self::WHERE];
		return $where ? $this->sqlFillSpace (self::SQL_WHERE.$where) : '' ;
	}
	/**
	 * 解析分组
	 * @return string
	 */
	protected function buildGroup() {
		$group = is_array ( $this->sql[self::GROUP] ) ? implode ( ',', $this->sql[self::GROUP] ) : $this->sql[self::GROUP];
		return $group ? $this->sqlFillSpace (self::SQL_GROUP . $group) : '';
	}
	/**
	 * 解析排序
	 * @return string
	 */
	protected function buildOrder() {
		$orderby = '';
		if (is_array ( $this->sql[self::ORDER] )) {
			foreach ( $this->sql[self::ORDER] as $key => $value ) {
				$orderby .= ($orderby ? ',' : '') . (is_string ( $key ) ? $key . ' ' .( $value ? self::SQL_DESC:self::SQL_ASC) : $value);
			}
		} else {
			$orderby = $this->sql[self::ORDER];
		}
		return $orderby ? $this->sqlFillSpace (self::SQL_ORDER . $orderby) : '';
	}
	/**
	 * 解析对分组的过滤语句
	 * @return string
	 */
	protected function buildHaving() {
		 $having = is_array($this->sql[self::HAVING]) ? implode(' ',$this->sql[self::HAVING]) : $this->sql[self::HAVING];
		 return $having ? $this->sqlFillSpace (self::SQL_HAVING.$having) : '' ;
	}
	/**
	 * 解析分页查询
	 * @return string
	 */
	protected function buildLimit() {
		if(empty($this->sql[self::LIMIT])){
			return ;
		}
		if(is_string($this->sql[self::LIMIT])){
			return $this->sqlFillSpace($this->sql[self::LIMIT]);
		}
		if(is_array($this->sql[self::LIMIT])){
			$this->sql[self::LIMIT] = array_pop($this->sql[self::LIMIT]);
		}
		if(is_array($this->sql[self::OFFSET])){
			$this->sql[self::OFFSET] = array_pop($this->sql[self::OFFSET]);
		}
		return $this->sqlFillSpace ( ($sql = $this->sql[self::LIMIT] > 0 ? self::SQL_LIMIT . $this->sql[self::LIMIT].' ' : '') ? $this->sql[self::OFFSET] > 0 ? $sql . self::SQL_OFFSET . $this->sql[self::OFFSET] : $sql : '' );
	}
	/**
	 * 解析更新数据
	 * @return string
	 */
	protected function buildSet() {
		if (empty ( $this->sql[self::SET] )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_UPDATE_DATA);
		}
		if (is_string ( $this->sql[self::SET] )) {
			return $this->sql[self::SET];
		}
		foreach ( $this->sql[self::SET] as $key => $value ) {
			if(is_string($key)){
				$this->sql[self::SET][$key] = $key . '=' . $this->escapeString ( $value );
			}else{
				$this->sql[self::SET][$key] = $value;
			}
		}
		return $this->sqlFillSpace ( implode ( ',', $this->sql[self::SET] ) );
	}
	/**
	 * 解析添加数据
	 * @return string
	 */
	protected function buildData() {
		if (empty ( $this->sql[self::DATA] ) || ! is_array ( $this->sql[self::DATA] )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_INSERT_DATA);
		}
		if($this->getDimension ( $this->sql[self::DATA] ) == 1){
			return $this->buildSingleData ( $this->sql[self::DATA] );
		}
		if($this->getDimension ( $this->sql[self::DATA] ) >= 2){
			return $this->buildMultiData ( $this->sql[self::DATA]  );
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
	public abstract function getInsertSql() ;
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
	public function delete(){
		$this->verifyAdapter();
		return $this->connection->delete($this->getDeleteSql());
	}
	
	/**
	 * 执行数据库update操作
	 * @return boolean
	 */
	public function update(){
		$this->verifyAdapter();
		return $this->connection->update($this->getUpdateSql());
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
		return $this->connection->insert($this->getInsertSql());
	}
	
	/**
	 * 执行数据库replace操作
	 * @return boolean
	 */
	public function replace(){
		$this->verifyAdapter();
		return $this->connection->replace($this->getReplaceSql());
	}
	
	/**
	 * 取得结果集
	 * @param int $fetch_type 类型
	 * @return array
	 */
	public function getAllRow($fetch_type = IWindDbConfig::RESULT_ASSOC){
		$this->verifyAdapter();
		return $this->connection->getAllRow($fetch_type);
	}
	
	
	/**
	 * 取得一条结果集
	 * @param int $fetch_type 类型
	 * @return array
	 */
	public function getRow($fetch_type = IWindDbConfig::RESULT_ASSOC){
		$this->verifyAdapter();
		return $this->connection->getRow($fetch_type);
	}
	
	
	
	public function verifyAdapter(){
		if(empty($this->connection)){
			throw new WindSqlException(WindSqlException::DB_ADAPTER_NOT_EXIST);
		}
		return true;
	}
	
	/**
	 * 取得别名标识
	 * @param string $name 源名称
	 * @param string $alias 别名
	 * @param string $schema 数据库名称
	 * @return string
	 */
	public function getAlias($name,$alias = '',$schema='') {
		$name = $alias ? $name.' ' .  self::SQL_AS . $alias : $name;
		return $this->sqlFillSpace ($schema ? $schema.'.'.$name : $name);
	}
	
	/**
	 * 对字符串转义
	 * @param string $value
	 * @return string
	 */
	public function escapeString(&$value,$key='') {
		if(is_int($value)){
			$value = (int)$value;
		}elseif(is_string($value)){
			$value = " '" . $value . "' ";
		}elseif(is_float($value)){
			$value = (float)$value;
		}elseif(is_object($value)){
			$value = serialize($value);
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
			$iValue .= $iValue ? ','.$this->buildSingleData ( $data ) : $this->buildSingleData ( $data );
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
	
	/**
	 * 重置sql语句组装条件
	 * @param unknown_type $type
	 */
	public function reset($type=''){
		if($type){
			unset($this->sql[$type]); 
		}else{
			$this->sql = array();
		}
	}
	
	public function getSql($type=''){
		return $type ? $this->sql[$type] : $this->sql;
	}
}