<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 数据库配置常量
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
interface IWindDbConfig{
	
	const CONNECTIONS = 'connections';
	const DRIVERS = 'drivers';
	const BUILDERS = 'builders';
	const DRIVER = 'driver';
	const BUILDER = 'builder';
	const TYPE = 'type';
	const USER = 'user';
	const PASS = 'password';
	const HOST = 'host';
	const PORT = 'port';
	const NAME = 'name';
	const CHARSET = 'charset';
	const FORCE = 'force';
	const PCONNECT = 'pconnect';
	const MASTER = 'master';
   	const SLAVE = 'slave';
   	const IDENTITY = 'identity';
	const CLASSNAME = 'class';
   	
	/**
	 * @var int 取得结果集以关联数组形式取得
	 */
	const ASSOC = 1;
	/**
	 * @var int 取得结果集以索引数组形式取得
	 */
	const INDEX = 2;
	/**
	 * @var int 取得结果集以关联和索引数组形式取得
	 */
	const BOTH = 3;

}