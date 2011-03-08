<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once ('component/db/drivers/IWindDbConfig.php');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindConnectionManagerTest extends BaseTestCase {
	private $dbManager = null;
	private $config = array();
	private function getDBConfig() {
		return array(
			'connections' => array(
				'connection1' => array(
					'driver' => 'mysql',
					'type' => 'master',
					'host' => 'localhost',
					'user' => 'root',
					'password' => 'suqian0512h',
					'port' => '3306',
					'name' => 'phpwind_8',
				),
				'connection2' => array(
					'driver' => 'mysql',
					'type' => 'slave',
					'host' => 'localhost',
					'user' => 'root',
					'password' => 'suqian0512h',
					'port' => '3306',
					'name' => 'phpwind_8beta',
				),
			),
			'drivers' => array(
				'mysql' => array(
					'builder' => 'mySqlBuilder',
					'class' => 'WIND:component.db.drivers.mysql.WindMySql',
				),
				'mssql' => array(
					'builder' => 'msSqlBuilder',
					'class' => 'WIND:component.db.drivers.mssql.WindMsSql',
				),
			),
			'builders' => array(
				'mySqlBuilder' => array(
					'class' => 'WIND:component.db.drivers.mysql.WindMySqlBuilder',
				),
			),
		);
	}
	public function init() {
		require_once ('component/db/WindConnectionManager.php');
		if ($this->dbManager == null) {
			$this->dbManager = new WindConnectionManager();
			$windConfig = new WindConfig(new WindConfigParser());
			$windConfig->setConfig($this->getDBConfig());
			$this->dbManager->setConfig($windConfig);
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public static function providerConnection() {
		return array(array('', ''), array('connection1', ''), array('', IWindDbConfig::MASTER), 
			array('', IWindDbConfig::SLAVE));
	}
	
	/**
	 * @dataProvider providerConnection
	 */
	public function testGetConnection($identify, $type) {
		$conn = $this->dbManager->getConnection($identify, $type);
		$this->assertTrue(($conn instanceof AbstractWindDbAdapter));
	}
	
	public function testGetMasterConnection() {
		$conn = $this->dbManager->getMasterConnection();
		$this->assertTrue(($conn instanceof AbstractWindDbAdapter));
	}
	
	public function testGetSlaveConnection() {
		$conn = $this->dbManager->getSlaveConnection();
		$this->assertTrue(($conn instanceof AbstractWindDbAdapter));
	}

}