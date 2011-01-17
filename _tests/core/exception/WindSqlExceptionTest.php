<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-23
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
class WindSqlExceptionTest extends BaseTestCase {
     public function setUp() {
     	parent::setUp();
		require_once ('core/exception/WindSqlException.php');
     }
     
     public function tearDown() {
     	parent::tearDown();
     }
     
     public function testNewException() {
     	try{
     		throw new WindSqlException('error', WindSqlException::DB_CONN_EMPTY);
     	}catch(Exception $e) {
     		$this->assertEquals("'error' Database configuration is empty.", $e->getMessage());
     		$this->assertEquals('WindSqlException', get_class($e));
     		return;
     	}
     	$this->fail('Test error');
     }
}