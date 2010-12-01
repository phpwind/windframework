<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-1
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindDbManager{
	
	private  static $config = array();
	private  static $linked = array();
	
	public function __construct($config = array()){
		$this->parseConfig($config);	
	}
	

	
	
	public static function getDbConnFactory($config){
		$name = 'Wind'.$this->dbMap[$this->getSchema($key)];
		L::import('WIND:component.db.'.$name);
		return new $name($config); 
	}
	
	public static function getDbDriver($key){
		return isset(self::$linked[$key]) ? self::$linked[$key] : isset(self::$config[$key]) ? $this->getDbConnFactory(self::$config[$key]) : null;
	}
}