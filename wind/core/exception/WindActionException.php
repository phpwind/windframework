<?php
/**
 * 模板视图异常类
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindActionException extends WindException {
	private $error;

	/**
	 * @param WindErrorMessage $error
	 */
	public function __construct($error) {
		$this->setError($error);
		parent::__construct('');
	}

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

	/**
	 * @return WindErrorMessage $error
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * @param WindErrorMessage $error
	 */
	public function setError($error) {
		$this->error = $error;
	}
}
?>