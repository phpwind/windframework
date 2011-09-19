<?php
/**
 * 模板视图异常类
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindFinalException extends Exception {

	/**
	 * 异常构造函数
	 * @param $message		     异常信息
	 * @param $code			     异常代号
	 * @param $innerException 内部异常
	 */
	public function __construct($message = '', $code = 0) {
		parent::__construct($message, $code);
	}
}

?>