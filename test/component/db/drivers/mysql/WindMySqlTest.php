<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-10
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestCase.php');
L::import(WIND_PATH . '/component/db/drivers/mysql/WindMySql.php');
L::import(WIND_PATH . '/component/db/drivers/mysql/WindMySqlBuilder.php');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMySqlTest extends  baseTestCase{
	private $WindMySql = null;
	
	public function setUp() {
		if($this->WindMySql == null){
			$config = C::getDataBaseConnection('phpwind_8');
			$this->WindMySql = new WindMySql($config);
		}
	}
	
	public function testGetSqlBuilder(){
		$this->assertTrue($this->WindMySql instanceof WindMySqlBuilder);
	}
	
	
	
	public function tearDown() {
		$this->WindMySql = null;
	}
}