<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.exception.WindException');
L::import('WIND:component.db.drivers.mysql.WindMySqlBuilder');
L::import('WIND:component.db.base.IWindDbConfig');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMySqlBuilderTest extends BaseTestCase {
	
	private $config = '';
	private $WindMySqlBuilder = null;
	
	public function init() {
		
		if ($this->WindMySqlBuilder == null) {
			$this->config = C::getDataBaseConnection('phpwind_8');
			$this->WindMySqlBuilder = new WindMySqlBuilder($this->config);
		}
	}
	
	public static function provider() {
		return array(
			array('pw_posts', '', '', '', ''), 
			array('pw_posts', 'a.uid=pw_posts.authorid', '', '', ''), 
			array('pw_posts', 'a.uid=b.authorid', 'b', '', ''), 
			array('pw_posts', 'a.uid=b.authorid', 'b', 'subject,pid', ''), 
			array('pw_posts', 'a.uid=b.authorid', 'b', 'b.subject,b.pid', ''), 
			array('pw_posts', 'a.uid=b.authorid', 'b', array('subject', 'pid'), 'phpwind_8')
			);
	}
	
	public static function providerWhere() {
		return array(array('username = "1"', '', ''), array(array('age <= ?', 'uid > ?'), array(3, 4), ''), 
			array(array('age = 3', 'uid >4'), '', ''), array('uid < ?', 1, true), 
			array('username = ? AND uid > ? ', array('"suqian"', 2), false));
	}
	
	public static function providerOrder() {
		return array(array(array('dateline' => 'desc'), true), array('dateline', true), array('age', false), 
			array(array('dateline' => true, 'age' => false), true));
	}
	
	public static function providerLimit() {
		return array(array(1, 0), array(1, 5));
	}
	
	public static function providerData() {
		return array(array(array('a', 'b', 'c')), 
			array(array(array('1a', '1b', '1c'), array('2a', '2b', '2c'))), 
			array('username' => array(1, 2, 3), 'age' => array(1, 2, 3)));
	}
	
	public static function providerSet() {
		return array(array('username = "1"', ''), array(array('age <= ?', 'uid > ?'), array(3, 4)), 
			array(array('age = 3', 'uid >4'), ''), array('uid < ?', 1), 
			array('username = ? , uid > ? ', array('"suqian"', 2)));
	}
	
	public static function providerAffected() {
		return array(array(true), array(false));
	}
	
	public static function providerPlaceHolder() {
		return array(array("id= 1 AND name= 'suqian'", array()), array(array('id= 1', "name= 'suqian'"), array()), 
			array(array('id=?', 'name=?'), array(1, 'suqian')), array(array('id' => 1, 'name' => 'suqian'), array()), 
			array('id=? AND name=?', array(1, 'suqian')), 
			array('id=:id AND name=:name', array(':name' => 'suqian', ':id' => 1)));
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
		$this->WindMySqlBuilder->reset();
	}
	
	/**
	 * @dataProvider provider
	 */
	
	public function testFrom($table, $joinWhere, $table_alias, $fields, $schema) {
		
		$builder = $this->WindMySqlBuilder->from($table, $table_alias, $fields, $schema);
		$from = $this->WindMySqlBuilder->getSql(WindSqlBuilder::FROM);
		$field = $fields ? $this->WindMySqlBuilder->getSql(WindSqlBuilder::FIELD) : true;
		$this->assertTrue($from && $field && ($builder instanceof WindSqlBuilder));
	}
	
	public function testDistinct() {
		$builder = $this->WindMySqlBuilder->distinct(true);
		$distinct = $this->WindMySqlBuilder->getSql(WindSqlBuilder::DISTINCT);
		$this->assertEquals(WindSqlBuilder::SQL_DISTINCT, $distinct);
		$this->assertTrue(($builder instanceof WindSqlBuilder));
	}
	
	public function testField() {
		
		$this->assertTrue($this->_field('username', 'uid'));
	}
	
	public function testFieldWithArray() {
		$this->assertTrue($this->_field(array('username', 'uid')));
	}
	
	public function testFieldWithParam() {
		$this->assertTrue($this->_field('username', 'uid', 'age'));
	}
	
	/**
	 * @dataProvider provider
	 */
	public function testJoin($table, $joinWhere, $table_alias, $fields, $schema) {
		$this->assertTrue($this->_join('join', $table, $joinWhere, $table_alias, $fields, $schema));
	}
	
	/**
	 * @dataProvider provider
	 */
	public function testLeftJoin($table, $joinWhere, $table_alias, $fields, $schema) {
		$this->assertTrue($this->_join(WindSqlBuilder::LEFT, $table, $joinWhere, $table_alias, $fields, $schema));
	}
	
	/**
	 * @dataProvider provider
	 */
	public function testRightJoin($table, $joinWhere, $table_alias, $fields, $schema) {
		$this->assertTrue($this->_join(WindSqlBuilder::RIGHT, $table, $joinWhere, $table_alias, $fields, $schema));
	}
	
	/**
	 * @dataProvider provider
	 */
	public function testFullJoin($table, $joinWhere, $table_alias, $fields, $schema) {
		$this->assertTrue($this->_join(WindSqlBuilder::FULL, $table, $joinWhere, $table_alias, $fields, $schema));
	}
	
	/**
	 * @dataProvider provider
	 */
	public function testInnerJoin($table, $joinWhere, $table_alias, $fields, $schema) {
		$this->assertTrue($this->_join(WindSqlBuilder::INNER, $table, $joinWhere, $table_alias, $fields, $schema));
	}
	
	/**
	 * @dataProvider provider
	 */
	public function testCrossJoin($table, $joinWhere, $table_alias, $fields, $schema) {
		$this->assertTrue($this->_join(WindSqlBuilder::CROSS, $table, $joinWhere, $table_alias, $fields, $schema));
	}
	
	/**
	 * @dataProvider providerWhere
	 */
	public function testWhere($where, $value, $group) {
		$this->assertTrue($this->_where(WindSqlBuilder::WHERE, $where, $value, $group, true));
	}
	
	/**
	 * @dataProvider providerWhere
	 */
	public function testOrWhere($where, $value, $group) {
		$this->assertTrue($this->_where(WindSqlBuilder::WHERE, $where, $value, $group, false));
	}
	
	public function testGroup() {
		$this->assertTrue($this->_field('dateline', 'uid'));
	}
	
	public function testGroupWithArray() {
		$this->assertTrue($this->_field(array('dateline', 'uid')));
	}
	
	public function testGroupWithParam() {
		$this->assertTrue($this->_field('username', 'uid'));
	}
	
	/**
	 * @dataProvider providerWhere
	 */
	public function testHaving() {
		$this->assertTrue($this->_where(WindSqlBuilder::HAVING, $where, $value, $group, true));
	}
	
	/**
	 * @dataProvider providerWhere
	 */
	public function testOrHaving() {
		$this->assertTrue($this->_where(WindSqlBuilder::HAVING, $where, $value, $group, false));
	}
	
	/**
	 * @dataProvider providerOrder
	 */
	public function testOrder($field, $type) {
		$builder = $this->WindMySqlBuilder->order($field, $type);
		$order = $this->WindMySqlBuilder->getSql(WindSqlBuilder::ORDER);
		$this->assertTrue($order && ($builder instanceof WindSqlBuilder));
	}
	
	/**
	 *@dataProvider providerLimit
	 */
	public function testLimit($limit, $offset) {
		$builder = $this->WindMySqlBuilder->limit($limit, $offset);
		$page = $this->WindMySqlBuilder->getSql(WindSqlBuilder::LIMIT);
		$this->assertTrue($page && ($builder instanceof WindSqlBuilder));
	}
	
	/**
	 *@dataProvider providerData
	 */
	public function testData($data) {
		$builder = $this->WindMySqlBuilder->data($data);
		$data = $this->WindMySqlBuilder->getSql(WindSqlBuilder::DATA);
		$this->assertTrue($data && ($builder instanceof WindSqlBuilder));
	}
	
	/**
	 * @dataProvider providerSet
	 */
	public function testSet($field, $value) {
		$builder = $this->WindMySqlBuilder->set($field, $value);
		$set = $this->WindMySqlBuilder->getSql(WindSqlBuilder::SET);
		$this->assertTrue($set && ($builder instanceof WindSqlBuilder));
	}
	
	public function testGetSelectSql() {
		$sql = "SELECT    a.username,b.title FROM   pw_members AS a   LEFT JOIN  pw_posts AS b ON a.uid=b.authorid  WHERE a.uid !=  1  OR a.group > 0  GROUP BY a.age  HAVING a.age !=  4   ORDER BY dateline DESC  ";
		$assemblySql = $this->WindMySqlBuilder->from('pw_members', 'a', 'username')->leftJoin('pw_posts', 'a.uid=b.authorid', 'b', 'title')->where('a.uid != ?', 1)->orWhere('a.group > 0')->group('a.age')->having(array(
			'a.age != ?'), array(4))->order('dateline', true)->getSelectSql();
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($assemblySql));
	}
	
	public function testGetInsertSql() {
		$sql = "INSERT   pw_members  ( name,age )VALUES  (  'a'  ,  'b'  ) , (  'c'  ,  'd'  ) ";
		$insertSql = $this->WindMySqlBuilder->from('pw_members')->field('name', 'age')->data(array(array('a', 'b'), 
			array('c', 'd')))->getInsertSql();
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($insertSql));
	
	}
	
	public function testGetUpdateSql() {
		$sql = "UPDATE   pw_members  SET  username=  'suqian'  ,age= 3   WHERE uid = 11 ";
		$updateSql = $this->WindMySqlBuilder->from('pw_members')->set('username=?,age=?', array('suqian', 3))->where('uid = 11')->getUpdateSql();
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($updateSql));
	}
	
	public function testGetReplaceSql() {
		$sql = "REPLACE   pw_members  ( name,age )VALUES  (  'a'  ,  'b'  ) , (  'c'  ,  'd'  ) ";
		$replaceSql = $this->WindMySqlBuilder->from('pw_members')->field('name', 'age')->data(array(array('a', 'b'), 
			array('c', 'd')))->getReplaceSql();
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($replaceSql));
	}
	
	public function testGetDeleteSql() {
		$sql = "DELETE  FROM   pw_members AS a   WHERE a.uid =  11  ";
		$deleteSql = $this->WindMySqlBuilder->from('pw_members', 'a')->where('a.uid = ?', 11)->getDeleteSql();
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($deleteSql));
	}
	
	/**
	 * @dataProvider providerAffected
	 */
	public function testGetAffectedSql($ifquery) {
		$sql = 'SELECT ' . ($ifquery ? 'FOUND_ROWS()' : 'ROW_COUNT()') . ' AS afftectedRows';
		$affectedSql = $this->WindMySqlBuilder->getAffectedSql($ifquery);
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($affectedSql));
	}
	
	public function testGetLastInsertIdSql() {
		$sql = 'SELECT LAST_INSERT_ID() AS insertId';
		$lastInsertSql = $this->WindMySqlBuilder->getLastInsertIdSql();
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($lastInsertSql));
	}
	
	public function testGetMetaTableSql() {
		$sql = 'SHOW TABLES FROM phpwind';
		$metaTableSql = $this->WindMySqlBuilder->getMetaTableSql('phpwind');
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($metaTableSql));
	}
	
	public function testGetMetaColumnSql() {
		$sql = 'SHOW COLUMNS FROM pw_members';
		$metaColumSql = $this->WindMySqlBuilder->getMetaColumnSql('pw_members');
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($metaColumSql));
	}
	
	public function testSelect() {
		$result = $this->WindMySqlBuilder->from('pw_members', 'a')->from('pw_posts', 'b')->where('a.uid=b.authorid')->where('b.tid >= ? and b.pid < ? and uid IN ?', array(
			2, 1000, array(1, 2, 3, 4, 5, 6, 7, 8)))->field('username', 'uid', 'b.subject')->select()->getAllRow(MYSQL_ASSOC);
		$this->assertTrue(is_array($result));
	}
	
	public function testUpdate() {
		$result = $this->WindMySqlBuilder->from('pw_posts')->set('subject=?,buy=?', array('suqian', 3))->where('pid = 1')->update();
		$this->assertTrue($result);
	}
	
	public function testDelete() {
		$result = $this->WindMySqlBuilder->from('pw_posts')->where('pid = 2')->delete();
		$this->assertTrue($result);
	}
	
	public function testInsert() {
		$result = $this->WindMySqlBuilder->from('pw_actions')->field('images', 'descrip', 'name')->data('a', 'b', 'c')->insert();
		$this->assertTrue($result);
	}
	
	/**
	 * @dataProvider providerPlaceHolder
	 */
	public function testPlaceHolder($where, $value) {
		$sql = "SELECT name,id,images FROM pw_actions WHERE id= 1 AND name='suqian'";
		$selectSql = $this->WindMySqlBuilder->from('pw_actions')->field('name', 'id', 'images')->where($where, $value)->getSelectSql();
		$this->assertEquals($this->trimSpace($sql), $this->trimSpace($selectSql));
	}
	
	private function trimSpace($sql) {
		return preg_replace("/[\t ]+/", '', $sql);
	}
	
	private function _where($type, $where, $value, $group, $logic) {
		$method = $logic ? $type : 'or' . ucfirst($type);
		$builder = $this->WindMySqlBuilder->$method($where, $value, $group);
		$_where = $this->WindMySqlBuilder->getSql($type);
		return $_where && ($builder instanceof WindSqlBuilder);
	}
	private function _field($field) {
		$params = func_num_args();
		$field = $params > 1 ? func_get_args() : func_get_arg(0);
		$builder = $this->WindMySqlBuilder->field($field);
		$field = $this->WindMySqlBuilder->getSql(WindSqlBuilder::FIELD);
		return $field && ($builder instanceof WindSqlBuilder);
	}
	
	private function _join($joinType, $table, $joinWhere, $table_alias, $fields, $schema) {
		$joinMethod = 'join' == $joinType ? $joinType : $joinType . 'Join';
		$builder = $this->WindMySqlBuilder->$joinMethod($table, $joinWhere, $table_alias, $fields, $schema);
		$join = $this->WindMySqlBuilder->getSql(WindSqlBuilder::JOIN);
		$field = $fields ? $this->WindMySqlBuilder->getSql(WindSqlBuilder::FIELD) : true;
		return $join && $field && ($builder instanceof WindSqlBuilder);
	}
}