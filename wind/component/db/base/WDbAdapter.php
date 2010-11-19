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
	 * @var array 框架支持的数据库种类
	 */
	protected $dbMap = array ('mysql' => 'MySql', 'mssql' => 'MsSql', 'pgsql' => 'PgSql', 'ocsql' => 'OcSql' );
	/**
	 * @var int 事务记数器
	 */
	protected $transCounter = 0;
	/**
	 * @var int 是否启用事务
	 */
	public $enableSavePoint = 0;
	/**
	 * @var array 事务回滚点
	 */
	protected $savepoint = array ();
	
	/**
	 * @var array 记录向数据库写入次数
	 */
	protected  $writeTimes = array();
	/**
	 * @var array 记录从数据库读入次数
	 */
	protected  $readTimes = array();
	/**
	 * @var array 数据库连接池
	 */
	protected  $linked = array ();
	/**
	 * @var array 数据库连接配置
	 */
	public static $config = array ();
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
	final private function parseConfig($config) {
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
		return $this->config = $db_config;
	}
	/**
	 * 以DSN格式解析数数库配置，其中(主从optype,永久连接pconnect,强制新连接force)可选
	 * @example mysql:://username:password@localhost:port/dbname/optype/pconect/force
	 * @param unknown_type $dsn 数据库连接格式
	 * @return array 
	 */
	final private function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(.+)\/?(master|slave)?\/?(0|1)?\/?(0|1)?\/?$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WSqlException ( "Database config is not correct format", 1 );
		}
		return array ('dbtype' => $config [1], 'dbuser' => $config [2], 'dbpass' => $config [3], 'dbhost' => $config [4], 'dbport' => $config [5], 'dbname' => $config [6], 'optype' => $config [7],'pconnect'=>$config [8],'force'=>$config [9] );
	}
	
	/**
	 * 连接数据库,构造连接池
	 */
	final protected function patchConnect() {
		foreach ( $this->config as $key => $value ) {
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
	 * 执行相关sql语句操作
	 * @param string $sql sql语句
	 * @param string|int|resource $key 数据库连接标识
	 * @param string optype 主从数据库(master/slave);
	 * @return boolean;
	 */
	public abstract function query($sql,$key='',$optype = '');
	/**
	 * @param int $fetch_type 取得结果集
	 */
	public abstract function getAllResult($fetch_type = MYSQL_ASSOC);
	/**
	 *取得数据库元数据表
	 */
	public abstract function getMetaTables();
	/**
	 *取得数据表元数据列 
	 */
	public abstract function getMetaColumns();
	/**
	 * 保存事务点
	 */
	//public abstract function savePoint();
	/**
	 * 开始事务点
	 */
	public abstract function beginTrans();
	/**
	 * 回滚事务
	 */
	//public abstract function rollbackTrans();
	/**
	 * 取得受影响的数据行数
	 * @param string|int 数据库连接标识
	 * @return int
	 */
	public abstract function getAffectedRows($key = '');
	/**
	 * 取得最后插入ID
	 * @param string|int 数据库连接标识
	 * @return int
	 */
	public abstract function getInsertId($key = '');
	/**
	 * 关闭数据库
	 */
	public abstract function close();
	/**
	 * 释放数据库连接资源
	 */
	public abstract function dispose();
	/**
	 * 数据库操作操作处理
	 */
	protected abstract function error();

	/**
	 * 执行数据库读取
	 * @param string $sql sql语句
	 * @param string|int|resource $key 数据库连接标识
	 * @return boolean;
	 */
	final public function read($sql, $key = '') {
		$this->query ( $sql, $key ,'slave');
		if (isset ( $this->readTimes [$this->key] )) {
			$this->readTimes [$this->key] ++;
		} else {
			$this->readTimes [$this->key] = 1;
		}
	}
	
	/**
	 * 执行数据库写入
	 * @param string $sql sql语句
	 * @param string|int|resource $key 数据库连接标识
	 * @return boolean;
	 */
	final public function write($sql, $key = '') {
		$this->query( $sql, $key ,'master');
		if (isset ( $this->writeTimes [$this->key] )) {
			$this->writeTimes [$this->key] ++;
		} else {
			$this->writeTimes [$this->key] = 1;
		}
	}
	
	/**
	 * 切换数据库连接
	 * @param string $key 数据库连接标识
	 */
	final public function changeConn($key) {
		$this->checkKey($key);
		$this->getSqlBuilderFactory($key);
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
	final public function getSqlBuilderFactory($key = '') {
		return  W::getInstance('W'.$this->dbMap[$this->getSchema($key)].'Builder');
	}

	/**
	 * 执行添加数据操作 (insert)
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	final public  function insert($option,$key = ''){
		$this->getExecDbLink('master',$key);
		return $this->write($this->getSqlBuilderFactory($this->key)->getInsertSql($option),$this->key);
	}
	
	/**
	 * 执行更新数据操作
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	final public  function update($option,$key = ''){
		$this->getExecDbLink('master',$key);
		return $this->write($this->getSqlBuilderFactory($this->key)->getUpdateSql($option),$this->key);
	}
	/**
	 * 执行查询数据操作
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	final public function select($option,$key = ''){
		$this->getExecDbLink('slave',$key);
		return $this->read($this->getSqlBuilderFactory($this->key)->getSelectSql($option),$this->key);
	}
	/**
	 * 执行删除数据操作
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	final public  function delete($option,$key = ''){
		$this->getExecDbLink('master',$key);
		return $this->write($this->getSqlBuilderFactory($this->key)->getDeleteSql($option),$this->key);
	}
	
	/**
	 * 执行新增数据操作(replace)
	 * @param string | array $option 查询条件
	 * @param string | int $key 数据库连接标识
	 * @return boolean
	 */
	final public function replace($option,$key = ''){
		$this->getExecDbLink('master',$key);
		return $this->write($this->getSqlBuilderFactory($this->key)->getReplaceSql($option),$this->key);
	}

	/**
	 * 返回上一条sqly语句
	 * @return string
	 */
	final public function getLastSql() {
		return $this->last_sql;
	}
	/**
	 * @param string | int $key 数据库连接标识
	 * @return number 写数据库次数
	 */
	final public function getWriteTimes($key = '') {
		if(($key = $this->checkKey($key))){
			return $this->writeTimes[$key];
		}
		$writes = 0;
		foreach($this->writeTimes as $value){
			$writes += $value;
		}
		return $writes;
	}
	
	/**
	 * @param string | int $key 数据库连接标识
	 * @return number 读数据库次数
	 */
	final public function getReadTimes($key='') {
		if(($key = $this->checkKey($key))){
			return $this->readTimes[$key];
		}
		$reads = 0;
		foreach($this->readTimes as $value){
			$reads += $value;
		}
		return $reads;
	}
	
	/**
	 * @param string | int $key 数据库连接标识
	 * @return number 读写数据库次数
	 */
	final public function getQueryTimes($key = '') {
		return $this->getReadTimes($key) + $this->getWriteTimes($key);
	}
	
	/**
	 * 返回数据库连接
	 * @param string | int $key 数据库标识
	 * @return resource 返回数据库连接
	 */
	protected function getLinked($key = '') {
		return $this->linked [$key];
	}
	
	/**
	 * 查看是是否要主从数据库设置，并按主从配置返回数据库配置信息
	 * @return array
	 */
	protected function getMasterSlave() {
		$array = array ();
		foreach ( $this->config as $key => $value ) {
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
	 * @return string|int 返回真正的执行数据库连接标识
	 */
	protected function getExecDbLink($optype = '',$key='') {
		if (empty ( $this->switch )) {
			$masterSlave = $this->getMasterSlave ();
			$config = (empty ( $masterSlave ) || empty ( $optype )) ? $this->config : $masterSlave [$optype];
		  	$this->key = $key ? $key : $this->getConfigKeyByPostion ( $config, mt_rand ( 0, count ( $config ) - 1 ) );
		  	$this->checkKey($this->key);
		}
	}
	
	
	
	
	/**
	 *根据config的pos返回key
	 * @param array $config 数据库配置
	 * @param int $pos config的位置
	 * @return string 返回config的key
	 */
	final private function getConfigKeyByPostion($config, $pos = 0) {
		$i = 0;
		foreach ( ( array ) $config as $key => $value ) {
			if ($pos === $i)
				return $key;
			$i ++;
		}
		return '';
	}
	
	/**
	 * @param string $sql
	 */
	final protected function logSql($sql = ''){
		$sql = $sql ? $sql : $this->last_sql;
		W::recordLog($sql,'DB','log');
	}
	
	/**
	 * 检查$linked中连接的合法性
	 * @param string|int|resource $key config的key或者连接资源
	 * @return string|resource
	 */
	final protected function checkKey($key = ''){
		if(!in_array($key,array_keys($this->linked)) || !is_resource($this->linked[$key])){
			throw new WSqlException('Database identify is not exists',1);
		}
		return $key;
	}
	
	final public function getSchema($key = ''){
		return $key ? $this->config[$key]['dbtype'] : $this->config[$this->key]['dbtype'];
	}
	
	final protected function keyEqual($key){
		return $this->key === $key;
	}
	
	final protected function schemaEqual($key){
		return $this->config[$this->key]['dbtype'] === $this->config[$key]['dbtype'];
	}
	
	

}