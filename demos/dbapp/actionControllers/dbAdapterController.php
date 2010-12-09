<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import ( 'WIND:component.db.drivers.mysql.WindMySql' );
L::import ( 'WIND:component.db.drivers.mssql.WindMsSql' );
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class DbAdapterController extends WindController{
	public function run(){
		echo 'This is dbAdapter demos';
	}
	
	public function mysql(){
		$config = C::getDataBaseConnection('phpwind_8');
		$mysql = new WindMySql($config);
		echo '<pre>this is mysql adapter';
		print_r($mysql);
	}
	
	public function mssql(){
		$config = C::getDataBaseConnection('user');
		$mssql = new WindMsSql($config);
		echo '<pre>this is mssql adapter';
		print_r($mssql);
		
	}
	
	public function mySqlBuilder(){
		$config = C::getDataBaseConnection('phpwind_8');
		$mysql = new WindMySql($config);
		echo '<pre>this is mysqlBuilder';
		print_r($mysql->getSqlBuilder());
	}
	
	public function delete(){
		$config = C::getDataBaseConnection('phpwind_8');
		$mysql = new WindMySql($config);
		echo '<pre>this is delete option:';
		$mysql->delete("DELETE FROM pw_posts WHERE pid = 2");
	}
	
	public function update(){
		$config = C::getDataBaseConnection('phpwind_8');
		$mysql = new WindMySql($config);
		echo '<pre>this is update option:';
		$mysql->update("UPDATE pw_members SET name= 'suqian' ,age= 3 WHERE name = 1");
	}
	
	public function select(){
		$config = C::getDataBaseConnection('phpwind_8');
		$mysql = new WindMySql($config);
		echo '<pre>this is select option:';
		$mysql->select("SELECT a.username,b.subject FROM pw_members AS a LEFT JOIN pw_posts AS b ON a.uid=b.authorid WHERE a.uid != 1 ORDER BY regdate DESC ");
		$result = $mysql->getAllRow(1);
		print_r($result);
	}
	
	public function insert(){
		$config = C::getDataBaseConnection('phpwind_8');
		$mysql = new WindMySql($config);
		echo '<pre>this is insert option:';
		$mysql->insert("INSERT pw_actions ( 'images','descrip','name' )VALUES ( 'a' , 'b','c' ) ( 'c' , 'd','f' )");
	}
}