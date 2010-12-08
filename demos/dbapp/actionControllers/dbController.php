<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import ( 'WIND:component.db.drivers.mysql.WindMySqlBuilder' );
class DbController extends WindController{
	 protected $phpwind = array (	
							'charset' => 'utf8', 
							'dbtype' => 'mysql', 
							'dbname' => 'phpwind_8', 
							'dbuser' => 'root', 
							'dbpass' => 'suqian0512h', 
							'dbhost' => 'localhost',
							'dbport' => 3306,
							'className' =>'WindMySql' 
					);
	public function run() {
		echo 'this is ' . __CLASS__;
	}
	
	public function sqlBuilder(){
		
		$mySqlBuilder = L::getInstance('WindMySqlBuilder');
		
		//SELECT username,uid FROM pw_members 
		$mySqlBuilder->from('pw_members')->field('username','uid');
		$sql = $mySqlBuilder->getSelectSql();
		echo $sql.'<br/>';
		//SELECT username,uid,b.title FROM pw_members AS a , pw_posts AS b WHERE a.uid=b.authorid AND b.tid >= 2 and b.pid < 1000 and uid IN (1,2,3,4,5,6,7,8) 
		$mySqlBuilder->from('pw_members','a')
					 ->from('pw_posts','b')
					 ->where('a.uid=b.authorid')
					 ->where('b.tid >= ? and b.pid < ? and uid IN ?',array(2,1000,array(1,2,3,4,5,6,7,8)))
					 ->field('username','uid','b.title');
		$sql = $mySqlBuilder->getSelectSql();
		echo $sql.'<br/>';
		//SELECT a.username,b.title FROM pw_members AS a LEFT JOIN pw_posts AS b ON a.uid=b.authorid WHERE a.uid != 1 ORDER BY dateline DESC 
		$mySqlBuilder->from('pw_members','a','username')
					 ->leftJoin('pw_posts','a.uid=b.authorid','b','title')
					 ->where('a.uid != ?',1)
					 ->order('dateline',true);
		$sql = $mySqlBuilder->getSelectSql();
		echo $sql.'<br/>';
		//INSERT pw_members ( name,age )VALUES ( 'a' , 'b' ) 
		$mySqlBuilder->from('pw_members')->field('name','age')->data('a','b');
		$sql = $mySqlBuilder->getInsertSql();
		echo $sql.'<br/>';
		//INSERT pw_members ( name,age )VALUES ( 'a' , 'b' ) ( 'c' , 'd' ) 
		$mySqlBuilder->from('pw_members')->field('name','age')->data(array(array('a','b'),array('c','d')));
		$sql = $mySqlBuilder->getInsertSql();
		echo $sql.'<br/>';
		//REPLACE pw_members ( name,age )SET ( 'a' , 'b' ) ( 'c' , 'd' ) 
		$mySqlBuilder->from('pw_members')->field('name','age')->data(array(array('a','b'),array('c','d')));
		$sql = $mySqlBuilder->getReplaceSql();
		echo $sql.'<br/>';
		
		//UPDATE pw_members SET name= 'suqian' ,age= 3 WHERE name = 1 
		$mySqlBuilder->from('pw_members')->set('name=?,age=?',array('suqian',3))->where('name = 1');
		$sql = $mySqlBuilder->getUpdateSql();
		echo $sql.'<br/>';
	} 
	
	public function select(){
		$mySqlBuilder = L::getInstance('WindMySqlBuilder',array($this->phpwind));
		$result=$mySqlBuilder->from('pw_members','a')
							 ->from('pw_posts','b')
							 ->where('a.uid=b.authorid')
							 ->where('b.tid >= ? and b.pid < ? and uid IN ?',array(2,1000,array(1,2,3,4,5,6,7,8)))
					 		 ->field('username','uid','b.subject')
					 		 ->select()
					 		 ->getAllRow(MYSQL_ASSOC);
		echo '<pre/>';
		print_r($result);
	}
	
	public function update(){
		$mySqlBuilder = L::getInstance('WindMySqlBuilder',array($this->phpwind));
		$mySqlBuilder->from('pw_posts')->set('subject=?,buy=?',array('suqian',3))->where('pid = 1')->update();
	}
	
	public function delete(){
		$mySqlBuilder = L::getInstance('WindMySqlBuilder',array($this->phpwind));
		$mySqlBuilder->from('pw_posts')->where('pid = 2')->delete();
	}
	
	public function insert(){
		$mySqlBuilder = L::getInstance('WindMySqlBuilder',array($this->phpwind));
		$mySqlBuilder->from('pw_actions')->field('images','descrip','name')->data('a','b','c')->insert();
	}
}