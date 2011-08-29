<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
Wind::import('WIND:mail.sender.IWindSendMail');
Wind::import ( 'WIND:mail.protocol.WindSmtp' );
/**
 * 邮件发送
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindSmtpMail implements IWindSendMail{
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
	
	public function __construct(array $config){
		$defautConfig = array('host'=>'127.0.0.1','port'=>'25','name'=>'localhost','auth'=>true);
		$config = array_merge($defautConfig,$config);
		if(!isset($config['host']) || !isset($config['port'])){
			throw new WindException('The mail host or port is not exist');
		}
		if($config['auth'] && (!isset($config['user']) || !isset($config['password']))){
			throw new WindException('In the verification mode, the user name and password is blank or wrong');
		}
		$this->auth = $config['auth'];
		$this->name = $config['name'];
		$this->smtp = new WindSmtp($config['host'],$config['port']);
		$this->smtp->open();
		$this->setAuthParams($config['user'],$config['password']);
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