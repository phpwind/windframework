<?php

L::import('WIND:core.filter.WindHandlerInterceptor');
/**
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindFormListener extends WindHandlerInterceptor {

	/**
	 * @var WindHttpRequest
	 */
	private $request = null;

	private $formPath = '';

	private $errorMessage = null;

	/**
	 * @param WindHttpRequest $request
	 * @param string $formPath
	 */
	public function __construct($request, $formPath) {
		$this->request = $request;
		$this->formPath = $formPath;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		//TODO 实现基于表单对象的表单验证机制，错误处理，表单对象集成WindEnableValidateModule
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {

	}

}

?>