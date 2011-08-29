<?php
Wind::import('COM:fitler.WindHandlerInterceptor');
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
	
	private $defaultMessage = '验证失败';

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
		if (!isset($this->validateRules['errorMessage']))
			$errorMessage = new WindErrorMessage();
		else {
			$errorMessage = $this->validateRules['errorMessage'];
			unset($this->validateRules['errorMessage']);
		}
		$_input = new stdClass();
		foreach ((array) $this->validateRules as $rule) {
			if (!is_array($rule))
				continue;
			$key = $rule['field'];
			$value = $this->request->getGet($key) ? $this->request->getGet($key) : $this->request->getPost(
				$key);
			$args = $rule['args'];
			array_unshift($args, $value);
			if (call_user_func_array(array($this->getValidator(), $rule['validator']), 
				(array) $args) === false) {
				if (null === $rule['default'])
					$errorMessage->addError(
						($rule['message'] ? $rule['message'] : $this->defaultMessage), $key);
				else
					$value = $rule['default'];
			}
			$this->request->setAttribute($key, $value);
			$_input->$key = $value;
		}
		if ($errorMessage->getError())
			$errorMessage->sendError();
		else
			$this->request->setAttribute('inputData', $_input);
	}

	/**
	 * 返回validator对象
	 * @throws WindException
	 * @return WindValidator
	 */
	private function getValidator() {
		if ($this->validator === null) {
			$_className = Wind::import($this->validatorClass);
			$this->validator = WindFactory::createInstance($_className);
			if ($this->validator === null)
				throw new WindException('validator', WindException::ERROR_RETURN_TYPE_ERROR);
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