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
class WMySql extends WDbAdapter {
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#connect()
	 */
	public function connect($config, $key) {
		if (!is_array ( $config ) || empty ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
		}
		if (!isset ( $key )) {
			throw new WSqlException ( "you must define master and slave database", 1 );
		}
		$this->key = $key;
		$host = $config ['dbport'] ? $config ['dbhost'] . ':' . $config ['dbport'] : $config ['dbhost'];
		$pconnect = $config ['pconnect'] ? $config ['pconnect'] : $this->pconnect;
		$force = $config ['force'] ? $config ['force'] : $this->force;
		$charset = $config ['charset'] ? $config ['charset'] : $this->charset;
		if (! ($this->linking = $this->getLinked ( $key ))) {
			self::$linked [$key] = $this->linking = $pconnect ? mysql_pconnect ( $host, $config ['dbuser'], $config ['dbpass'] ) : mysql_connect ( $host, $config ['dbuser'], $config ['dbpass'], $force );
			if ($config ['dbname'] && is_resource ( $this->linking )) {
				$this->changeDB ( $config ['dbname'],$key);
			}
			$this->setCharSet ($charset,$key);
			if (isset ( self::$config [$key] )) {
				self::$config [$key] = $config;
			}
		}
		return  $this->linking;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#query()
	 */
	public function query($sql, $key = '') {
		$this->checkKey($key);
		if(empty($this->switch)){
			$this->getLinking ( 'slave', $key );
		}
		if (! is_resource ( $this->linking )) {
			throw new WSqlException ( "This linking is not validate handle or resource", 1 );
		}
		$key = $key ? $key : $this->key;
		if(isset(self::$readTimes[$key])){
			self::$readTimes[$key]++;
		}else{
			self::$readTimes[$key] = 1;
		}
		$this->query = mysql_query ( $sql, $this->linking );
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#execute()
	 */
	public function execute($sql,$key = '') {
		$this->checkKey($key);
		if(empty($this->switch)){
			$this->getLinking ( 'master', $key );
		}
		$key = $key ? $key : $this->key;
		if(isset(self::$writeTimes[$key])){
			self::$writeTimes[$key]++;
		}else{
			self::$writeTimes[$key] = 1;
		}
		$this->query = mysql_query ( $sql, $this->linking );
		return true;
	}
	
	public  function getAll(){
		
	}
	public  function getMetaTables(){
		
	}
	public  function getMetaColumns(){
		
	}
	public  function savePoint(){
		
	}
	public  function beginTrans(){
		
	}
	public  function rollbackTrans(){
		
	}
	public  function getAffectedRows(){
		
	}
	public  function getInsertId(){
		
	}
	public  function close(){
		foreach(self::$linked as $key=>$value){
			mysql_close($value);
		}
	}
	public  function dispose(){
		foreach(self::$linked as $key=>$value){
			mysql_close($value);
			unset(self::$linked[$key]);
		}
		$this->linking = null;
	}
	public function getVersion($key = '') {
		return mysql_get_server_info ( $this->getLinked ( $key ) );
	}
	
	public function setCharSet($charset, $key = '') {
		$version = ( int ) substr ( $this->getVersion ( $key ), 0, 1 );
		if ($version > 4) {
			$this->execute ( "SET NAMES '" . $charset . "'", $key);
		}
		return true;
	}
	
	public function changeDB($databse, $key = '') {
		return $this->execute ( "USE $databse", $key);
	}
	
	protected function error(){
		$this->last_errstr = mysql_error();
		$this->last_errcode = mysql_errno();
		if($this->last_errstr || $this->last_errcode){
			throw new WSqlException($this->last_errstr,$this->last_errcode);
		}
	}
}