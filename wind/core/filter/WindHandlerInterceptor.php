<?php

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindHandlerInterceptor {

	protected $result;

	/**
	 * Enter description here ...
	 */
	public function preHandle() {

	}

	/**
	 * Enter description here ...
	 */
	public function postHandle() {

	}

	/**
	 * Enter description here ...
	 * @return mixed
	 */
	public function handle() {
		$args = func_get_args();
		call_user_func_array(array($this, 'preHandle'), $args);
		if (null !== ($handler = $this->interceptorChain->getHandler())) {
			call_user_func_array(array($handler, 'handle'), $args);
		} else {
			$this->result = $this->interceptorChain->execute();
		}
		call_user_func_array(array($this, 'postHandle'), $args);
		return $this->result;
	}

	/**
	 * @param WindHandlerInterceptorChain $interceptorChain
	 */
	public function setHandlerInterceptorChain($interceptorChain) {
		if ($interceptorChain instanceof WindComponentModule) {
			$attributes = $interceptorChain->getAttribute();
			foreach ($attributes as $key => $value) {
				$this->$key = $value;
			}
		}
		$this->interceptorChain = $interceptorChain;
	}

}

?>