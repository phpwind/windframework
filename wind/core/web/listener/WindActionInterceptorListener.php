<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindActionInterceptorListener extends WindHandlerInterceptor {
	/**
	 * @var WindHttpResponse
	 */
	protected $response;
	/**
	 * @var WindHttpRequest
	 */
	protected $request;
	/**
	 * @var array
	 */
	protected $interceptors = array();

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param array $interceptors
	 */
	public function __construct($request, $response, $interceptors) {
		$this->request = $request;
		$this->response = $response;
		$this->interceptors = $interceptors;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {}
}

?>