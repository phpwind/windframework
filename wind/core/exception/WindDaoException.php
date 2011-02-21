<?php

L::import('WIND:core.exception.WindException');
/**
 * 模板视图异常类
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindDaoException extends WindException {

	/**
	 * 自定义异常号的对应异常信息
	 * 
	 * @param int $code  异常号
	 * @return string 返回异常号对应的异常组装信息原型
	 */
	protected function messageMapper($code) {
		$messages = array();
		
		return isset($messages[$code]) ? $messages[$code] : '$message';
	}
}

?>