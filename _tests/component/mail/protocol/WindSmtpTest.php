<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindSmtpTest extends BaseTestCase {
	private $smtp = null;
	
	public function init() {
		require_once ('component/mail/protocol/WindSmtp.php');
		if (null === $this->smtp) {
			$this->smtp = new WindSmtp('smtp.qq.com', 25);
		}
	}
	
	public function setUp() {
		parent::setUp();
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	/**
	 * 根据测试填写上自己的测试邮箱
	 */
	public static function provider() {
		return array(array('qq.com', '961412979@qq.com', '', '961412979@qq.com', '961412979@qq.com'));
	}
	/**
	 * @dataProvider provider
	 */
	public function testSend($ehlo, $loginMail, $loginPwd, $from, $to) {
		$this->smtp->open();
		$this->smtp->ehlo($ehlo);
		$this->smtp->authLogin($loginMail, $loginPwd);
		$this->smtp->mailFrom($from);
		$this->smtp->rcptTo($to);
		//$smtp->rcptTo('1752585926@qq.com testa');
		$data = "From: {$from}\n";
		$data .= "To: {$to}\n";
		$data .= "Cc: {$to},afafa 402289603@qq.com\n";
		$data .= "Date: Mon, 25 Oct 2004 14:24:27 +0800\n";
		$data .= "Subject: test mail\n\n";
		$data .= "Hi,test2\nThis is a test mail,you don't reply it.only test sendmail is not incorrect 
		";
		$this->smtp->data($data);
		$this->smtp->noop();
		$this->smtp->close();
		$this->assertTrue(true);
	}

}