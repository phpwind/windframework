<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once('../../../../BaseTestCase.php');
L::import(WIND_PATH . '/component/exception/WindException.php');
L::import(WIND_PATH . '/component/db/drivers/mysql/WindMySqlBuilder.php');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMySqlBuilderTest extends BaseTestCase{

	private $config = '';
	private $WindMySqlBuilder = null;
	
	
	public function init(){
		
		if($this->WindMySqlBuilder == null){
			$this->config = C::getDataBaseConnection('phpwind_8');
			$this->WindMySqlBuilder = new WindMySqlBuilder($this->config);
		}
	}
	
	public static function provider(){
		return array(
			array('pw_posts','','','',''),
			array('pw_posts','a.uid=pw_posts.authorid','','',''),
			array('pw_posts','a.uid=b.authorid','b','',''),
			array('pw_posts','a.uid=b.authorid','b','subject,pid',''),
			array('pw_posts','a.uid=b.authorid','b','b.subject,b.pid',''),
			array('pw_posts','a.uid=b.authorid','b',array('subject','pid'),'phpwind_8'),
		);
	}
	
	public function setUp() {
		$this->init();
	}
	
	public function tearDown(){
		$this->WindMySqlBuilder->reset();
	}
	
	/**
     * @dataProvider provider
     */

	public function testFrom($table,$joinWhere,$table_alias,$fields,$schema){

		$builder = $this->WindMySqlBuilder->from($table,$table_alias,$fields,$schema);
		$from = $this->WindMySqlBuilder->getSql(WindSqlBuilder::FROM);
		$field = $fields ? $this->WindMySqlBuilder->getSql(WindSqlBuilder::FIELD):true;
		$this->assertTrue($from && $field && ($builder instanceof WindSqlBuilder));
	}
	
	public function testDistinct(){
		$builder = $this->WindMySqlBuilder->distinct(true);
		$distinct = $this->WindMySqlBuilder->getSql(WindSqlBuilder::DISTINCT);
		$this->assertEquals(WindSqlBuilder::SQL_DISTINCT,$distinct);
		$this->assertTrue(($builder instanceof WindSqlBuilder));
	}
	
	public function testField(){
		$builder = $this->WindMySqlBuilder->field('username,uid');
		$field = $this->WindMySqlBuilder->getSql(WindSqlBuilder::FIELD);
		$this->assertTrue($field && ($builder instanceof WindSqlBuilder));
	}
	
	/**
     * @dataProvider provider
     */
	public function testJoin($table,$joinWhere,$table_alias,$fields,$schema){
		$this->assertTrue($this->_join(WindSqlBuilder::INNER,$table,$joinWhere,$table_alias,$fields,$schema));
	}
	
	
	/**
     * @dataProvider provider
     */
	public function testLeftJoin(){
		$this->assertTrue($this->_join(WindSqlBuilder::LEFT,$table,$joinWhere,$table_alias,$fields,$schema));
	}
	
	/**
     * @dataProvider provider
     */
	public function testRightJoin(){
		$this->assertTrue($this->_join(WindSqlBuilder::RIGHT,$table,$joinWhere,$table_alias,$fields,$schema));
	}
	
	/**
     * @dataProvider provider
     */
	public function testFullJoin(){
		$this->assertTrue($this->_join(WindSqlBuilder::FULL,$table,$joinWhere,$table_alias,$fields,$schema));
	}
	
	public function testInnerJoin(){
		$this->assertTrue($this->_join(WindSqlBuilder::INNER,$table,$joinWhere,$table_alias,$fields,$schema));
	}
	
	public function testCrossJoin(){
		
	}
	
	public function testWhere(){
		
	}
	
	public function testOrWhere(){
		
	}
	
	public function testGroup(){
		
	}
	
	public function testHaving(){
		
	}
	
	public function testOrHaving(){
		
	}
	
	public function testOrder(){
		
	}
	
	public function testLimit(){
		
	}
	
	public function testData(){
		
	}
	
	public function testSet(){
		
	}
	
	private function _join($joinType,$table,$joinWhere,$table_alias,$fields,$schema){
		$joinMethod = $joinType.'Join';
		$builder = $this->WindMySqlBuilder->$joinMethod($table,$joinWhere,$table_alias,$fields,$schema);
		$join = $this->WindMySqlBuilder->getSql(WindSqlBuilder::JOIN);
		$field = $fields ? $this->WindMySqlBuilder->getSql(WindSqlBuilder::FIELD):true;
		return $join && $field && ($builder instanceof WindSqlBuilder);
	}
}