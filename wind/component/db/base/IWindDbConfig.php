<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

interface IWindDbConfig{
	const CONFIG_TYPE = 'dbtype';
	const CONFIG_USER = 'dbuser';
	const CONFIG_PASS = 'dbpass';
	const CONFIG_HOST = 'dbhost';
	const CONFIG_PORT = 'dbport';
	const CONFIG_NAME = 'dbname';
	const CONFIG_RWDB = 'optype';
	const CONFIG_CHAR = 'charset';
	const CONFIG_FORCE = 'force';
	const CONFIG_PCONN = 'pconnect';
	const CONFIG_MASTER = 'master';
   	const CONFIG_SLAVE = 'slave';
   	const CONFIG_PATH= 'path';
   	const CONFIG_CLASS = 'className';
}