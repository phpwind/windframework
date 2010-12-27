<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

L::import('WIND:core.WindMessage');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class WindErrorMessage extends WindMessage {
	private $errorAction = '';
	private $errorActionPath = '';
	private static $instance = null;
	
	private $forward = null;
	private $request;
	private $response;
	
	private function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
	}
	
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
	public function setErrorAction($action, $path) {
		$this->errorAction = $action;
		$this->errorActionPath = $path;
	}
	
	/**
	 * 重定向错误处理
	 */
	public function sendError() {
		if (count($this->getError()) == 0) return;
		if ($this->errorActionPath === '') {
			$this->errorActionPath = C::getErrorMessage(IWindConfig::ERRORMESSAGE_ERRORACTION);
		}
		if ($this->errorAction === '') {
			$this->errorAction = 'run';
		}
		if ($this->forward === null) {
			$this->forward = new WindForward();
			$this->forward->setAction($this->errorAction, $this->errorActionPath);
		}
		$this->response->getDispatcher()->setForward($this->forward)->dispatch(); 
		$this->clear(); 
		exit();
	}
	
	/**
	 * @return WindErrorMessage
	 */
	static public function getInstance($request, $response) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($request, $response);
		}
		return self::$instance;
	}

}