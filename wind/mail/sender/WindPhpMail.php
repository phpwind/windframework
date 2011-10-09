<?php
Wind::import('WIND:mail.sender.IWindSendMail');
/**
 * 使用php内部函数发送邮件
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$ 
 * @package mail
 * @subpackage sender
 */
class WindPhpMail implements IWindSendMail {

	public function send(WindMail $mail) {
		$recipients = $mail->getRecipients();
		$to = $this->getToAsString($recipients);
		return mail($to, $mail->getSubject(), $mail->createBody(), $mail->createHeader());
	}

	public function getToAsString($recipients = array()) {
		$to = '';
		foreach ($recipients as $key => $value) {
			$_value = is_string($key) ? $key . ' ' . $_value : $_value;
			$to .= $to ? ', ' . $_value : $_value;
		}
		return $to;
	
	}
}