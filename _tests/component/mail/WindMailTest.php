<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMailTest extends BaseTestCase {
	private $mail = null;
	
	public function init() {
		require_once ('component/mail/WindMail.php');
		if (null === $this->mail) {
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
	public static function providerMail() {
		return array(array('635927818@qq.com', 'suqian'), array('635927818@qq.com', 1));
	}
	
	/**
	 * @dataProvider providerMail
	 */
	public function testTo($mail, $name) {
		$this->mail->setTo($mail, $name);
		$to = $this->mail->getTo();
		$this->assertArrayHasKey($name, $to);
		$this->assertTrue($mail == $to[$name]);
	}
	/**
	 * @dataProvider providerMail
	 */
	public function testCc($mail, $name) {
		$this->mail->setCc($mail, $name);
		$cc = $this->mail->getCc();
		$this->assertArrayHasKey($name, $cc);
		$this->assertTrue($mail == $cc[$name]);
	}
	
	/**
	 * @dataProvider providerMail
	 */
	public function testBcc($mail, $name) {
		$this->mail->setBcc($mail, $name);
		$bcc = $this->mail->getBCc();
		$this->assertArrayHasKey($name, $bcc);
		$this->assertTrue($mail == $bcc[$name]);
	}
	
	public function testFrom() {
		$this->mail->setFrom('635927818@qq.com');
		$from = $this->mail->getFrom();
		$this->assertTrue('635927818@qq.com' == $from);
	}
	
	public function testSubject() {
		$this->mail->setSubject('subject');
		$this->mail->setSubject('reSubject');
		$subject = $this->mail->getSubject();
		$this->assertTrue('reSubject' == $subject);
	}
	
	public function testChineseDate() {
		$this->mail->setDate();
		$date = $this->mail->getMailHeader(WindMail::DATE);
		$this->assertTrue(WindGeneralDate::getChinaDate() == $date[0]);
	}
	
	public function testRFCDate() {
		$this->mail->setDate(null, false);
		$date = $this->mail->getMailHeader(WindMail::DATE);
		$this->assertTrue(WindGeneralDate::getRFCDate() == $date[0]);
	}
	
	public function testMessageId() {
		$this->mail->setMessageId(null, false);
		$messageId = $this->mail->getMailHeader(WindMail::MESSAGEID);
		$this->assertTrue(is_array($messageId) && is_string($messageId[0]));
	}
	
	public function testBodyHtml() {
		$this->mail->setBodyHtml('test suqian go');
		$html = $this->mail->getBodyHtml();
		$this->assertTrue('test suqian go' == $html);
	}
	
	public function testBodyText() {
		$this->mail->setBodyText('test suqian go');
		$text = $this->mail->getBodyText();
		$this->assertTrue('test suqian go' == $text);
	}
	
	public function testContentTypeWithText() {
		$this->mail->setBodyText('test suqian go');
		$this->mail->setContentType();
		$contentType = $this->mail->getMailHeader(WindMail::CONTENTTYPE);
		$this->assertTrue(is_string($contentType[0]));
	}
	
	public function testContentTypeWithHtml() {
		$this->mail->setBodyHtml('test phpwind go');
		$this->mail->setContentType();
		$contentType = $this->mail->getMailHeader(WindMail::CONTENTTYPE);
		$this->assertTrue(is_string($contentType[0]));
	}
	
	public function testContentTypeWithTextAndHtml() {
		$this->mail->setBodyHtml('test phpwind go');
		$this->mail->setBodyText('test suqian go');
		$this->mail->setContentType();
		$contentType = $this->mail->getMailHeader(WindMail::CONTENTTYPE);
		$this->assertTrue(is_string($contentType[0]));
	}
	
	public function testContentTypeWithEmbed() {
		$this->mail->setBodyHtml('test phpwind go');
		$this->mail->setAttachment('./maze.png', 'image/png', WindMail::DIS_INLINE, WindMail::ENCODE_QP);
		$this->mail->setEmbed(true);
		$this->mail->setContentType();
		$contentType = $this->mail->getMailHeader(WindMail::CONTENTTYPE);
		$this->assertTrue(is_string($contentType[0]));
	}
	
	public function testContentTypeWithAttach() {
		$this->mail->setBodyHtml('test phpwind go');
		$this->mail->setAttachment('./maze.png', 'image/png', WindMail::DIS_INLINE, WindMail::ENCODE_QP);
		$this->mail->setEmbed(false);
		$this->mail->setCharset('gbk');
		$this->mail->setContentType();
		$contentType = $this->mail->getMailHeader(WindMail::CONTENTTYPE);
		$this->assertTrue(is_string($contentType[0]));
	}
	
	public function testGetRecipients() {
		$mail = self::providerMail();
		$this->mail->setCc($mail[0][0], $mail[0][1]);
		$this->mail->setTo($mail[1][0], $mail[1][1]);
		$recipients = $this->mail->getRecipients();
		$this->assertTrue(is_array($recipients) && count($mail) == count($recipients));
	}
	
	public function testSend() {
		$this->mail->clearAll();
		$this->mail->setFrom('635927818@qq.com');
		$this->mail->setTo('635927818@qq.com');
		$this->mail->setTo('aoxue.1988.su.qian@163.com');
		$this->mail->setCc('weihu@aliyun-inc.com');
		$this->mail->setBcc('594524924@qq.com', 'ha');
		$this->mail->setSubject("test mail ");
		$this->mail->setDate();
		$this->mail->setBodyHtml("test it<a>aaa</a>");
		$this->mail->setContentEncode(WindMail::ENCODE_BASE64);
		$this->mail->setAttachment(__FILE__, 'application/x-httpd-php', WindMail::DIS_INLINE, WindMail::ENCODE_QP);
		$header = $this->mail->createHeader();
		$body = $this->mail->createBody();
		$this->assertTrue(is_string($header) && is_string($body));
		$config = array('host'=>'smtp.qq.com','port'=>25,'name'=>'qq.com','auth'=>true,'user'=>'635927818@qq.com','password'=>'');
		$this->mail->send(WindMail::SEND_SMTP,$config);
	}
	
	public function testEncodeHeader(){
		$string = 'test encode header中国';
		$base64 = '=?gbk?B?dGVzdCBlbmNvZGUgaGVhZGVy5Lit5Zu9?==?gbk?B??=';
		$qp = '=?gbk?Q?test=20encode=20header=E4=B8=AD=E5=9B=BD?=';
		$encode = $this->mail->encodeHeader($string,WindMail::ENCODE_BASE64);
		$this->assertTrue($base64 == strtr(trim($encode),array("\n"=>'',' '=>'')));
		$encode = $this->mail->encodeHeader($string,WindMail::ENCODE_QP);
		$this->assertTrue($qp == trim($encode));
	}
	
}