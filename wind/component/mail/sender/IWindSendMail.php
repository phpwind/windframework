<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
interface IWindSendMail{
	/**
	 * 发送邮件
	 * @param WindMail $mail 邮件消息封装对象
	 */
	public function send(WindMail $mail);
}