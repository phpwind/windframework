<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once('core/exception/WindException.php');
require_once('component/db/drivers/mysql/WindMysql.php');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMySqlTest extends BaseTestCase {
	private $WindMySql = null;
	private $table = 'pw_actions';
	private $selectSql = 'SELECT images,descrip,name FROM pw_actions';
	private $insertSql = "INSERT pw_actions ( images,descrip,name )VALUES ( 'a' ,'b','c' ) ";
	private $updateSql = "UPDATE pw_actions SET name= 'suqian'  WHERE id = 1";
	private $deleteSql = "DELETE FROM pw_actions WHERE id = 2";
	private $config = array();
	
	public function __construct() {
		parent::__construct();
		C::init(include 'config.php');
		$this->config = C::getDataBaseConnection('phpwind_8');
		$this->WindMySql = new WindMySql($this->config);
	}
	
	public function testGetSqlBuilder() {
		$this->assertTrue($this->WindMySql->getSqlBuilder() instanceof WindMySqlBuilder);
	}
	
	public function testGetLastSql() {
		$this->testQuery();
		$this->assertEquals($this->selectSql, $this->WindMySql->getLastSql());
	}
	
	public function testQuery() {
		$this->assertTrue($this->WindMySql->query($this->selectSql));
	}
	
	public function testGetAffectedRows() {
		$this->assertTrue(is_int($this->WindMySql->getAffectedRows()));
	}
	
	public function testGetLastInsertId() {
		$this->assertTrue(is_int($this->WindMySql->getLastInsertId()));
	}
	
	public function testGetMetaTables() {
		$this->assertTrue(is_array($this->WindMySql->getMetaTables()));
	}
	
	public function testGetMetaTablesBySchema() {
		$this->assertTrue(is_array($this->WindMySql->getMetaTables($this->config[IWindDbConfig::CONN_NAME])));
	}
	
	public function testGetMetaColumns() {
		$this->assertTrue(is_array($this->WindMySql->getMetaColumns($this->table)));
	}
	
	public function testGetAllRow() {
		$this->testQuery();
		$this->assertTrue(is_array($this->WindMySql->getAllRow(MYSQL_ASSOC)));
	}
	
	public function testGetRow() {
		$this->testQuery();
		$this->assertTrue(is_array($this->WindMySql->getRow(MYSQL_ASSOC)));
	}
	
	public function testInsert() {
		$this->assertTrue($this->WindMySql->insert($this->insertSql));
	}
	
	public function testUpdate() {
		$this->assertTrue($this->WindMySql->update($this->updateSql));
	}
	
	public function testDelete() {
		$this->assertTrue($this->WindMySql->update($this->deleteSql));
	}
	
	public function testGetDriver() {
		$this->assertEquals($this->config[IWindDbConfig::CONN_DRIVER], $this->WindMySql->getDriver());
	}
	
	public function testGetSchema() {
		$this->assertEquals($this->config[IWindDbConfig::CONN_NAME], $this->WindMySql->getSchema());
	}
	
	public function testGetConfig() {
		$config = $this->WindMySql->getConfig();
		$this->assertEquals($this->config[IWindDbConfig::CONN_NAME], $config[IWindDbConfig::CONN_NAME]);
	}
	
	public function testGetConnection() {
		$this->assertTrue(is_resource($this->WindMySql->getConnection()));
	}
	
	public function testTransAction(){
		$this->assertTrue($this->WindMySql->beginTrans());
		//$this->assertTrue($this->WindMySql->commitTrans());
	}
	
	
	
	public function __destruct() {
		$this->WindMySql = null;
	}
}