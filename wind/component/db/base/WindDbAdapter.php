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
abstract class WindDbAdapter {
	
	/**
	 * @var string 前次执行的sqly语句
	 */
	protected $last_sql = '';
	/**
	 * @var string 前句执行sql时的错误字符串
	 */
	protected $last_errstr = '';
	/**
	 * @var int 前句执行sql时的错误代码
	 */
	protected $last_errcode = 0;

	/**
	 * @var string 数据库字符集
	 */
	protected $charset = 'gbk';
	/**
	 * @var boolean 是否强制连接
	 */
	protected $force = false;
	/**
	 * @var boolean 是否永久连接
	 */
	protected $pconnect = false;

	/**
	 * @var array 框架支持的数据库种类
	 */
	protected $dbMap = array ('mysql' => 'MySql', 'mssql' => 'MsSql', 'pgsql' => 'PgSql', 'ocisql' => 'OciSql' );
	/**
	 * @var int 事务记数器
	 */
	protected $transCounter = 0;
	/**
	 * @var int 是否启用事务
	 */
	protected $enableSavePoint = 0;
	/**
	 * @var array 事务回滚点
	 */
	protected $savepoint = array ();
	/**
	 * @var resoruce 数据库连接
	 */
	protected  $connection = null;
	/**
	 * @var WindSqlBuilder sql语句生成器
	 */
	protected  $sqlBuilder = null;
	/**
	 * @var resource 当前查询句柄
	 */
	protected $query = null;
	/**
	 * @var array 数据库连接配置
	 */
	protected  $config = array ();
	
	/**
	 * 初始化配置
	 * @param array $config
	 */
	public function __construct($config) {
		$this->parseConfig ( $config );
		$this->connect();
	}
	
