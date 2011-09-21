<?php
/**
 * Enter description here ...
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$ 
 * @package wind.mail.sender
 */
interface IWindSendMail {

	/**
	 * 发送邮件
	 * @param WindMail $mail 邮件消息封装对象
	 */
	public function send(WindMail $mail);
}