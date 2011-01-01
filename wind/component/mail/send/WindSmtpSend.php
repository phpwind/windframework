<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */

class WindSmtpSend{
	protected $smtp = null;
	public function __construct($host,$port){
		$this->smtp = new WindSmtp($host,$port);
	}
	
	public function send(WindMail $mailBuilder){
		
	}
}