<?php
require_once 'web/WindWebApplicationTest.php';
require_once 'web/WindUrlHelperTest.php';
require_once 'web/WindForwardTest.php';
require_once 'web/WindErrorHandlerTest.php';
require_once 'web/WindDispatcherTest.php';
require_once 'web/WindControllerTest.php';
/**
 * Static test suite.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class AllWebTest extends PHPUnit_Framework_TestSuite {

	public static function main() {
		PHPUnit_TestUI_TestRunner::run(self::suite());
	}

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('WindFramework AllWebTest');
		$suite->addTestSuite("WindWebApplicationTest");
		$suite->addTestSuite("WindUrlHelperTest");
		$suite->addTestSuite("WindForwardTest");
		$suite->addTestSuite("WindErrorHandlerTest");
		$suite->addTestSuite("WindDispatcherTest");
		$suite->addTestSuite("WindControllerTest");
		return $suite;
	}
}

