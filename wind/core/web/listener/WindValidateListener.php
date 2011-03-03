<?php

L::import('WIND:core.filter.WindHandlerInterceptor');
/**
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindValidateListener extends WindHandlerInterceptor {

	/**
	 * @var WindHttpRequest
	 */
	private $request = null;

	private $validateRules = array();
	
	private $validator = null;

	private $validatorClass = '';

	private $errorMessage = null;

	/**
	 * @param WindHttpRequest $request
	 * @param array $validateRules
	 * @param string $validatorClass
	 */
	public function __construct($request, $validateRules, $validatorClass) {
		$this->request = $request;
		$this->validateRules = (array) $validateRules;
		$this->validatorClass = $validatorClass;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		$errorMessage = new WindErrorMessage();
		foreach ((array)$this->validateRules as $rule) {
		    $key = $rule['field'];
		    $value = $this->request->getGet($key) ? $this->request->getGet($key) : $this->request->getPost($key);
		    $args = $rule['args'];
		    array_unshift($args, $value);
		    if (call_user_func_array(array($this->getValidator(), $rule['validator']), (array)$args) !== false) {
    		    continue;
		    }
		    if (null === $rule['default']) {
		        $errorMessage->addError($key . ': ' . $rule['message'], $key);
		        continue;
    		}
		    $this->request->setAttribute($key, $rule['default']);
		}
		if ($errorMessage->getError()) $errorMessage->sendError();
	}
	
	private function getValidator() {
	    if ($this->validator === null) {
			$_className = L::import($this->validatorClass);
			L::import('WIND:core.factory.WindFactory');
			$this->validator = WindFactory::createInstance($_className);
			if ($this->validator === null) throw new WindException('validator', WindException::ERROR_RETURN_TYPE_ERROR);
		}
		return $this->validator;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {

	}

}

?>