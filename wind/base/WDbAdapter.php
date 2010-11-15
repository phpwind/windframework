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
	
	protected $linking = null;
	protected $queryId = '';
	protected $last_sql = '';
	protected $last_errstr = '';
	protected $last_errcode = 0;
	protected $sqlBuilder = null;
	protected $isConntected = 0;
	protected $isLog = false;
	protected $key = '';
	
	protected $dbtype = '';
	protected $dbMap = array ('mysql' => 'MySql', 'mssql' => 'MsSql', 'pgsql' => 'PgSql', 'ocsql' => 'OcSql' );
	protected $transCounter = 0;
	public $enableSavePoint = 0;
	protected $savepoint = array ();
	
	protected static $writeTimes = 0;
	protected static $readTimes = 0;
	protected static $linked = array ();
	protected static $config = array ();
	protected function __construct($config) {
		$this->parseConfig ( $config );
		$this->patchConnect ();
	}
	
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
	private function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(.+)\/?(master|slave)*$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
		}
		return array ('dbtype' => $config [1], 'dbuser' => $config [2], 'dbpass' => $config [3], 'dbhost' => $config [4], 'dbport' => $config [5], 'dbname' => $config [6], 'optype' => $config [7] );
	}
	
	protected function patchConnect() {
		foreach ( self::$config as $key => $value ) {
			$this->connect ( $value, $key );
		}
	}
	
	public abstract function connect($config, $key);
	public abstract function query($sql,$key='',$current = true);
	public abstract function execute($sql,$key='',$current = true);
	public abstract function getAll();
	public abstract function getMetaTables();
	public abstract function getMetaColumns();
	public abstract function savePoint();
	public abstract function beginTrans();
	public abstract function rollbackTrans();
	public abstract function getAffectedRows();
	public abstract function getInsertId();
	protected abstract function close();
	protected abstract function dispose();
	
	public  function getExecSqlTime(){
		
	}
	
	public function changeConn($key) {
		if (! isset ( self::$linked [$key] )) {
			throw new WSqlException ( "this database connecton is not exists", 1 );
		}
		$this->linking = self::$linked [$key];
		$this->key = $key;
	}

	public function getSqlBuilderFactory() {
		$config = self::$config [$this->key];
		if (empty ( $config ) || ! is_array ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
		}
		$dbType = $this->dbMap[strtolower($config ['dbtype'])];
		$builder = 'W'.$dbType.'Builder';
		$this->sqlBuilder = W::getInstance($builder);//¼ÓÔØÎÊÌâ
	}
	public  function insert(){
		$sql = $this->sqlBuilder->getInsertSql();
		return $this->exceute($sql,$key);
	}
	public  function update(){
		$sql = $this->sqlBuilder->getUpdateSql();
		return $this->exceute($sql,$key);
	}
	public function select(){
		$sql = $this->sqlBuilder->getUpdateSql();
		return $this->query($sql,$key);
	}
	public  function delete(){
		$sql = $this->sqlBuilder->getDeleteSql();
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
	
	protected function getLinking($optype = '', $key = '') {
		$masterSlave = $this->getMasterSlave ();
		$config = empty ( $masterSlave ) || empty ( $optype ) ? self::$config : $masterSlave [$optype];
		$key = $key ? $key : $this->getConfigKeyByPostion ( $config, mt_rand ( 0, count ( $config ) - 1 ) );
		$this->linking = self::$linked [$key];
		$this->key = $key;
	}
	
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