<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
include (R_P . '/test/component/container/WindModuleTest.php');
include (R_P . '/test/component/db/AllDBTest.php');
include (R_P . '/test/component/form/WindFormFilterTest.php');
include (R_P . '/test/component/message/AllMessageTest.php');

class AllComponentTest {

	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllComponentTest_Suite');
		$suite->addTestSuite('WindModuleTest');
		$suite->addTest(ALLDBTest::suite());
		$suite->addTest(AllMessageTest::suite());

		//$suite->addTestSuite('WindActionFormTest');
		//$suite->addTestSuite('WindFormFilterTest');


		return $suite;
	}
}
