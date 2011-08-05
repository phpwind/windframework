<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindHandlerInterceptorChain extends WindModule {
	protected $_interceptors = array();
	protected $_callBack = null;
	protected $_args = array();
	protected $_state = true;

	/**
	 * 设置回调方法
	 * 
	 * @param string|array $callBack
	 * @param array $args
	 * @return
	 */
	public function setCallBack($callBack, $args = array()) {
		$this->_callBack = $callBack;
		$this->_args = $args;
	}

	/**
	 * 执行callback方法
	 * 
	 * @throws WindException
	 * @return void|mixed
	 */
	public function execute() {
		if ($this->_callBack === null) return null;
		if (is_string($this->_callBack) && !function_exists($this->_callBack)) {
			throw new WindException('[core.filter.WindHandlerInterceptorChain.execute]' . $this->_callBack, 
				WindException::ERROR_FUNCTION_NOT_EXIST);
		}
		return call_user_func_array($this->_callBack, (array) $this->_args);
	}

	/**
	 * 返回处理句柄
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
		Wind::log(
			'[core.filter.WindHandlerInterceptorChain.getHandler] the type of Interceptor ' . gettype($handler) .
				 ' is not supported.', WindLogger::LEVEL_DEBUG, 'wind.core');
		return $this->getHandler();
	}

	/**
	 * 添加过滤连中的拦截器对象, 支持数组和对象两种类型
	 * 
	 * @param $interceptors
	 * @return 
	 */
	public function addInterceptors($interceptors) {
		if (is_array($interceptors))
			$this->_interceptors += $interceptors;
		else
			$this->_interceptors[] = $interceptors;
	}
	
	/**
	 * 重置初始化信息
	 * @return boolean
	 */
	public function reset() {
		$this->_interceptors = array();
		$this->_callBack = null;
		$this->_args = array();
		$this->_state = true;
		return true;
	}
}
?>