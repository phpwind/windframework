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
class WindMySqlBuilder extends WindSqlBuilder { 
	
	public  function distinct($flag = true){
		$this->sql[self::DISTINCT] = $flag ? self::SQL_DISTINCT : '';
		return $this;
	}
	public  function filed($field){
		$params = func_num_args();
		$field = $params >1 ? func_get_args() : func_get_arg(0);
		return $this->assemblySql($field,self::FIELD);
	}
	public  function from($table,$table_alias='',$fields=''){
		$fields && $this->assemblyFieldByTable($fields,$table,$table_alias);
		return $this->assemblySql(array($table=>array($table_alias)),self::FROM);
	}
	
	public function leftJoin($table,$joinWhere,$alias='',$cols='',$schema =''){
		return $this->join(self::LEFT,$table,$joinWhere,$alias,$cols,$schema);
	}
	
	public function rightJoin($table,$joinWhere,$alias='',$cols='',$schema =''){
		return $this->join(self::RIGHT,$table,$joinWhere,$alias,$cols,$schema);
	}
	
	public function innerJoin($table,$joinWhere,$alias='',$cols='',$schema =''){
		return $this->join(self::INNER,$table,$joinWhere,$alias,$cols,$schema);
	}
	
	public function crossJoin($table,$joinWhere,$alias='',$cols='',$schema =''){
		return $this->join(self::CROSS,$table,$joinWhere,$alias,$cols,$schema);
	}
	
	public function fullJoin($table,$joinWhere,$alias='',$cols=self::SQL_ALLFIELD,$schema =''){
		return $this->join(self::FULL,$table,$joinWhere,$alias,$cols,$schema);
	}
	
	public  function where(){
		return $this;
	}
	public  function order($order){
		$params = func_num_args();
		$order = $params >1 ? func_get_args() : func_get_arg(0);
		$this->assemblySql($order,self::ORDER);
		return $this;
	}
	public  function group($group){
		$params = func_num_args();
		$group = $params >1 ? func_get_args() : func_get_arg(0);
		$this->assemblySql($group,self::GROUP);
		return $this;
	}
	public  function having(){
		$this->assemblySql($limit,self::HAVING);
		return $this;
	}
	public  function limit($limit,$offset = ''){
		$this->assemblySql((int)$limit,self::LIMIT);
		return $this->assemblySql((int)$offset,self::OFFSET);
	}
	
