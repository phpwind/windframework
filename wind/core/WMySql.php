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
	
	public function connect($config, $key) {
		if (is_array ( $config ) || empty ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
		}
		if (isset ( $key )) {
			throw new WSqlException ( "you must define master and slave database", 1 );
		}
		$host = $config ['dbport'] ? $config ['dbhost'] . ':' . $config ['dbport'] : $config ['dbhost'];
		$pconnect = $config ['pconnect'] ? $config ['pconnect'] : $this->pconnect;
		$force = $config ['force'] ? $config ['force'] : $this->force;
		$charset = $config ['charset'] ? $config ['charset'] : $this->charset;
		if (! ($linked = $this->getLink ( $key ))) {
			$linked = $pconnect ? mysql_pconnect ( $host, $config ['dbuser'], $config ['dbpass'] ) : mysql_connect ( $host, $config ['dbuser'], $config ['dbpass'], $force );
			if($config['dbname'] && is_resource($linked)){
				$this->setDataBase($config['dbname'],$linked);
			}
			$this->setCharSet($charset,$linked);
			self::$linked [$key] = $linked;
			if(isset(self::$config[$key])){
				self::$config[$key] = $config;
			}
		}
		return $linked;
	}
	
	public function getVersion($link = null) {
		return mysql_get_server_info ( $link );
	}
	
	public function setCharSet($charset, $link = null) {
		$version = ( int ) substr ( $this->getVersion ( $link ), 0, 1 );
		if ($version > 4) {
			$this->execute ( "SET NAMES '" . $charset . "'", $link );
		}
	}
	
	public function setDataBase($databse,$link = null){
		if(!$this->execute("USE $databse",$link)){
			mysql_select_db($database,$link);
		}
	} 
}