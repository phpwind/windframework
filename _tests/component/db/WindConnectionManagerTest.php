<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once ('component/db/base/IWindDbConfig.php');
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
		return include (T_P . '/data/db_config.php');
	}
	public function init() {
		require_once ('component/db/WindConnectionManager.php');
		if ($this->dbManager == null) {
			$this->config = $this->getDBConfig();
			$this->dbManager = new WindConnectionManager($this->config);
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
		return array(array('', ''), array('phpwind', ''), array('', IWindDbConfig::CONN_MASTER), 
			array('', IWindDbConfig::CONN_SLAVE));
	}
	
	/**
	 * @dataProvider providerConnection
	 */
	public function testGetConnection($identify, $type) {
		$conn = $this->dbManager->getConnection($identify, $type);
		$this->assertTrue(($conn instanceof WindDbAdapter));
	}
	
	public function testGetMasterConnection() {
		$conn = $this->dbManager->getMasterConnection();
		$this->assertTrue(($conn instanceof WindDbAdapter));
	}
	
	public function testGetSlaveConnection() {
		$conn = $this->dbManager->getSlaveConnection();
		$this->assertTrue(($conn instanceof WindDbAdapter));
	}
	
	public function testRegisterConnectionConfig() {
		$config = array('driver' => 'mssql', 'type' => 'slave', 'host' => 'localhost', 'user' => 'sa', 
			'password' => '151@suqian', 'name' => 'phpwind');
		$this->dbManager->registerConnectionConfig('registerConfig', $config);
		$this->assertTrue(is_array($this->dbManager->getConfig('registerConfig')));
	
	}
	
	public function testRegisterConnectionDriver() {
		$drivers = array(
			'registerDriver' => array('builder' => 'mysql', 'class' => 'WIND:component.db.drivers.mysql.WindMySql'));
		$this->dbManager->registerConnectionDriver('registerDriver', $drivers);
		$this->assertTrue(is_array($this->dbManager->getDriver('registerDriver')));
	}
	
	public function testRegisterConnectionBuilder() {
		$builder = array(
			'registerDriver' => array('builder' => 'mysql', 'class' => 'WIND:component.db.drivers.mysql.WindMySql'));
		$this->dbManager->registerConnectionBuilder('registerBuilder', $builder);
		$this->assertTrue(is_array($this->dbManager->getBuilder('registerBuilder')));
	}

}