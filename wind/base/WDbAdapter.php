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
	protected $key = '';
	protected $charset = 'gbk';
	protected $force = false;
	protected $pconnect = false;
	protected $switch = 0;
	
	protected $dbtype = '';
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
	 * @param unknown_type $config
	 * @return Ambigous <multitype:, multitype:unknown >
	 */
	private function parseConfig($config) {
		$db_config = array ();
		if (empty ( $config ) || ! is_array ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
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
	 * @param unknown_type $dsn
	 * @return multitype:unknown 
	 */
	private function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(.+)\/?(master|slave)*$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
		}
		return array ('dbtype' => $config [1], 'dbuser' => $config [2], 'dbpass' => $config [3], 'dbhost' => $config [4], 'dbport' => $config [5], 'dbname' => $config [6], 'optype' => $config [7] );
	}
	
	/**
	 * 
	 */
	protected function patchConnect() {
		foreach ( self::$config as $key => $value ) {
			$this->connect ( $value, $key );
		}
	}
	
	public abstract function connect($config, $key);
	public abstract function query($sql,$key='');
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
	
	public  function getExecSqlTime(){
		
	}
	
	/**
	 * @param unknown_type $key
	 */
	public function changeConn($key) {
		if (! isset ( self::$linked [$key] )) {
			throw new WSqlException ( "this database connecton is not exists", 1 );
		}
		$this->linking = self::$linked [$key];
		$this->key = $key;
		$this->switch = 1;
	}
	public function freeChange(){
		$this->switch = 0;
	}

	
	/**
	 * 
	 */
	public function getSqlBuilderFactory() {
		$config = self::$config [$this->key];
		if (empty ( $config ) || ! is_array ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
		}
		$dbType = $this->dbMap[strtolower($config ['dbtype'])];
		$builderClass = 'W'.$dbType.'Builder';
		$this->sqlBuilder = W::getInstance($builderClass);//加载问题
		
	}

	/**
	 * @param unknown_type $option
	 * @param unknown_type $key
	 */
	public  function insert($option,$key = ''){
		$sql = $this->sqlBuilder->getInsertSql($optiion);
		return $this->exceute($sql,$key);
	}
	
	/**
	 * @param unknown_type $option
	 * @param unknown_type $key
	 */
	public  function update($option,$key = ''){
		$sql = $this->sqlBuilder->getUpdateSql($option);
		return $this->exceute($sql,$key);
	}
	/**
	 * @param unknown_type $option
	 * @param unknown_type $key
	 */
	public function select($option,$key = ''){
		$sql = $this->sqlBuilder->getSelectSql($option);
		return $this->query($sql,$key);
	}
	/**
	 * @param unknown_type $option
	 * @param unknown_type $key
	 */
	public  function delete($option,$key = ''){
		$sql = $this->sqlBuilder->getDeleteSql($option);
		return $this->exceute($sql,$key);
	}
	
	public function replace($option,$key = ''){
		$sql = $this->sqlBuilder->getReplaceSql($option);
		return $this->exceute($sql,$key);
	}

	public function getLastSql() {
		return $this->last_sql;
	}
	public function getWriteTimes() {
		return self::$writeTimes;
	}
	public function getReadTimes() {
		return self::$readTimes;
	}
	public function getQueryTimes() {
		return ( int ) self::$writeTimes + ( int ) self::$readTimes;
	}
	
	protected function getLinked($key = '') {
		return $key ? self::$linked [$key] : $this->linking;
	}
	
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
	 * @param unknown_type $optype
	 * @param unknown_type $key
	 */
	protected function getLinking($optype = '', $key = '') {
		$masterSlave = $this->getMasterSlave ();
		$config = empty ( $masterSlave ) || empty ( $optype ) ? self::$config : $masterSlave [$optype];
		$key = $key ? $key : $this->getConfigKeyByPostion ( $config, mt_rand ( 0, count ( $config ) - 1 ) );
		$this->linking = self::$linked [$key];
		$this->key = $key;
	}
	
	/**
	 * @param unknown_type $config
	 * @param unknown_type $pos
	 * @return unknown|string
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

}