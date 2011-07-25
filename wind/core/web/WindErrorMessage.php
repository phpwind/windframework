<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class WindErrorMessage extends WindModule implements IWindErrorMessage {
	private $error = array();
	private $errorAction = 'run';
	/**
	 * @var string
	 */
	private $errorController = 'windError';

	/**
	 * @param string $message
	 */
	public function __construct($message = '', $errorAction = '', $errorController = '') {
		$this->addError($message);
		$this->setErrorAction($errorAction);
		$this->setErrorController($errorController);
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
		else
			return isset($this->error[$key]) ? $this->error[$key] : '';
	}

	/* (non-PHPdoc)
	 * @see IWindErrorMessage::addError()
	 */
	public function addError($error, $key = '') {
		if (!$error)
			return;
		if ($key === '') {
			if (is_string($error))
				$this->error[] = $error;
			elseif (is_object($error))
				$error = get_object_vars($error);
			if (is_array($error))
				$this->error += $error;
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
	 * @return the $errorController
	 */
	public function getErrorController() {
		return $this->errorController;
	}

	/**
	 * @param field_type $errorAction
	 */
	public function setErrorAction($errorAction) {
		if ($errorAction)
			$this->errorAction = $errorAction;
	}

	/**
	 * @param field_type $errorController
	 */
	public function setErrorController($errorController) {
		if ($errorController)
			$this->errorController = $errorController;
	}
}