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
			self::$linked [$key] = $this->linking = $linked = $pconnect ? mysql_pconnect ( $host, $config ['dbuser'], $config ['dbpass'] ) : mysql_connect ( $host, $config ['dbuser'], $config ['dbpass'], $force );
			if ($config ['dbname'] && is_resource ( $linked )) {
				$this->changeDB ( $config ['dbname'], $key );
			}
			$this->setCharSet ( $charset, $key );
			if (isset ( self::$config [$key] )) {
				self::$config [$key] = $config;
			}
		}
		return $linked;
	}
	
	public function query($sql, $key = '', $current = true) {
		if ($current) {
			$this->getLinking ( 'slave', $key );
		}
		if (! is_resource ( $this->linking )) {
			throw new WSqlException ( "this database is not validate handle", 1 );
		}
		mysql_query ( $sql, $this->linking );
	}
	
	public function execute($sql, $key = '', $current = true) {
		if ($current) {
			$this->getLinking ( 'master', $key );
		}
		mysql_query ( $sql, $this->linking );
	}
	public function getVersion($key = '') {
		return mysql_get_server_info ( $this->getLinked ( $key ) );
	}
	
	public function setCharSet($charset, $key = '', $current = false) {
		$version = ( int ) substr ( $this->getVersion ( $key ), 0, 1 );
		if ($version > 4) {
			$this->execute ( "SET NAMES '" . $charset . "'", $key, $current );
		}
		return true;
	}
	
	public function changeDB($databse, $key = '', $current = false) {
		return $this->execute ( "USE $databse", $key, $current );
	}
}