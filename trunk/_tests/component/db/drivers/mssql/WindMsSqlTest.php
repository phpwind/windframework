<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMsSqlTest extends BaseTestCase {
	private $WindMsSql = null;
	private $table = 'pw_actions';
	private $selectSql = 'SELECT images,descrip,name FROM pw_actions';
	private $insertSql = "INSERT pw_actions ( images,descrip,name )VALUES ( 'a' ,'b','c' ) ";
	private $updateSql = "UPDATE pw_actions SET name= 'suqian'  WHERE id = 1";
	private $deleteSql = "DELETE FROM pw_actions WHERE id = 2";
	private $config = array();
	
	public function init() {
		L::import ( 'WIND:core.exception.WindException' );
		L::import ( 'WIND:component.db.base.IWindDbConfig' );
		L::import ( 'WIND:component.db.drivers.mssql.WindMsSqlBuilder' );
		L::import ( 'WIND:component.db.drivers.mssql.WindMsSql' );
		if ($this->WindMySql == null) {
			$this->config = C::getDataBaseConnection('user');
			$this->WindMsSql = new WindMsSql($this->config);
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testGetSqlBuilder() {
		$this->assertTrue($this->WindMsSql->getSqlBuilder() instanceof WindMsSqlBuilder);
	}
	
	public function testGetLastSql() {
		$this->testQuery();
		$this->assertEquals($this->selectSql, $this->WindMsSql->getLastSql());
	}
	
	public function testQuery() {
		$this->assertTrue($this->WindMsSql->query($this->selectSql));
	}
	
	public function testGetAffectedRows() {
		$this->assertTrue(is_int($this->WindMsSql->getAffectedRows()));
	}
	
	public function testGetLastInsertId() {
		$this->assertTrue(is_int($this->WindMsSql->getLastInsertId()));
	}
	
	public function testGetMetaTables() {
		$this->assertTrue(is_array($this->WindMsSql->getMetaTables()));
	}
	
	public function testGetMetaTablesBySchema() {
		$this->assertTrue(is_array($this->WindMsSql->getMetaTables($this->config[IWindDbConfig::CONN_NAME])));
	}
	
	public function testGetMetaColumns() {
		$this->assertTrue(is_array($this->WindMsSql->getMetaColumns($this->table)));
	}
	
	public function testGetAllRow() {
		$this->testQuery();
		$this->assertTrue(is_array($this->WindMsSql->getAllRow(MSSQL_ASSOC)));
	}
	
	public function testGetRow() {
		$this->testQuery();
		$this->assertTrue(is_array($this->WindMsSql->getRow(MSSQL_ASSOC)));
	}
	
	public function testInsert() {
		$this->assertTrue($this->WindMsSql->insert($this->insertSql));
	}
	
	public function testUpdate() {
		$this->assertTrue($this->WindMsSql->update($this->updateSql));
	}
	
	public function testDelete() {
		$this->assertTrue($this->WindMsSql->update($this->deleteSql));
	}
	
	public function testGetDriver() {
		$this->assertEquals($this->config[IWindDbConfig::CONN_DRIVER], $this->WindMsSql->getDriver());
	}
	
	public function testGetSchema() {
		$this->assertEquals($this->config[IWindDbConfig::CONN_NAME], $this->WindMsSql->getSchema());
	}
	
	public function testGetConfig() {
		$config = $this->WindMsSql->getConfig();
		$this->assertEquals($this->config[IWindDbConfig::CONN_NAME], $config[IWindDbConfig::CONN_NAME]);
	}
	
	public function testGetConnection() {
		$this->assertTrue(is_resource($this->WindMsSql->getConnection()));
	}
	/*
	public function testBeginTrans(){
		$this->assertTrue($this->WindMsSql->beginTrans());
	}
	
	public function testCommitTrans(){
		$this->assertTrue($this->WindMsSql->commitTrans());
	}*/
	
	public function __destruct() {
		$this->WindMsSql = null;
	}
}