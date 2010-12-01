<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

define ( 'F_P', '../../wind/' );
define ( 'C_P', '../../wind/' );


require_once (F_P . '/wind.php');

echo '<pre/>';
/**
 * mysql 连接使用
 */


require '../../wind/component/db/WindMySql.php';
require '../../wind/component/db/WindMsSql.php';

$phpwind = array ('charset' => 'gb2312', 'dbtype' => 'mysql', 'dbname' => 'phpwind_8', 'dbuser' => 'root', 'dbpass' => 'suqian0512h', 'dbhost' => 'localhost', 'dbport' => 3306 );
$phpwind_beta = array ('charset' => 'gb2312', 'dbtype' => 'mysql', 'dbname' => 'phpwind_8beta', 'dbuser' => 'root', 'dbpass' => 'suqian0512h', 'dbhost' => 'localhost', 'dbport' => '3306', 'force' => 1 );
$config = array ('phpwind' => $phpwind, 'beta' => $phpwind_beta );
$mysql = new WindMySql ( $config );

$option ['table'] = 'pw_members a';
$option ['where'] = array ('a =223 and b=33' );
$option ['where'] = array('a = :a AND B=:b',array(':a'=>'sss',':b'=>'sss'));
$option ['field'] = array ('a.uid' => 'ids', 'a.username' );
$option ['join'] = array ('pw_posts' => array ('left', 'a.uid=b.authorid', 'b' ) );
//不指定db连接
//$mysql->select ( $option );
//指定db连接
$mysql->select ( $option,'phpwind' );
$result = $mysql->getAllResult ();
//$mysql->getAffectedRows ( true );
//$mysql->getMetaColumns ( 'pw_members' );
//$result = $mysql->getAllResult ();
//更新数据
$option ['set'] = array ('username' => "test" );
$option ['table'] = 'pw_members';
$option ['where'] = array ('lt' => array ('uid', 1 ) );
$mysql->update ( $option, 'phpwind' );
//新增数据
$option ['where'] = array ('eq' => array ('uid', 1 ) );
$option ['data'] = array ("asfafafafaf" );
$option ['table'] = 'pw_members';
$option ['field'] = array ('username' );
$mysql->insert ( $option, 'phpwind' );
//删除数据
$option ['table'] = 'pw_members';
$option ['where'] = array ('eq' => array ('uid', 22 ) );
$mysql->delete ( $option, 'phpwind' );
print_r($result);

//sql server连接使用
 
$dsn = array(
'test'=>"mssql:://username:password@localhost:port/dbname/optype/pconect/force",
);
$phpwind_beta = array ('charset' => 'gb2312', 'dbtype' => 'mssql', 'dbname' => 'phpwind', 'dbuser' => 'sa', 'dbpass' => '151@suqian', 'dbhost' => 'localhost', 'dbport' => '', 'force' => 1 );
$config = array ('betat' => $phpwind_beta );
$mssql = new WindMsSql ( $config );
/**
$option ['table'] = 'pw_members a';
$option ['where'] = array ('lt' => array ('a.uid', 10 ) );
$option ['field'] = array ('a.uid' => 'ids', 'a.username' );
$option ['join'] = array ('pw_posts' => array ('left', 'a.uid=b.authorid', 'b' ) );
$mssql->select ( $option );
$result = $mssql->getAllResult ();
//print_r($result);
$result = $mssql->getMetaColumns ( 'pw_posts' );
$result = $mssql->getAllResult ();
print_r ( $result );
*/



$config = array (
	'phpwind' => array (
		'charset' => 'gb2312', 
		'dbtype' => 'mysql', 
		'dbname' => 'phpwind_8', 
		'dbuser' => 'root', 
		'dbpass' => 'suqian0512h', 
		'dbhost' => 'localhost', 
		'dbport' => 3306,
		'optype' => 'master'
	), 
	'beta' =>  array (
		'charset' => 'gb2312', 
		'dbtype' => 'mysql', 
		'dbname' => 'phpwind_8beta', 
		'dbuser' => 'root', 
		'dbpass' => 'suqian0512h', 
		'dbhost' => 'localhost', 
		'dbport' => '3306', 
		'force' => 1 ,
		'optype' => 'master',
		'pconnect' => 1
	)
);

$dsn ="mssql://username:password@localhost:3306/dbname/gbk/1/1/";
	
//print_r ( $mssql->parseDSN($dsn));