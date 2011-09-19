<?php
/**
 * 模板视图异常类
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindViewException extends WindException {
	
	const VIEW_NOT_EXIST = '300';
	const COMPILE_NOT_EXIST = '400';

	/**
	 * 自定义异常号的对应异常信息
	 * 
	 * @param int $code  异常号
	 * @return string 返回异常号对应的异常组装信息原型
	 */
	protected function messageMapper($code) {
		$messages[self::VIEW_NOT_EXIST] = 'Not exist view template file or Incorrect file path \'$message\'';
		$messages[self::COMPILE_NOT_EXIST] = 'Not exist view compile file or Incorrect file path \'$message\'';
		return isset($messages[$code]) ? $messages[$code] : '$message';
	}
}

?>