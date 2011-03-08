<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindSmtpSendTest extends BaseTestCase {
	/* @var $sender WindSmtpMail*/
	private $sender = null; 
	/**
	 * @var $mail WindMail
	 */
	private $mail = null;
	
	public function init() {
		require_once ('component/mail/WindMail.php');
		require_once ('component/mail/sender/WindSmtpMail.php');
		if (null === $this->sender) {
			$config = array('host'=>'smtp.qq.com','port'=>25,'name'=>'qq.com','auth'=>true,'user'=>'635927818@qq.com','password'=>'');
			
			$this->sender = new WindSmtpMail($config);
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
		$this->sender->send($this->mail);
		
	}
}