	private function assemblySql($assembleValue,$assembleType){
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
	
	private function assemblyFieldByTable($fields,$table,$table_alias=''){
		if($fields && (is_string($fields) || is_array($fields))){
			$fields = is_array($fields) ? $fields : explode(',',$fields);
			foreach($fields as $key=>$field){
				$fields[$key] = (false === ($pos = strpos('.',$field))) ? $table_alias ? $table_alias.'.'.$field : $table.'.'.$field :$field;
			}
			$this->assemblySql($cols,self::FIELD);
		}
		return $this;
	}
	
	public abstract function join($type,$table,$joinWhere,$table_alias='',$fields='',$schema =''){
		if(!in_array($type,array_keys(self::$joinType))){
			throw new WindSqlException(WindSqlException::DB_JOIN_TYPE_ERROR);
		}
		$fields && $this->assemblyFieldByTable($fields,$table,$table_alias);
		return $this->assemblySql(array($table=>array($type,$joinWhere,$table_alias)),self::JOIN);
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildTable()
	 */
	public function buildFrom($table = array()) {
		if (empty ( $table ) || (! is_string ( $table ) && ! is_array ( $table ))) {
			throw new WindSqlException (WindSqlException::DB_TABLE_EMPTY);
		}
		$table = is_string ( $table ) ? explode ( ',', $table ) : $table;
		$tableList = '';
		foreach ( $table as $key => $value ) {
			if (is_int ( $key )) {
				$tableList .= $tableList ? ',' . $value : $value;
			}
			if (is_string ( $key )) {
				$tableList .= $tableList ? ',' . $this->getAlias ( $key, $as, $value ) : $this->getAlias ( $key, $as, $value );
			}
		}
		return $this->sqlFillSpace ( $tableList );
	}
	
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildDistinct()
	 */
	public function buildDistinct($distinct = false) {
		return $this->sqlFillSpace ( $distinct ? 'DISTINCT' : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildField()
	 */
	public function buildField($field = array()) {
		$fieldList = '';
		if (empty ( $field )) {
			$fieldList = '*';
		}
		if (! is_string ( $field ) && ! is_array ( $field )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_FIELD_FORMAT);
		}
		$field = is_string ( $field ) ? explode ( ',', $field ) : $field;
		foreach ( $field as $key => $value ) {
			if (is_int ( $key )) {
				$fieldList .= $fieldList ? ',' . $value : $value;
			}
			if (is_string ( $key )) {
				$fieldList .= $fieldList ? ',' . $this->getAlias ( $key, $as, $value ) : $this->getAlias ( $key, $as, $value );
			}
		}
		return $this->sqlFillSpace ( $fieldList );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildJoin()
	 */
	public function buildJoin($join = array()) {
		if (empty ( $join )) {
			return '';
		}
		if (is_string ( $join )) {
			return $this->sqlFillSpace($join);
		}
		$joinContidion = '';
		foreach ( $join as $table => $config ) {
			if (is_string ( $config ) && is_int($table)) {
				$joinContidion .= $joinContidion ? ' ' . $config : $config;
				continue;
			}
			if (is_array ( $config ) && is_string($table)) {
				$table = $this->getAlias ( $table, $as, $config [2] );
				$joinWhere = $config [1] ? self::SQL_ON . $config [1] : '';
				$condition = self::$joinTypes[$config [0]] . self::SQL_JOIN . $table . $joinWhere;
				$joinContidion .= $joinContidion ? ' ' . $condition : $condition;
			}
		}
		return $this->sqlFillSpace ( $joinContidion );
	}
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildWhere()
	 */
	public function buildWhere($where = array()) {
		if (empty ( $where )) {
			return '';
		}
		if (is_string ( $where )) {
			return self::SQL_WHERE . $this->sqlFillSpace($where);
		}
		$_where = $this->formatWhere($where);
		return $this->sqlFillSpace (self::SQL_WHERE.implode(' ',$_where)) ;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildGroup()
	 */
	public function buildGroup($group = array()) {
		return $this->sqlFillSpace ( $group ? self::SQL_GROUP . (is_array ( $group ) ? implode ( ',', $group ) : $group) . '' : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildOrder()
	 */
	public function buildOrder($order = array()) {
		$orderby = '';
		if (is_array ( $order )) {
			foreach ( $order as $key => $value ) {
				$orderby .= ($orderby ? ',' : '') . (! is_int ( $key ) ? $key . ' ' . strtoupper ( $value ) : $value);
			}
		} else {
			$orderby = $order;
		}
		return $this->sqlFillSpace ( $orderby ? self::SQL_ORDER . $orderby : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildHaving()
	 */
	public function buildHaving($having = '') {
		return $this->sqlFillSpace ( $having ? self::SQL_HAVING . $having : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildLimit()
	 */
	public function buildLimit($limit = 0, $offset = 0) {
		if(is_string($limit)){
			return $this->sqlFillSpace($limit);
		}
		return $this->sqlFillSpace ( ($sql = $limit > 0 ? self::SQL_LIMIT . $limit : '') ? $offset > 0 ? $sql . self::SQL_OFFSET . $offset : $sql : '' );
	}
	
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildData()
	 */
	public function buildData($data) {
		if (empty ( $data ) || ! is_array ( $data )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_INSERT_DATA);
		}
		if($this->getDimension ( $data ) == 1){
			return $this->buildSingleData ( $data );
		}
		if($this->getDimension ( $data ) >= 2){
			$key = array_keys($data);
			if(is_string($key[0])){
				$rows = count($data[$key[0]]);
				$tmp_data = array();
				for($i=0;$i<$rows;$i++){
					foreach($data as $key=>$value){
						$tmp_data[$i][] = $value[$i];
						unset($data[$key][$i]);
					}
				}
			}
			$data = $tmp_data ? $tmp_data : $data;
			return $this->buildMultiData ( $data );
		}
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildSet()
	 */
	public function buildSet($set) {
		if (empty ( $set )) {
			throw new WindSqlException (WindSqlException::DB_QUERY_UPDATE_DATA);
		}
		if (is_string ( $set )) {
			return $set;
		}
		foreach ( $set as $key => $value ) {
			$data [] = $key . '=' . $this->escapeString ( $value );
		}
		return $this->sqlFillSpace ( implode ( ',', $data ) );
	}
	

	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildAffected()
	 */
	public function buildAffected($ifquery){
		$rows = $ifquery ? 'FOUND_ROWS()' : 'ROW_COUNT()';
		return $this->sqlFillSpace("$rows AS afftectedRows");
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildLastInsertId()
	 */
	public function buildLastInsertId(){
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
	public function escapeString($value) {
		return " '" . $value . "' ";
	}
	
	/**
	 * 取得别名标识
	 * @param string $name 源名称
	 * @param string $as   别名标识
	 * @param string $alias 别名
	 * @return string
	 */
	private function getAlias($name, $as = ' ', $alias = '') {
		return $this->sqlFillSpace ( ($alias ? $name . $this->sqlFillSpace ( strtoupper ( $as ) ) . $alias : $name) );
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

