<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class WindError {
	private static $error = array();
	
	/**
	 * 添加一条错误记录
	 * 
	 * @param string $message
	 * @param boolean $clear
	 */
	public function addError($message, $clear = false) {
		self::$error[] = $message;
	}
	
	/**
	 * 清理所有错误记录
	 */
	public function clearError() {
		self::$error = array();
	}
	
	/**
	 * 返回所有错误记录
	 */
	public function getError() {
		return self::$error;
	}
	
	/**
	 * 中断处理，并立即输出错误消息
	 */
	public function showMessage($message) {

	}
	
	static public function getInstance() {

	}

}