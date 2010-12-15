<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

include (R_P . '/test/component/db/WindConnectionManagerTest.php');
include (R_P . '/test/component/db/drivers/mysql/WindMySqlBuilderTest.php');
include (R_P . '/test/component/db/drivers/mysql/WindMySqlTest.php');
include (R_P . '/test/component/db/drivers/mssql/WindMsSqlBuilderTest.php');
include (R_P . '/test/component/db/drivers/mssql/WindMsSqlTest.php');

class AllDBTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllDBTest_Suite');
		$suite->addTestSuite('WindMysqlBuilderTest');
		$suite->addTestSuite('WindMySqlTest');
		$suite->addTestSuite('WindConnectionManagerTest');
		/*$suite->addTestSuite('WindMsSqlBuilderTest');
		$suite->addTestSuite('WindMsSqlTest');*/
		return $suite;
	}
}