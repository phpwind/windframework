<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-1
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
final class WindDbManager{
	
	private  static $config = array();
	private  static $linked = array();
	private  $dbDriver = null;
	private  static $dbManager = null;
	
	private function __construct($config = array()){
		self::$config = $config ? $config : $this->getDbConfig();
	}
	
	private function getDbConfig(){
		$dbConfig = C::getDbConfig();
		$dbDriver = C::getDbDriver();$dbConfig = array(
		'phpwind' => array(
			'dbtype' => 'mysql',
			'dbhost' => 'localhost',
			'dbuser' => 'root',
			'dbpass' => 'suqian0512h',
			'dbport' => '3306',
			'dbname' => 'phpwind',
		),
		'user' => array(
			'dbtype' => 'mssql',
			'dbhost' => 'localhost',
			'dbuser' => 'sa',
			'dbpass' => '151@suqian',
			'dbname' => 'phpwind',
		),
	);
	$dbDriver =  array(
		'mysql' => array(
			'path' => 'WIND:component.db.WindMySql',
			'className' => 'WindMySql',
		),
		'mssql' => array(
			'path' => 'WIND:component.db.WindMsSql',
			'className' => 'WindMsSql',
		),
	);
		foreach($dbConfig as $key=>$value){
			if(in_array($value['dbtype'],array_keys($dbDriver))){
				$dbConfig[$key] = array_merge($dbConfig[$key],$dbDriver[$value['dbtype']]);
			}
		}
		return $dbConfig;
	}
	
	
	public function addDriverConfig($config,$identify = ''){
		if($identify && empty(self::$config[$identify])){
			throw new WindSqlException("");
		}
		$identify ? self::$config[$identify] = $config : self::$config[] = $config;
	}

	
	
	/**
	 * @param unknown_type $identify
	 * @param unknown_type $optype
	 * @return multitype:
	 */
	public  function dbDriverFactory($identify = '',$optype = ''){
		if($identify && empty(self::$config[$identify])){
			throw new WindSqlException("");
		}
		$identify = $identify ? $identify : $this->getRandomDbDriverIdentify($optype);
		if(empty(self::$linked[$identify])){
			$config = self::$config[$identify];
			L::import($config['path']);
			self::$linked[$identify] = new $config['className']($config);
		}
		return $this->dbDriver = self::$linked[$identify];
	}
	
	private function getRandomDbDriverIdentify($optype = ''){
		$masterSlave = $this->getMasterSlave ();
		$config = (empty ( $masterSlave ) || empty ( $optype )) ? self::$config : $masterSlave [$optype];
		return $this->getConfigIdentifyByPostion ( $config, mt_rand ( 0, count ( $config ) - 1 ) );
	}
	
	/**
	 * 查看是是否要主从数据库设置，并按主从配置返回数据库配置信息
	 * @return array
	 */
	private function getMasterSlave() {
		$array = array ();
		foreach ( self::$config as $key => $value ) {
			if (in_array ( $value ['optype'], array ('master', 'slave' ) )) {
				$array [$value ['optype']] [$key] = $value;
			}
		}
		return $array;
	}
	
	/**
	 *根据config的pos返回key
	 * @param array $config 数据库配置
	 * @param int $pos config的位置
	 * @return string 返回config的key
	 */
	 private function getConfigIdentifyByPostion($config, $pos = 0) {
		$i = 0;
		foreach ( ( array ) $config as $key => $value ) {
			if ($pos === $i)
				return $key;
			$i ++;
		}
		return '';
	}

	public static function getInstance($config = array()){
		if(NULL === self::$dbManager){
			self::$dbManager = new self($config);
		}
		return self::$dbManager;
	}
	
	public function __clone(){
		return self::$dbManager;
	}
}