<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 用户定义session存储机制
 * the last known user to change this file in the repository  <$LastChangedBy: weihu $>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id: AbstractWindUserSession.php 1704 2011-03-08 10:40:17Z weihu $ 
 * @package 
 */
abstract class  AbstractWindSession extends WindModule {
	protected $handler = null;
	
	/**
	 * 打开会话存储机制
	 * @param string $savePath
	 * @param string $sessionName
	 * @return  bollean
	 */
	public abstract function open($savePath, $sessionName);
	/**
	 * 关闭会话存储存储机制
	 * @return  bollean
	 */
	public abstract function close();
	/**
	 * 将sessionID对应的数据写到存储
	 * @param string $name
	 * @param mixed $value
	 */
	public abstract function write($sessId, $sessData);
	/**
	 * 从存储中装载session数据
	 * @param mixed $sessid
	 */
	public abstract function read($sessId);
	/**
	 * 对存储系统中的数据进行垃圾收集
	 * @param mixed $maxlifetime
	 */
	public abstract function gc($maxlifetime);
	/**
	 * 破坏与指定的会话ID相关联的数据
	 * @param mixed $name
	 */
	public abstract function destroy($sessId);
	
	/**
	 * 数据序列化
	 * @param mixed $sessData
	 * @return string
	 */
	protected function serializeData($sessData) {
		return (is_array($sessData) || is_object($sessData)) ? serialize($sessData) : $sessData;
	}
	
	/**
	 * 数据反序列化
	 * @param string $sessData
	 * @return mixed
	 */
	protected function unserializeData($sessData) {
		$data = unserialize($sessData);
		return (is_array($data) || is_object($data)) ? $data : $sessData;
	}
	
	/**
	 * 设置监听接口
	 */
	public function setSessionHandler(){
		session_set_save_handler(array($this,'open'),array($this,'close'),array($this,'read'),array($this,'write'),array($this,'destroy'),array($this,'gc'));
	}
	

	/**
	 * 设置链接对象
	 * @param AbstractWindCache $handler
	 */
	public function setHandler($handler) {
		if ($handler instanceof AbstractWindCache)
			$this->handler = $handler;
	}
	
	/**
	 * 获得链接对象
	 * @return AbstractWindCache
	 */
	public function getHandler() {
		return $this->_getHandler();
	}
}

