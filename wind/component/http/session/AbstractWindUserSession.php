<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 用户定义session存储机制
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class  AbstractWindUserSession {
	
	/**
	 * 打开会话存储机制
	 * @param string $savePath
	 * @param string $sessionName
	 * @return  bollean
	 */
	public static abstract function open($savePath, $sessionName);
	/**
	 * 关闭会话存储存储机制
	 * @return  bollean
	 */
	public static abstract function close();
	/**
	 * 将sessionID对应的数据写到存储
	 * @param string $name
	 * @param mixed $value
	 */
	public static abstract function write($name,$value);
	/**
	 * 从存储中装载session数据
	 * @param mixed $sessid
	 */
	public static abstract function read($name);
	/**
	 * 对存储系统中的数据进行垃圾收集
	 * @param mixed $maxlifetime
	 */
	public static abstract function gc($maxlifetime);
	/**
	 * 破坏与指定的会话ID相关联的数据
	 * @param mixed $name
	 */
	public static abstract function destroy($name);
	
	public static function callUserSessionHandler(){
		$className = get_class($this);
		session_set_save_handler(array($className,'open'),array($className,'close'),array($className,'read'),array($className,'write'),array($className,'destroy'),array($className,'gc'));
	}
}

