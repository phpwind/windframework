<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
include (dirname(dirname(dirname(__FILE__))) . '/BaseTestCase.php');
L::import('WIND:core.exception.WindException');
L::import('WIND:component.db.WindConnectionManager');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindConnectionManagerTest extends BaseTestCase {
	private $dbManager = null;
	private $config = array();
	public function init() {
		if ($this->dbManager == null) {
			$this->config = C::getDataBase();
			$this->dbManager = new WindConnectionManager($this->config);
		}
	}
	
	public function setUp() {
		$this->init();
	}
	
	public static function providerConnection() {
		return array(array('', ''), array('phpwind_8', ''), array('', IWindDbConfig::CONN_MASTER), 
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