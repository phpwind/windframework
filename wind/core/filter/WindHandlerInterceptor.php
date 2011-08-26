<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class WindHandlerInterceptor extends WindModule {
	protected $result = null;
	protected $interceptorChain = null;

	abstract public function preHandle();

	abstract public function postHandle();

	/**
	 * @return mixed
	 */
	public function handle() {
		$args = func_get_args();
		$this->result = call_user_func_array(array($this, 'preHandle'), $args);
		if ($this->result !== null) {
			return $this->result;
		}
		if (null !== ($handler = $this->interceptorChain->getHandler())) {
			$this->result = call_user_func_array(array($handler, 'handle'), $args);
		} else {
			$this->result = $this->interceptorChain->handle();
		}
		call_user_func_array(array($this, 'postHandle'), $args);
		return $this->result;
	}

	/**
	 * 设置过滤链对象
	 * 
	 * @param WindHandlerInterceptorChain $interceptorChain
	 */
	public function setHandlerInterceptorChain($interceptorChain) {
		$this->interceptorChain = $interceptorChain;
	}
}
?>