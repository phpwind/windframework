<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindSmtpSendTest extends BaseTestCase {
	private $sender = null; /* @var $sender WindSmtpSend*/
	private $mail = null;
	
	public function init() {
		require_once ('component/mail/WindMail.php');
		require_once ('component/mail/send/WindSmtpSend.php');
		if (null === $this->sender) {
			$this->sender = new WindSmtpSend('smtp.qq.com',25,'qq.com',true);
			$this->mail = new WindMail();
		}
	}
	
	public function setUp() {
		parent::setUp();
		date_default_timezone_set('UTC');
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testSend(){
		$this->mail->clearAll();
		$this->mail->setFrom('635927818@qq.com');
		$this->mail->setTo('635927818@qq.com');
		$this->mail->setCc('635927818@qq.com');
		$this->mail->setBcc('635927818@qq.com', 'ha');
		$this->mail->setSubject("test mail ");
		$this->mail->setDate();
		$this->mail->setBodyHtml("test it<a>aaa</a>");
		$this->mail->setContentEncode(WindMail::ENCODE_BASE64);
		$this->mail->setAttachment(__FILE__, 'application/x-httpd-php', WindMail::DIS_INLINE, WindMail::ENCODE_QP);
		$this->sender->setAuthParams('635927818@qq.com','');
		$this->sender->send($this->mail);
	}
}