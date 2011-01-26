<?php

L::import('WIND:core.WindComponentModule');
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindHandlerInterceptorChain extends WindComponentModule {

	protected $_interceptors = array();

	protected $_callBack = null;

	protected $_args = array();

	private $_state = true;

	/**
	 * Enter description here ...
	 */
	public function setCallBack($callBack, $args = array()) {
		$this->_callBack = $callBack;
		$this->_args = $args;
	}

	/**
	 * Enter description here ...
	 * 
	 * @throws WindException
	 * @return void|mixed
	 */
	public function execute() {
		if ($this->_callBack === null) return null;
		if (is_string($this->_callBack) && !function_exists($this->_callBack)) throw new WindException($this->_callBack, WindException::ERROR_FUNCTION_NOT_EXIST);
		
		return call_user_func_array($this->_callBack, (array) $this->_args);
	}

	/**
	 * Enter description here ...
	 * 
	 * @return WindHandlerInterceptor
	 */
	public function getHandler() {
		if ($this->_state) {
			$this->addInterceptors(new WindHandlerInterceptor());
			$this->_state = false;
		}
		if (count($this->_interceptors) <= 0) return null;
		
		$handler = array_shift($this->_interceptors);
		if ($handler instanceof WindHandlerInterceptor) {
			$handler->setHandlerInterceptorChain($this);
			return $handler;
		}
		return $this->getHandler();
	}

	/**
	 * Enter description here ...
	 * 
	 * @param $interceptors
	 */
	public function addInterceptors($interceptors) {
		if (is_array($interceptors))
			$this->_interceptors += $interceptors;
		else
			$this->_interceptors[] = $interceptors;
	}

}

?>