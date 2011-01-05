<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

/**
 * 邮件发送
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindSmtpSend{
	/**
	 * @var WindSmtp 邮件发送服务器
	 */
	protected $smtp = null;
	/**
	 * @var boolean 是否启用验证
	 */
	protected $auth = true;
	/**
	 * @var string 邮件主机名
	 */
	protected $name = 'localhost';
	/**
	 * @var string 邮件用户名
	 */
	protected $username = '';
	/**
	 * @var string 邮件密码
	 */
	protected $password = '';
	
	public function __construct($host,$port,$name = 'localhost',$auth = true){
		$this->auth = true;
		$this->name = $name;
		$this->smtp = new WindSmtp($host,$port);
		$this->smtp->open();
	}
	
	/**
	 * 发送邮件
	 * @param WindMail $mail 邮件消息封装对象
	 */
	public function send(WindMail $mail){
		$this->smtp->ehlo($this->name);
		if($this->auth){
			$this->smtp->authLogin($this->username,$this->password);
		}
		$this->smtp->mailFrom($mail->getFrom());
		foreach($mail->getRecipients() as $rcpt){
			$this->smtp->rcptTo($rcpt);
		}
		$header = $mail->createHeader();
		$body = $mail->createBody();
		$data = $header.$body;
		$this->smtp->data($header.$body);
		$this->smtp->quit();
	}
	
	/**
	 * 设置验证参数
	 * @param string $username 用户名
	 * @param string $password 密码
	 */
	public function setAuthParams($username,$password){
		$this->username = $username;
		$this->password = $password;
	}
	

	
	public function __destruct(){
		$this->smtp->close();
		$this->smtp = null;
	}
}