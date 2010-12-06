<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:component.message.WindMessage');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindErrorMessage extends WindMessage {
	private $errorAction = '';
	private static $instance = null;
	
	private function __construct() {}
	
	private $forward = null;
	
	/**
	 * 添加错误信息
	 * 
	 * @param string $message
	 * @param string $key
	 */
	public function addError($message, $key = '') {
		parent::addMessage($message, $key);
	}
	
	/**
	 * 返回错误信息
	 * 
	 * @param string $key
	 * @return Ambigous <string, multitype:>
	 */
	public function getError($key = '') {
		return parent::getMessage($key);
	}
	
	/**
	 * 设置错误处理操作
	 */
	public function setErrorAction($action = '') {
		$action && $this->errorAction = $action;
	}
	
	/**
	 * 重定向错误处理
	 */
	public function sendError() {
		if (count($this->getError()) == 0) return;
		if ($this->errorAction === '') {
			$this->errorAction = C::getErrorMessage(IWindConfig::ERRORMESSAGE_ERRORACTION);
		}
		if ($this->forward === null) {
			$this->forward = new WindModelAndView();
			$this->forward->setAction('run', $this->errorAction);
		}
		WindDispatcher::getInstance()->initWithMav($this->forward)->dispatch();
		exit();
	}
	
	/**
	 * @return WindErrorMessage
	 */
	static public function &getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}

}