<?php
/**
 * 错误处理类接口定义
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindErrorMessage {

	/**
	 * 添加错误信息
	 * @param string $message
	 * @param string $key
	 */
	public function addError($message, $key = '');

	/**
	 * 获得一条Error信息
	 * @param string $key
	 */
	public function getError($key = '');

	/**
	 * 清空Error信息
	 */
	public function clearError();

	/**
	 * 发送错误信息
	 */
	public function sendError();
}

?>