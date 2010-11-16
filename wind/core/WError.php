<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 错误输出类
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WError {
	private static $error = array();
	
	/**
	 * 添加一条错误记录
	 * 
	 * @param string $message
	 * @param boolean $clear
	 */
	static public function addError($message, $clear = false) {
		self::$error[] = $message;
	}
	
	/**
	 * 清理所有错误记录
	 */
	static public function clearError() {
		self::$error = array();
	}
	
	/**
	 * 返回所有错误记录
	 */
	static public function getError() {
		return self::$error;
	}
	
	/**
	 * 中断处理，并立即输出错误消息
	 */
	static public function showMessage($message) {

	}

}