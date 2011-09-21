<?php
Wind::import('WIND:mail.sender.IWindSendMail');
/**
 * 使用sendmail发送邮件
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$ 
 * @package wind.mail.sender
 */
class WindSendMail implements IWindSendMail {

	/**
	 * @var string sendmail命令路径
	 */
	private $sendMail = '/usr/sbin/sendmail';

	/**
	 * @var string 发送者
	 */
	private $sender = '';

	/**
	 * @var string 工作进程
	 */
	private $process = null;

	/**
	 * @param string $sendMail 工作进程
	 * @param string $sender 发送者
	 */
	public function __construct(array $config = null) {
		if (isset($config['sendMail'])) {
			$this->sendMail = $config['sendMail'];
		}
		if (isset($config['sender'])) {
			$this->sender = $config['sender'];
		}
	}

	/**
	 * 发送邮件
	 * @param WindMail $mail mail信息封装对象
	 * @return string
	 */
	public function send(WindMail $mail) {
		$this->open();
		$this->transData($mail->createHeader());
		$this->transData($mail->createBody());
		return $this->close() ? false : true;
	}

	/**
	 * 开启一个sendmail进进程
	 */
	public function open() {
		if ($this->sender) {
			$mailCmd = sprintf("%s -oi -f %s -t", escapeshellcmd($this->sendMail), escapeshellarg($this->sender));
		} else {
			$mailCmd = sprintf("%s -oi -t", escapeshellcmd($this->sendMail));
		}
		$this->process = popen($mailCmd, 'w');
	}

	/**
	 * 传输数据
	 * @param string $data 数据
	 */
	public function transData($data) {
		fputs($this->process, $data);
	}

	/**
	 * 关闭一个进程
	 * @return number
	 */
	public function close() {
		return pclose($this->process);
	}

	public function __destruct() {
		$this->process = null;
	}
}