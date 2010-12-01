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
	
	private $mav = null;
	
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
	 * 
	 */
	public function setErrorAction($action = '') {
		$this->errorAction = $action;
	}
	
	/**
	 * 向指定的模板页输出Error
	 */
	public function showError() {
		$this->sendError();
	}
	
	/**
	 * 重定向错误处理
	 */
	public function sendError() {
		if ($this->errorAction === '') {
			$this->errorAction = C::getErrorMessage(IWindConfig::ERRORMESSAGE_ERRORACTION);
		}
		if ($this->mav === null) {
			$this->mav = new WindModelAndView();
			$this->mav->setAction('run', $this->errorAction);
		}
		WindDispatcher::getInstance()->setMav($this->mav)->dispatch();
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