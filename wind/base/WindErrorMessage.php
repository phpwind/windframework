<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class WindErrorMessage extends WindModule implements IWindErrorMessage {
	private $error = array();
	private $errorAction;

	/**
	 * @param string $message
	 */
	public function __construct($message = '', $errorAction = '') {
		$message !== '' && $this->addError($message);
		$errorAction !== '' && $this->setErrorAction($errorAction);
	}

	/* (non-PHPdoc)
	 * @see IWindErrorMessage::sendError()
	 */
	public function sendError() {
		if (empty($this->error))
			return;
		throw new WindActionException($this);
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
		return isset($this->error[$key]) ? $this->error[$key] : '';
	}

	/* (non-PHPdoc)
	 * @see IWindErrorMessage::addError()
	 */
	public function addError($error, $key = '') {
		if ($key === '') {
			if (is_string($error))
				$this->error[] = $error;
			elseif (is_object($error))
				$error = get_object_vars($error);
			if (is_array($error))
				$this->error = array_merge($this->error, $error);
		} else
			$this->error[$key] = $error;
	}

	/**
	 * @return the $errorAction
	 */
	public function getErrorAction() {
		return $this->errorAction;
	}

	/**
	 * /module/controller/action/?a=b&c=a
	 * @param string $errorAction
	 */
	public function setErrorAction($errorAction) {
		$this->errorAction = $errorAction;
	}

}

/**
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