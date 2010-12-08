<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.exception.WindSqlException');
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
	 * @var resource 当前查询句柄
	 */
	protected $query = null;
	/**
	 * @var array 数据库连接配置
	 */
	protected  $config = array ();
	
	const CONFIG_DRIVER_TYPE = 'dbtype';
	const CONFIG_DRIVER_USER = 'dbuser';
	const CONFIG_DRIVER_PASS = 'dbpass';
	const CONFIG_DRIVER_HOST = 'dbhost';
	const CONFIG_DRIVER_PORT = 'dbport';
	const CONFIG_DRIVER_NAME = 'dbname';
	const CONFIG_DRIVER_RWDB = 'optype';
	const CONFIG_DRIVER_CHAR = 'charset';
	const CONFIG_DRIVER_FORCE = 'force';
	const CONFIG_DRIVER_PCONN = 'pconnect';

	
	
	public function __construct($config) {
		$this->parseConfig ( $config );
		$this->connect();
	}
	
	/**
	 * 解析数数库配置
	 * @param array $config 数据库配置，必须是基于键值的二维数组或必须是基于键值DNS格式的一维数组
	 * @example DSN格式array('phpwind'=>'mysql:://username:password@localhost:port/dbname/optype/pconnect/force');
	 * 			arrays格式array('phpwind'=>array('dbtype'=>'mysql','dbname'=>'root','dbpass'=>'123456',
	 * 							'dbuser'=>'root','dbhost'=>'locahost','dbport'=>3306,
	 * 							'optype'=>'master','pconnect'=>1,'force'=>1);
	 * @return array 返回解析后的数据库配置
	 */
	final protected function parseConfig($config) {
		$config = is_array($config) ? $config : $this->parseDSN($config);
		return $this->checkConfig($config);
	}
	/**
	 * 以DSN格式解析数数库配置，其中(主从optype,永久连接pconnect,强制新连接force)可选
	 * @example mysql:://username:password@localhost:port/dbname/force/pconect/optype/
	 * @param unknown_type $dsn 数据库连接格式
	 * @return array 
	 */
	final public function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(\w+)\/(\w+)\/(0|1)\/(0|1)\/(master|slave)?\/?$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WindSqlException (WindSqlException::DB_CONFIG_FORMAT);
		}
		return array (
						self::CONFIG_DRIVER_TYPE => $config [1], 
						self::CONFIG_DRIVER_USER => $config [2], 
						self::CONFIG_DRIVER_PASS => $config [3], 
						self::CONFIG_DRIVER_HOST => $config [4], 
						self::CONFIG_DRIVER_PORT => $config [5], 
						self::CONFIG_DRIVER_NAME => $config [6],
						self::CONFIG_DRIVER_CHAR=>$config [7], 
						self::CONFIG_DRIVER_FORCE => $config [8],
						self::CONFIG_DRIVER_PCONN=>$config [9],
						self::CONFIG_DRIVER_RWDB=>$config [10] 
					);
	}
	
	final private function checkConfig($config){
		if (empty ( $config ) || (! is_array ( $config ) && !is_string($config))) {
			throw new WindSqlException (WindSqlException::DB_CONFIG_EMPTY);
		}
		if(empty($config[self::CONFIG_DRIVER_TYPE]) || empty($config[self::CONFIG_DRIVER_HOST]) || empty($config[self::CONFIG_DRIVER_NAME]) || empty($config[self::CONFIG_DRIVER_USER])  || empty($config[self::CONFIG_DRIVER_PASS])){
			throw new WindSqlException (WindSqlException::DB_CONFIG_FORMAT);
		}
		$config [self::CONFIG_DRIVER_HOST] = $config [self::CONFIG_DRIVER_PORT] ? $config [self::CONFIG_DRIVER_HOST] . ':' . $config [self::CONFIG_DRIVER_PORT] : $config [self::CONFIG_DRIVER_HOST];
		$config [self::CONFIG_DRIVER_PCONN] = $config [self::CONFIG_DRIVER_PCONN] ? $config [self::CONFIG_DRIVER_PCONN] : $this->pconnect;
		$config [self::CONFIG_DRIVER_FORCE] = $config [self::CONFIG_DRIVER_FORCE] ? $config [self::CONFIG_DRIVER_FORCE] : $this->force;
		$config [self::CONFIG_DRIVER_CHAR] = $config [self::CONFIG_DRIVER_CHAR] ? $config [self::CONFIG_DRIVER_CHAR] : $this->charset;
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
	 * 
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
	 * 执行添加数据操作 (insert)
	 * @param string | array $sql 查询条件
	 * @return boolean
	 */
	final public  function insert($sql){
		return $this->query($sql);
	}
	
	/**
	 * 执行更新数据操作
	 * @param string | array $sql 查询条件

	 * @return boolean
	 */
	final public  function update($sql){
		return $this->query($sql);
	}
	/**
	 * 执行查询数据操作
	 * @param string | array $sql 查询条件

	 * @return boolean
	 */
	final public function select($sql){
		return $this->query($sql);
	}
	/**
	 * 执行删除数据操作
	 * @param string | array $sql 查询条件
	 * @return boolean
	 */
	final public  function delete($sql){
		return $this->query($sql);
	}
	
	/**
	 * 执行新增数据操作(replace)
	 * @param string | array $sql 查询条件
	 * @return boolean
	 */
	final public function replace(){
		return $this->query($sql);
	}
	
	public function escapeString($value) {
		return " '" . $value . "' ";
	}
	
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

	final public function getSchema(){
		return  $this->config[self::CONFIG_DRIVER_NAME];
	}
	
	final public function getDbDriver(){
		return  $this->config[self::CONFIG_DRIVER_TYPE];
	}

	public function __destruct(){
		$this->dispose();
	}
}