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
abstract class WDbAdapter{
	
	protected $linking = null;
	protected $queryId = '';
	protected $last_sql = '';
	protected $last_errstr = '';
	protected $last_errcode = 0;
	protected $sqlBuilder = null;
	protected $isConntected = 0;
	protected $isLog = false;
	
	protected $dbtype = '';
	protected $transCounter = 0;
	public $enableSavePoint = 0;
	protected $savepoint = array();
	
	protected static $writeTimes = 0;
	protected static $readTimes = 0;
	protected static $linked = array();
	protected static $config = array();
	protected function __construct(){
		
	}
	
	private function parseConfig($config){
		$db_config = array();
		if(empty($config) || !is_array($config)){
			throw new WSqlException("database config is not correct",1);
		}
		foreach($config as $key=>$value){
			if(is_array($value)) $db_config[$key] = $value;
			if(is_string($value)) $db_config[$key] = $this->parseDsn($value);
		}
		return self::$config = $db_config;
	}
	private function parseDSN($dsn){
		$ifdsn = preg_match('/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(.+)\/?(master|slave)*$/',trim($dsn),$config);
		if(empty($dsn) || empty($ifdsn) || empty($config)){
			throw new WSqlException("database config is not correct",1);
		}
     	return array (
            'dbtype'   => $config[1],
            'dbuser'  => $config[2],
            'dbpass'   => $config[3],
            'dbhost'  => $config[4],
            'dbport'    => $config[5],
     		'dbname'   => $config[6],
     		'optype'   => $config[7],
           );
	}
	
	protected function patchConnect(){
		foreach(self::$config as $key=>$value){
			$this->connect($value,$key);
		}
	}
	
	protected abstract function connect($config,$key);
	public function addConnect($config){
		if($this->isLink($config[''])){
			
		}
	}
	public function switchConnect();
	public function switchDataBase();
	public function getSqlBuilder();
	public function query();
	public function exceute();
	public function insert();
	public function update();
	public function select();
	public function delete();
	public function getAll();
	public function getMetaTables();
	public function getMetaColumns();
	public function getExecSqlTime();
	
	protected function close();
	protected function dispose();
	public function savePoint();
	public function beginTrans();
	public function rollbackTrans();
	public function getAffectedRows();
	public function getInsertId();
	public function getLastSql(){
		return $this->last_sql;
	}
	public function getWriteTimes(){
		return self::$writeTimes;
	}
	public function getReadTimes(){
		return self::$readTimes;
	}
	public function getQueryTimes(){
		return (int)self::$writeTimes+(int)self::$readTimes;
	}
	
	protected function checkMasterSlave(){
		return defined('MASTER_SLAVE');
	}
	
	protected function getLink($key = ''){
		return $key ? self::$linked[$key] : $this->linking;
	}
	
	

}