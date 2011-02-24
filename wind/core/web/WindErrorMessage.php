<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

L::import('WIND:core.WindMessage');
L::import('WIND:core.web.IWindErrorMessage');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class WindErrorMessage implements IWindErrorMessage {

	private $error = array();

	private $errorAction = '';

	private $errorController = '';

	/* (non-PHPdoc)
	 * @see IWindErrorMessage::sendError()
	 */
	public function sendError() {
		$error = implode(',', $this->error);
	}

	/**
	 * 设置错误处理方法
	 * 
	 * @param string $action
	 * @param string $controller
	 */
	public function setErrorAction($action, $controller) {
		$this->errorAction = $action;
		$this->errorController = $controller;
	}

	/* (non-PHPdoc)
	 * @see IWindErrorMessage::clearError()
	 */
	public function clearError() {
		$this->error = array();
	}

	/* (non-PHPdoc)
	 * @see IWindErrorMessage::getError()
	 */
	public function getError($key = '') {
		if ($key === '')
			return $this->error;
		else
			return isset($this->error[$key]) ? $this->error[$key] : '';
	}

	/* (non-PHPdoc)
	 * @see IWindErrorMessage::addError()
	 */
	public function addError($error, $key = '') {
		if ($key === '') {
			if (is_object($error)) $error = get_object_vars($error);
			if (is_array($error)) $this->error += $error;
		} else
			$this->error[$key] = $error;
	}

}