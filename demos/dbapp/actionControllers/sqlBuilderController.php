<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import ( 'WIND:component.db.drivers.mysql.WindMySqlBuilder' );
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class SqlBuilderController extends WindController{
	public function run(){
		echo 'This is sqlBuilder demos';
	}
	
	public function selectSql(){
		$mySqlBuilder = new WindMySqlBuilder();
		
		//SELECT username,uid FROM pw_members 
		$mySqlBuilder->from('pw_members')->field('username','uid');
		$sql = $mySqlBuilder->getSelectSql();
		echo 'This is simple select sql::  '.$sql.'<br/>';
		//SELECT username,uid,b.title FROM pw_members AS a , pw_posts AS b WHERE a.uid=b.authorid AND b.tid >= 2 and b.pid < 1000 and uid IN (1,2,3,4,5,6,7,8) 
		$mySqlBuilder->from('pw_members','a')
					 ->from('pw_posts','b')
					 ->where('a.uid=b.authorid')
					 ->where('b.tid >= ? and b.pid < ? and uid IN ?',array(2,1000,array(1,2,3,4,5,6,7,8)))
					 ->field('username','uid','b.title');
		$sql = $mySqlBuilder->getSelectSql();
		echo 'This is complex select sql::  '.$sql.'<br/>';
		//SELECT a.username,b.title FROM pw_members AS a LEFT JOIN pw_posts AS b ON a.uid=b.authorid WHERE a.uid != 1 ORDER BY dateline DESC 
		$mySqlBuilder->from('pw_members','a','username')
					 ->leftJoin('pw_posts','a.uid=b.authorid','b','title')
					 ->where('a.uid != ?',1)
					 ->order('dateline',true);
		$sql = $mySqlBuilder->getSelectSql();
		echo 'This is join sql::  '.$sql.'<br/>';
		
		$mySqlBuilder->from('pw_members','a','username')
					 ->leftJoin('pw_posts','a.uid=b.authorid','b','title')
					 ->where('a.uid != :uid and name like :username',array(':uid'=>'1',':username'=>'%adfa%'))
					 ->order('dateline',true);
		$sql = $mySqlBuilder->getSelectSql();
		echo 'This is join sql::  '.$sql.'<br/>';
	}
	
	public function insertSql(){
		$mySqlBuilder = new WindMySqlBuilder();
		//INSERT pw_members ( name,age )VALUES ( 'a' , 'b' ) ( 'c' , 'd' ) 
		$mySqlBuilder->from('pw_members')->field('name','age')->data(array(array('a','b'),array('c','d')));
		$sql = $mySqlBuilder->getInsertSql();
		echo 'This is insert sql::  '.$sql.'<br/>';
		//REPLACE pw_members ( name,age )SET ( 'a' , 'b' ) ( 'c' , 'd' ) 
		$mySqlBuilder->from('pw_members')->field('name','age')->data(array(array('a','b'),array('c','d')));
		$sql = $mySqlBuilder->getReplaceSql();
		echo 'This is replace sql::  '.$sql.'<br/>';
	}
	
	public function deleteSql(){
		$mySqlBuilder = new WindMySqlBuilder();
		//UPDATE pw_members SET name= 'suqian' ,age= 3 WHERE name = 1 
		$mySqlBuilder->from('pw_members')->where('a.uid = ?',1);
		$sql = $mySqlBuilder->getDeleteSql();
		echo 'This is delete sql::  '.$sql.'<br/>';
	}
	
	public function updateSql(){
		$mySqlBuilder = new WindMySqlBuilder();
		//UPDATE pw_members SET name= 'suqian' ,age= 3 WHERE name = 1 
		$mySqlBuilder->from('pw_members')->set('name=?,age=?',array('suqian',3))->where('name = 1');
		$sql = $mySqlBuilder->getUpdateSql();
		echo 'This is update sql::  '.$sql.'<br/>';
	}
	

	
	public function select(){
		$phpwind = array (	
							'charset' => 'utf8', 
							'driver' => 'mysql', 
							'name' => 'phpwind_8', 
							'user' => 'root', 
							'password' => 'suqian0512h', 
							'host' => 'localhost',
							'port' => 3306,
					);
		$mySqlBuilder = new WindMySqlBuilder($phpwind);
		$result=$mySqlBuilder->from('pw_members','a')
							 ->from('pw_posts','b')
							 ->where('a.uid=b.authorid')
							 ->where('b.tid >= ? and b.pid < ? and uid IN ?',array(2,1000,array(1,2,3,4,5,6,7,8)))
					 		 ->field('username','uid','b.subject')
					 		 ->select()
					 		 ->getAllRow(MYSQL_ASSOC);
		echo 'This is select option<pre/>';
		print_r($result);
	}
	
	public function insert(){
		echo 'This is insert option<pre/>';
		$phpwind = array (	
							'charset' => 'utf8', 
							'driver' => 'mysql', 
							'name' => 'phpwind_8', 
							'user' => 'root', 
							'password' => 'suqian0512h', 
							'host' => 'localhost',
							'port' => 3306,
					);
		$mySqlBuilder = new WindMySqlBuilder($phpwind);
		$mySqlBuilder->from('pw_actions')
					 ->field('images','descrip','name')
					 ->data('a','b','c')
					 ->insert();
		
	}
	
	public function update(){
		echo 'This is update option<pre/>';
		$phpwind = array (	
							'charset' => 'utf8', 
							'driver' => 'mysql', 
							'name' => 'phpwind_8', 
							'user' => 'root', 
							'password' => 'suqian0512h', 
							'host' => 'localhost',
							'port' => 3306,
					);
		$mySqlBuilder = new WindMySqlBuilder($phpwind);
		$mySqlBuilder->from('pw_posts')
					 ->set('subject=?,buy=?',array('suqian',3))
					 ->where('pid = 1')
					 ->update();
	}
	
	public function  delete(){
		echo 'This is delete option<pre/>';
		$phpwind = array (	
							'charset' => 'utf8', 
							'driver' => 'mysql', 
							'name' => 'phpwind_8', 
							'user' => 'root', 
							'password' => 'suqian0512h', 
							'host' => 'localhost',
							'port' => 3306,
					);
		$mySqlBuilder = new WindMySqlBuilder($phpwind);
		$mySqlBuilder->from('pw_posts')
					 ->where('pid = 2')
					 ->delete();
		
	}
	
	public function distribute(){

		$manager =  new  WindConnectionManager();
		$adapter = $manager->getConnection('','');
		$builder = $adapter->getSqlBuilder();
		$result = $builder->from('pw_members','a','username')
				->leftJoin('pw_posts','a.uid=b.authorid','b','authorid')
				->select()->getAllRow(1);
		echo '<pre/>';
		print_r($result);
	}
}