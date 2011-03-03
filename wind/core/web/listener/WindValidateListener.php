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
		//TODO 实现基于验证规则的表单验证机制，错误处理
	
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {

	}

}

?>