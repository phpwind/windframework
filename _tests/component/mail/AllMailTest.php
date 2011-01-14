<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2011-1-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

include ('component/mail/protocol/WindImapTest.php');
include ('component/mail/protocol/WindPop3Test.php');
include ('component/mail/protocol/WindSmtpTest.php');
include ('component/mail/protocol/WindSocketTest.php');

include ('component/mail/send/WindSmtpSendTest.php');
include ('component/WindMailTest.php');

class AllMailTest {
	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('AllComponentTest_Suite');
		$suite->addTestSuite('WindImapTest');
		$suite->addTestSuite('WindPop3Test');
		$suite->addTestSuite('WindSmtpTest');
		$suite->addTestSuite('WindSocketTest');
		$suite->addTestSuite('WindSmtpSendTest');
		$suite->addTestSuite('WindMailTest');
		return $suite;
	}
}