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
abstract class WDbAdapter {
	
	/**
	 * @var resource 当前dbl连接句柄
	 */
	protected $linking = null;
	
	/**
	 * @var resource 当前查询句柄
	 */
	protected $query = '';
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
	 * @var WSqlBuilder sql语句生成器
	 */
	protected $sqlBuilder = null;
	/**
	 * @var int 是否连接
	 */
	protected $isConntected = 0;
	
	/**
	 * @var string | int 当前数据库连接所指向数据库配置的key
	 */
	protected $key = '';
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
	 * @var int 是否切换过数据库
	 */
	protected $switch = 0;
	
	/**
	 * @var strint 数据库schema
	 */
	protected $dbtype = '';
	/**
	 * @var array 框架支持的数据库种类
	 */
	protected $dbMap = array ('mysql' => 'MySql', 'mssql' => 'MsSql', 'pgsql' => 'PgSql', 'ocsql' => 'OcSql' );
	protected $transCounter = 0;
	public $enableSavePoint = 0;
	protected $savepoint = array ();
	
	/**
	 * @var array 记录向数据库写入次数
	 */
	public static $writeTimes = array();
	/**
	 * @var array 记录从数据库读入次数
	 */
	protected static $readTimes = array();
	/**
	 * @var array 数据库连接池
	 */
	protected static $linked = array ();
	/**
	 * @var array 数据库连接句柄
	 */
	protected static $config = array ();
	public function __construct($config) {
		$this->parseConfig ( $config );
		$this->patchConnect ();
		$this->getSqlBuilderFactory();
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
	private function parseConfig($config) {
		$db_config = array ();
		if (empty ( $config ) || ! is_array ( $config )) {
			throw new WSqlException ( "Database Config is not correct", 1 );
		}
		foreach ( $config as $key => $value ) {
			if (is_array ( $value ))
				$db_config [$key] = $value;
			if (is_string ( $value ))
				$db_config [$key] = $this->parseDsn ( $value );
		}
		return self::$config = $db_config;
	}
	/**
	 * 以DSN格式解析数数库配置，其中(主从optype,永久连接pconnect,强制新连接force)可选
	 * @example mysql:://username:password@localhost:port/dbname/optype/pconect/force
	 * @param unknown_type $dsn 数据库连接格式
	 * @return array 
	 */
	private function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(.+)\/?(master|slave)?\/?(0|1)?\/?(0|1)?\/?$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WSqlException ( "Database config is not correct format", 1 );
		}
		return array ('dbtype' => $config [1], 'dbuser' => $config [2], 'dbpass' => $config [3], 'dbhost' => $config [4], 'dbport' => $config [5], 'dbname' => $config [6], 'optype' => $config [7],'pconnect'=>$config [8],'force'=>$config [9] );
	}
	
	/**
	 * 连接数据库,构造连接池
	 */
	protected function patchConnect() {
		foreach ( self::$config as $key => $value ) {
			$this->connect ( $value, $key );
		}
	}
	
	/**
	 * 连接数据库
	 * @param array $config 数据库配置
	 * @param string $key 数据库连接标识
	 */
	public abstract function connect($config, $key);
	/**
	 * 执行数据库查询
	 * @param string $sql sql语句
	 * @param string | int $key 数据库连接标识
	 * @return boolean;
	 */
	public abstract function query($sql,$key='');
	/**
	 * 执行数据库写入
	 * @param string $sql sql语句
	 * @param string | int $key 数据库连接标识
	 * @return boolean;
	 */
	public abstract function execute($sql,$key='');
	public abstract function getAll();
	public abstract function getMetaTables();
	public abstract function getMetaColumns();
	public abstract function savePoint();
	public abstract function beginTrans();
	public abstract function rollbackTrans();
	public abstract function getAffectedRows();
	public abstract function getInsertId();
	public abstract function close();
	public abstract function dispose();
	protected abstract function error();
	public  function getExecSqlTime(){
		
	}
	
	/**
	 * 切换数据库连接
	 * @param string $key 数据库连接标识
	 */
	public function changeConn($key) {
		if (! isset ( self::$linked [$key] )) {
			throw new WSqlException ( "this Database Connecton is not exists", 1 );
		}
		$this->linking = self::$linked [$key];
		$this->key = $key;
		$this->switch = 1;
	}
	/**
	 *释放切换连接 
	 */
	public function freeChange(){
		$this->switch = 0;
	}

	
	
	/**
	 * sqlbuilder Factory
	 * @return WSqlBuilder 返回sql语句生成器
	 */
	public function getSqlBuilderFactory() {
		$config = self::$config [$this->key];
		if (empty ( $config ) || ! is_array ( $config )) {
			throw new WSqlException ( "Database Config is not correct format", 1 );
		}
		$dbType = $this->dbMap[strtolower($config ['dbtype'])];
		$builderClass = 'W'.$dbType.'Builder';
		return $this->sqlBuilder = W::getInstance($builderClass);
	}

	/**
	 * 执行添加数据操作 (insert)
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	public  function insert($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getInsertSql($optiion);
		$this->exceute($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}
	
	/**
	 * 执行更新数据操作
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	public  function update($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getUpdateSql($option);
		$this->exceute($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}
	/**
	 * 执行查询数据操作
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	public function select($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getSelectSql($option);
		$this->query($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}
	/**
	 * 执行删除数据操作
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	public  function delete($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getDeleteSql($option);
		$this->exceute($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}
	
	/**
	 * 执行新增数据操作(replace)
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	public function replace($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getReplaceSql($option);
		$this->exceute($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}

	/**
	 * 返回上一条sqly语句
	 * @return string
	 */
	public function getLastSql() {
		return $this->last_sql;
	}
	/**
	 * @param strin | int $key 数据库连接标识
	 * @return number 写数据库次数
	 */
	public function getWriteTimes($key = '') {
		if($key = $this->checkKey($key)){
			return self::$writeTimes[$key];
		}
		$writes = 0;
		foreach(self::$writeTimes as $value){
			$writes += $value;
		}
		return $writes;
	}
	
	/**
	 * @param strin | int $key 数据库连接标识
	 * @return number 读数据库次数
	 */
	public function getReadTimes($key='') {
		if($key = $this->checkKey($key)){
			return self::$readTimes[$key];
		}
		$reads = 0;
		foreach(self::$readTimes as $value){
			$reads += $value;
		}
		return $reads;
	}
	
	/**
	 * @param string | int $key 数据库连接标识
	 * @return number 读写数据库次数
	 */
	public function getQueryTimes($key = '') {
		return $this->getReadTimes($key) + $this->getWriteTimes($key);
	}
	
	/**
	 * 返回数据库连接
	 * @param strin | int $key 数据库标识
	 * @return resource 返回数据库连接
	 */
	protected function getLinked($key = '') {
		return $key ? self::$linked [$key] : $this->linking;
	}
	
	/**
	 * 查看是是否要主从数据库设置，并按主从配置返回数据库配置信息
	 * @return array
	 */
	protected function getMasterSlave() {
		$array = array ();
		foreach ( self::$config as $key => $value ) {
			if (in_array ( $value ['optype'], array ('master', 'slave' ) )) {
				$array [$value ['optype']] [$key] = $value;
			}
		}
		return $array;
	}
	
	/**
	 * 取得当前数据库连接
	 * @param string $optype 数据库主从配置(master/slave);
	 * @param string | int $key 数据库连接标识
	 */
	protected function getLinking($optype = '', $key = '') {
		$this->checkKey($key);
		$masterSlave = $this->getMasterSlave ();
		$config = empty ( $masterSlave ) || empty ( $optype ) ? self::$config : $masterSlave [$optype];
		$key = $key ? $key : $this->getConfigKeyByPostion ( $config, mt_rand ( 0, count ( $config ) - 1 ) );
		$this->linking = self::$linked [$key];
		$this->key = $key;
	}
	
	/**
	 *根据config的pos返回key
	 * @param array $config 数据库配置
	 * @param int $pos config的位置
	 * @return string 返回config的key
	 */
	private function getConfigKeyByPostion($config, $pos = 0) {
		$i = 0;
		foreach ( ( array ) $config as $key => $value ) {
			if ($pos === $i)
				return $key;
			$i ++;
		}
		return '';
	}
	
	protected function logSql(){
		W::recordLog($this->last_sql,'DB','log');
	}
	
	/**
	 * 检查$linked中连接的合法性
	 * @param string $key config的key
	 * @return string
	 */
	protected function checkKey($key = ''){
		if($key && !in_array($key,array_keys(self::$linked))){
			throw new WSqlException('Database identify is not exists',1);
		}
		return $key;
	}
	
	

}