	/**
	 * 解析数数库配置
	 * @param array $config 数据库配置，必须是基于键值的二维数组或必须是基于键值DNS格式的一维数组
	 * @return array 返回解析后的数据库配置
	 */
	final protected function parseConfig($config) {
		$config = is_array($config) ? $config : $this->parseDSN($config);
		return $this->checkConfig($config);
	}
	/**
	 * 以DSN格式解析数数库配置，其中(主从optype,永久连接pconnect,强制新连接force)可选
	 * @param string $dsn 数据库连接格式
	 * @return array 
	 */
	final public function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(\w+)\/(\w+)\/(0|1)\/(0|1)\/(master|slave)?\/?$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WindSqlException (WindSqlException::DB_CONFIG_FORMAT);
		}
		return array (
						IWindDbConfig::CONFIG_TYPE => $config [1], 
						IWindDbConfig::CONFIG_USER => $config [2], 
						IWindDbConfig::CONFIG_PASS => $config [3], 
						IWindDbConfig::CONFIG_HOST => $config [4], 
						IWindDbConfig::CONFIG_PORT => $config [5], 
						IWindDbConfig::CONFIG_NAME => $config [6],
						IWindDbConfig::CONFIG_CHAR=>$config [7], 
						IWindDbConfig::CONFIG_FORCE => $config [8],
						IWindDbConfig::CONFIG_PCONN=>$config [9],
						IWindDbConfig::CONFIG_RWDB=>$config [10] 
					);
	}
	
	/**
	 * 验证config操作
	 * @param array $config
	 * @return array
	 */
	final private function checkConfig($config){
		if (empty ( $config ) || (! is_array ( $config ) && !is_string($config))) {
			throw new WindSqlException (WindSqlException::DB_CONFIG_EMPTY);
		}
		if(empty($config[IWindDbConfig::CONFIG_TYPE]) || empty($config[IWindDbConfig::CONFIG_HOST]) || empty($config[IWindDbConfig::CONFIG_NAME]) || empty($config[IWindDbConfig::CONFIG_USER])  || empty($config[IWindDbConfig::CONFIG_PASS])){
			throw new WindSqlException (WindSqlException::DB_CONFIG_FORMAT);
		}
		$config [IWindDbConfig::CONFIG_HOST] = $config [IWindDbConfig::CONFIG_PORT] ? $config [IWindDbConfig::CONFIG_HOST] . ':' . $config [IWindDbConfig::CONFIG_PORT] : $config [IWindDbConfig::CONFIG_HOST];
		$config [IWindDbConfig::CONFIG_PCONN] = $config [IWindDbConfig::CONFIG_PCONN] ? $config [IWindDbConfig::CONFIG_PCONN] : $this->pconnect;
		$config [IWindDbConfig::CONFIG_FORCE] = $config [IWindDbConfig::CONFIG_FORCE] ? $config [IWindDbConfig::CONFIG_FORCE] : $this->force;
		$config [IWindDbConfig::CONFIG_CHAR] = $config [IWindDbConfig::CONFIG_CHAR] ? $config [IWindDbConfig::CONFIG_CHAR] : $this->charset;
		return $this->config = $config;
	}
	
	/**
	 * 连接数据库
	 */
	protected abstract function connect();
	/**
	 * 执行相关sql语句操作
	 * @param string $sql sql语句
	 * @return boolean;
	 */
	public abstract function query($sql);
	/**
	 * 取得结果集
	 * @param int $fetch_type 类型
	 * @return array
	 */
	public abstract function getAllRow($fetch_type);
	
	public abstract function getRow($fetch_type);
	/**
	 * 开始事务点
	 */
	public abstract function beginTrans();
	/**
	 * 提交事务
	 */
	public abstract function commitTrans();
	/**
	 * 关闭数据库
	 */
	public abstract function close();
	/**
	 * 取得影响行数
	 */
	public abstract function getAffectedRows();
	/**
	 * 取得最后新增自增ID
	 */
	public abstract function getLastInsertId();
	
	/**
	 * @param unknown_type $schema
	 */
	public abstract function getMetaTables($schema = '');
	
	/**
	 *取得数据表元数据列 
	 */
	public abstract function getMetaColumns($table);
	/**
	 * 释放数据库连接资源
	 */
	public abstract function dispose();
	/**
	 * 数据库操作操作处理
	 */
	protected abstract function error($sql);
	
	/**
	 * 返回sqlBuilder生成器
	 * @return WindSqlBuilder
	 */
	final public  function getSqlBuilder(){
		if(empty($this->sqlBuilder)){
			$_currentAdapter = get_class($this);
			$_builder = $_currentAdapter.'Builder';
			$_driver = str_replace('Wind','',$_currentAdapter);
			L::import ( "WIND:component.db.drivers.{$_driver}.{$_builder}" );
			$this->sqlBuilder = L::getInstance($_builder,array($this));
		}
		return $this->sqlBuilder;
	}
	/**
	 * 执行添加数据操作 (insert)
	 * @param string | array $sql 查询条件
	 * @return boolean
	 */
	final public  function insert($sql){
		return $this->query($sql);
	}
	
	/**
	 * 执行更新数据操作
	 * @param string  $sql 查询条件

	 * @return boolean
	 */
	final public  function update($sql){
		return $this->query($sql);
	}
	/**
	 * 执行查询数据操作
	 * @param string  $sql 查询条件

	 * @return boolean
	 */
	final public function select($sql){
		return $this->query($sql);
	}
	/**
	 * 执行删除数据操作
	 * @param string  $sql 查询条件
	 * @return boolean
	 */
	final public  function delete($sql){
		return $this->query($sql);
	}
	
	/**
	 * 执行新增数据操作(replace)
	 * @param string  $sql 查询条件
	 * @return boolean
	 */
	final public function replace(){
		return $this->query($sql);
	}
	
	public function escapeString($value) {
		return " '" . $value . "' ";
	}
	
	/**
	 * 取得当前DB连接
	 * @return resoruce
	 */
	public function getConnection(){
		return $this->connection;
	}
	
	public function getConfig(){
		return $this->config;
	}

	/**
	 * 返回上一条sqly语句
	 * @return string
	 */
	final public function getLastSql() {
		return $this->last_sql;
	}

	/**
	 * 取得数据库
	 * @return string:
	 */
	final public function getSchema(){
		return  $this->config[IWindDbConfig::CONFIG_NAME];
	}
	
	/**
	 * 取得数据库驱动
	 * @return string;
	 */
	final public function getDbDriver(){
		return  $this->config[IWindDbConfig::CONFIG_TYPE];
	}

	public function __destruct(){
		$this->dispose();
	}
}