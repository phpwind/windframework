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
	const DATABASE = 'database';
	const DISTRIBUTED = 'distributed';
	const PARSERARRAY = 'connections, drivers, builders ';
	
	const CONNECTIONS = 'connections';
	const CONN_DRIVER = 'driver';
	const CONN_TYPE = 'type';
	const CONN_USER = 'user';
	const CONN_PASS = 'password';
	const CONN_HOST = 'host';
	const CONN_PORT = 'port';
	const CONN_NAME = 'name';
	const CONN_CHAR = 'charset';
	const CONN_FORCE = 'force';
	const CONN_PCONN = 'pconnect';
	const CONN_MASTER = 'master';
   	const CONN_SLAVE = 'slave';
   	const CONN_IDENTITY = 'identity';
   	
   	const DRIVERS = 'drivers';
   	const DRIVER_CLASS = 'class';
   	const DRIVER_BUILDER = 'builder';
   	
   	const BUILDERS = 'builders';
   	const BUILDER_CLASS= 'class';
   	
	const TABLE_NAME = 'table_name';
	const TABLE_ALIAS = 'table_alias';
	
	const RESULT_ASSOC = 1;
	const RESULT_NUM = 2;
	const RESULT_BOTH = 3;

}