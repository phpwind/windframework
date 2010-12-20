<?php
require_once 'PHPUnit\Framework\TestCase.php';
/**
 * test case.
 */
class Test extends PHPUnit_Framework_TestCase {
	
private $config = '';
	
	public function init(){
		$this->config = C::getDataBaseConnection('phpwind_8');
		$this->WindMySqlBuilder = new WindMySqlBuilder($this->config);
	}
	
	public static function a(){
		return array(
			array('pw_posts','','','',''),
			array('pw_posts','a.uid=pw_posts.authorid','','',''),
			array('pw_posts','a.uid=b.authorid','b','',''),
			array('pw_posts','a.uid=b.authorid','b','subject,pid',''),
			array('pw_posts','a.uid=b.authorid','b','b.subject,b.pid',''),
			array('pw_posts','a.uid=b.authorid','b',array('subject','pid'),'phpwind_8'),
		);
	}
	
	/**
     * @dataProvider a
     */

	public function testFrom($table,$joinWhere,$table_alias,$fields,$schema){
		print_r(func_get_args());
	}
	
	
	
	

}

