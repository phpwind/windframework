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
	
	protected function connect($config,$key='') {
		if(is_array($config) || empty($config)){
			throw new WSqlException ( "database config is not correct", 1 );
		}
		$host = $config['dbport'] ? $config['dbhost'].':'.$config['dbport'] : $config['dbhost'];
		$pconect = $config['pconnect'] ? $config['pconnect'] : $this->pconnect;
		$force = $config['force'] ? $config['force'] : $this->force;
		if ($this->checkMasterSlave ()) {
			if ($tmp = $config ['master']) {
				self::$linked [$tmp] [$key] = $this->connect ( $value );
			} else {
				throw new WSqlException ( "you must define master and slave database", 1 );
			}
		
		} else {
			if($this->isLink($key))
			self::$linked [$key] = $this->connect ( $value );
		}
	}
}