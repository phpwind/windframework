<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.db.base.WindSqlBuilder');
/**
 * mysql常用sql语句组装器
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
final class WindMySqlBuilder extends WindSqlBuilder { 
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#from()
	 */
	public  function from($table,$table_alias='',$fields='',$schema = ''){
		$fields && $this->assembleFieldByTable($fields,$table,$table_alias);
		return $this->assembleSql(array($table=>array($table_alias,$schema)),self::FROM);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#distinct()
	 */
	public  function distinct($flag = true){
		$this->sql[self::DISTINCT] = $flag ? self::SQL_DISTINCT : '';
		return $this;
	}
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#field()
	 */
	public  function field($field){
		$params = func_num_args();
		$field = $params >1 ? func_get_args() : func_get_arg(0);
		return $this->assembleSql($field,self::FIELD);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#join()
	 */
	public function join($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::INNER,$table,$joinWhere,$alias,$fields,$schema);
	}

	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#leftJoin()
	 */
	public function leftJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::LEFT,$table,$joinWhere,$alias,$fields,$schema);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#rightJoin()
	 */
	public function rightJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::RIGHT,$table,$joinWhere,$alias,$fields,$schema);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#innerJoin()
	 */
	public function innerJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::INNER,$table,$joinWhere,$alias,$fields,$schema);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#crossJoin()
	 */
	public function crossJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::CROSS,$table,$joinWhere,$alias,$fields,$schema);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#fullJoin()
	 */
	public function fullJoin($table,$joinWhere,$alias='',$fields='',$schema =''){
		return $this->assembleJoin(self::FULL,$table,$joinWhere,$alias,$fields,$schema);
	}
	
	/**
	 * @param unknown_type $where
	 * @param unknown_type $value
	 * @param unknown_type $group
	 * @return WindMySqlBuilder
	 */
	public function  Where($where,$value=array(),$group=false){
		return $this->assembleWhere($where,self::WHERE,$value,true,$group);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#orWhere()
	 */
	public  function orWhere($where,$value=array(),$group=false){
		return $this->assembleWhere($where,self::WHERE,$value,false,$group);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#group()
	 */
	public  function group($group){
		$params = func_num_args();
		$group = $params >1 ? func_get_args() : func_get_arg(0);
		return $this->assembleSql($group,self::GROUP);
	}
	
	/**
	 * @param unknown_type $having
	 * @param unknown_type $value
	 * @param unknown_type $group
	 * @return WindMySqlBuilder
	 */
	public  function having($having,$value=array(),$group=false){
		return $this->assembleWhere($having,self::HAVING,$value,true,$group);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#orHaving()
	 */
	public function orHaving($having,$value=array(),$group=false){
		return $this->assembleWhere($having,self::HAVING,$value,false,$group);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#order()
	 */
	public  function order($field,$type = true){
		$field = is_array($field) ? $field : array($field=>$type);
		return $this->assembleSql($field,self::ORDER);
	}

	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#limit()
	 */
	public  function limit($limit,$offset = ''){
		$this->assembleSql((int)$limit,self::LIMIT);
		return $this->assembleSql((int)$offset,self::OFFSET);
	}
	
	public function data($data){
		$params = func_num_args();
		$data = $params >1 ? func_get_args() : func_get_arg(0);
		return $this->assembleSql($data,self::DATA);
	}
	
	public function set($field,$value=array()){
		$realSet = $this->parsePlaceHolder($field,$value,',');
		return $this->assembleSql($realSet,self::SET);
	}
	
	/**
	 * @param unknown_type $assembleValue
	 * @param unknown_type $assembleType
	 * @return WindMySqlBuilder
	 */
	private function assembleSql($assembleValue,$assembleType){
		if(empty($this->sql[$assembleType]) || !is_array($this->sql[$assembleType])){
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
	 * @param unknown_type $where
	 * @param unknown_type $whereType
	 * @param unknown_type $value
	 * @param unknown_type $logic
	 * @param unknown_type $group
	 * @return WindMySqlBuilder
	 */
	private function assembleWhere($where,$whereType=self::WHERE,$value=array(),$logic = true,$group = false){
		$_where = '';
		$where = $this->trueWhere($where,$value,$logic);
		if($group && in_array($group,self::$group)){
			$_where = self::$group[$group];
		}
		if($this->sql[self::WHERE]){
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
	 * @param unknown_type $fields
	 * @param unknown_type $table
	 * @param unknown_type $table_alias
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
	 * @param unknown_type $type
	 * @param unknown_type $table
	 * @param unknown_type $joinWhere
	 * @param unknown_type $table_alias
	 * @param unknown_type $fields
	 * @param unknown_type $schema
	 * @return WindMySqlBuilder
	 */
	private  function assembleJoin($type,$table,$joinWhere,$table_alias='',$fields='',$schema =''){
		if(!in_array($type,array_keys(self::$joinType))){
			throw new WindSqlException(WindSqlException::DB_JOIN_TYPE_ERROR);
		}
		$fields && $this->assembleFieldByTable($fields,$table,$table_alias);
		return $this->assembleSql(array($table=>array($type,$joinWhere,$table_alias,$schema)),self::JOIN);
	}
	
	private function parsePlaceHolder($text,$replace=array(),$separators=','){
		if($text && $replace && is_array($text)){
			foreach($text as $key=>$_where){
				$text[$key] = str_replace('?',$this->escapeString($replace[$key]),$_where);
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
		}
		return $text;
	}
	
	/**
	 * @param unknown_type $where
	 * @param unknown_type $value
	 * @param unknown_type $logic
	 * @return Ambigous <string, mixed>
	 */
	private function trueWhere($where,$value = array(),$logic = true){
		return $this->parsePlaceHolder($where,$value, $this->sqlFillSpace ($logic ? self::SQL_AND : self::SQL_OR));
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildTable()
	 */
	protected function buildFrom() {
		if (empty ( $this->sql[self::FROM] ) &&   !is_array ( $table )) {
			throw new WindSqlException (WindSqlException::DB_TABLE_EMPTY);
		}
		$tableList = '';
		foreach ( $this->sql[self::FROM] as $key => $value ) {
			$tableList .= $tableList ? ',' . $this->getAlias ( $key,$value[0],$value[1] ) : $this->getAlias ( $key,$value[0],$value[1] );
		}
		return $this->sqlFillSpace ( $tableList );
	}
	
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildDistinct()
	 */
	protected function buildDistinct() {
		return $this->sqlFillSpace ($this->sql[self::DISTINCT]);
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildField()
	 */
	protected function buildField() {
		if (empty ( $this->sql[self::FIELD] ) &&   !is_array ( $this->sql[self::FIELD] )) {
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
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildJoin()
	 */
	protected function buildJoin() {
		if (empty ( $this->sql[self::JOIN] )) {
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
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildWhere()
	 */
	protected function buildWhere() {
		if ($this->sql[self::WHERE]) {
			return $this->sqlFillSpace (self::SQL_WHERE.implode(' ',$this->sql[self::WHERE])) ;
		}
		return '';
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildGroup()
	 */
	protected function buildGroup() {
		return $this->sqlFillSpace ( $this->sql[self::GROUP] ? self::SQL_GROUP . (is_array ( $this->sql[self::GROUP] ) ? implode ( ',', $this->sql[self::GROUP] ) : $this->sql[self::GROUP]) . '' : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildOrder()
	 */
	protected function buildOrder() {
		$orderby = '';
		if(empty($this->sql[self::ORDER])){
			return '';
		}
		if (is_array ( $this->sql[self::ORDER] )) {
			foreach ( $this->sql[self::ORDER] as $key => $value ) {
				$orderby .= ($orderby ? ',' : '') . (is_string ( $key ) ? $key . ' ' .( $value ? self::SQL_DESC:self::SQL_ASC) : $value);
			}
		} else {
			$orderby = $this->sql[self::ORDER];
		}
		return $this->sqlFillSpace ( $orderby ? self::SQL_ORDER . $orderby : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildHaving()
	 */
	protected function buildHaving() {
		if($this->sql[self::HAVING]){
			 return $this->sqlFillSpace (self::SQL_HAVING.implode(' ',$this->sql[self::HAVING])) ;
		}
		return '';
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildLimit()
	 */
	protected function buildLimit() {
		if(empty($this->sql[self::LIMIT])){
			return ;
		}
		if(is_string($this->sql[self::LIMIT])){
			return $this->sqlFillSpace($this->sql[self::LIMIT]);
		}
		return $this->sqlFillSpace ( ($sql = $this->sql[self::LIMIT] > 0 ? self::SQL_LIMIT . $this->sql[self::LIMIT] : '') ? $this->sql[self::OFFSET] > 0 ? $sql . self::SQL_OFFSET . $this->sql[self::OFFSET] : $sql : '' );
	}
	
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildData()
	 */
	protected function buildData() {
		if (empty ( $this->sql[self::DATA] ) || ! is_array ( $this->sql[self::DATA] )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_INSERT_DATA);
		}
		if($this->getDimension ( $this->sql[self::DATA] ) == 1){
			return $this->buildSingleData ( $this->sql[self::DATA] );
		}
		if($this->getDimension ( $this->sql[self::DATA] ) >= 2){
			$key = array_keys($this->sql[self::DATA]);
			if(is_string($key[0])){
				$rows = count($this->sql[self::DATA][$key[0]]);
				$tmp_data = array();
				for($i=0;$i<$rows;$i++){
					foreach($this->sql[self::DATA] as $key=>$value){
						$tmp_data[$i][] = $value[$i];
						unset($this->sql[self::DATA][$key][$i]);
					}
				}
			}
			$data = $tmp_data ? $tmp_data :  $this->sql[self::DATA];
			return $this->buildMultiData ( $data );
		}
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildSet()
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
	

	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildAffected()
	 */
	public function getAffected($ifquery){
		$rows = $ifquery ? 'FOUND_ROWS()' : 'ROW_COUNT()';
		return $this->sqlFillSpace("$rows AS afftectedRows");
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildLastInsertId()
	 */
	public function getLastInsertId(){
		return $this->sqlFillSpace('LAST_INSERT_ID() AS insertId');
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#getMetaTableSql()
	 */
	public function getMetaTableSql($schema){
		if(empty($schema)){
			throw new WindSqlException (WindSqlException::DB_EMPTY);
		}
		return $this->sqlFillSpace('SHOW TABLES FROM '.$schema);
	}
	
	/* (non-PHPdoc)
	 * @see wind/component/db/base/WindSqlBuilder#getMetaColumnSql()
	 */
	public function getMetaColumnSql($table){
		if(empty($table)){
			throw new WindSqlException (WindSqlException::DB_TABLE_EMPTY);
		}
		return $this->sqlFillSpace('SHOW COLUMNS FROM '.$table);
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#escapeString()
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
	 * 取得别名标识
	 * @param string $name 源名称
	 * @param string $alias 别名
	 * @param string $schema 数据库名称
	 * @return string
	 */
	private function getAlias($name,$alias = '',$schema='') {
		$name = $alias ? $name.' ' .  self::SQL_AS . $alias : $name;
		return $this->sqlFillSpace ($schema ? $schema.'.'.$name : $name);
	}
	
	/**
	 * 解析查询表达式
	 * @param string $field  列名
	 * @param stirng $value 列值
	 * @param string $compare 表达式
	 * @param mixed  $ifconvert 否要对$value进行转换
	 * @return string
	 */
	private function buildCompare($field, $value, $compare,$ifconvert = true) {
		if (empty ( $field ) || !isset ( $value )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_COMPARESS_ERROR);
		}
		if (! in_array ( $compare, array_keys ( $this->compare ) )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_COMPARESS_EXIST);
		}
		if (in_array ( $compare, array ('in', 'notin' ) )) {
			$value = explode ( ',', $value );
			array_walk ( $value, array ($this, 'escapeString' ) );
			$value = implode ( ',', $value );
			$parsedCompare = $field . $this->sqlFillSpace ( $this->compare [$compare] ) . '(' . $value . ')';
		} else {
			$parsedCompare = $field . $this->sqlFillSpace ( $this->compare [$compare] ) . ($ifconvert ? $value : $this->escapeString ( $value ));
		}
		return $parsedCompare;
	}

		/**
	 * 检查是否是合法的查询条件
	 * @param array $where
	 * @return array  
	 */
	private function staticWhere($where,&$statics = array('logic'=>0,'group'=>0,'condition'=>0)) {
		foreach ( $where as $key => $value ) {
			if (is_int ( $key ) && is_string($value)) {
				if (in_array ( $value, array_keys ( $this->logic ) )) {
					$statics['logic']++;
				}
				if (in_array ( $value, array_keys ( $this->group ) )) {
					$statics['group'] ++;
				}
			}
			if (is_string ( $key ) && is_array($value)) {
				if (in_array ( $key, array_keys ( $this->compare ) )) {
					$statics['condition']++;
				}
			}
			if(is_int($key) && is_array($value)){
				$this->staticWhere($value,&$statics);
			}
		}
		return $statics;
	}
	
	private function checkWhere($where){
		if (! is_array ( $where )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_CONDTTION_FORMAT);
		}
		extract($this->staticWhere($where));
		if ($group % 2 === 1) {
			throw new WindSqlException (WindSqlException::DB_QUERY_GROUP_MATCH);
		}
		if ($logic && $condition && $condition - $logic != 1) {
			throw new WindSqlException (WindSqlException::DB_QUERY_LOGIC_MATCH);
		}
		if ($group && $condition === 0) {
			throw new WindSqlException (WindSqlException::DB_QUERY_GROUP_MATCH);
		}
		return array($logic,$group,$condition);
	}
	
	private function formatWhere($where,&$_where=array()){
		static $ifcheck = false;
		if(false === $ifcheck){
			$this->checkWhere ( $where );
			$ifcheck = true;
		}
		foreach ( $where as $key => $value ) {
			if (is_int ( $key ) && is_string($value)) {
					if (in_array ( $value, array_keys ( $this->logic ) )) {
						$_where[] = $this->logic [$value];
					}
					if (in_array ( $value, array_keys ( $this->group ) )) {
						$_where[] = $this->group [$value];
					}
			}
			if (is_string ( $key ) && is_array($value)) {
					$_where[] = $this->buildCompare ( $value [0], $value [1], $key,$value[2] );
			}			
			if(is_int ( $key ) && is_array($value)){
				  $this->formatWhere($value,$_where);
			}
		}
		return $_where;
	}
}

