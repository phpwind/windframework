<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

L::import('WIND:core.web.IWindErrorMessage');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class WindErrorMessage implements IWindErrorMessage {

	private $error = array();

	private $errorAction = 'run';

	private $errorController = 'WIND:core.web.WindErrorHandler';

	/* (non-PHPdoc)
	 * @see IWindErrorMessage::sendError()
	 */
	public function sendError() {
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
		if ($key === '') {
			if (is_string($error))
				$this->error[] = $error;
			elseif (is_object($error))
				$error = get_object_vars($error);
			if (is_array($error)) $this->error += $error;
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
		if ($errorAction) $this->errorAction = $errorAction;
	}

	/**
	 * @param field_type $errorController
	 */
	public function setErrorController($errorController) {
		if ($errorController) $this->errorController = $errorController;
	}

}