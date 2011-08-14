<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindRoute extends WindHandlerInterceptor {

	/**
	 * 根据匹配的路由规则，构建Url
	 * 
	 * @return string
	 */
	abstract public function build();

	/**
	 * 路由规则匹配方法，返回匹配到的参数列表
	 * 
	 * @return array
	 */
	abstract public function match();

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::handle()
	 */
	public function handle() {
		$args = func_get_args();
		$this->result = call_user_func_array(array($this, 'match'), $args);
		if ($this->result !== null) {
			return $this->result;
		}
		if (null !== ($handler = $this->interceptorChain->getHandler())) {
			$this->result = call_user_func_array(array($handler, 'handle'), $args);
		} else {
			$this->result = $this->interceptorChain->execute();
		}
		call_user_func_array(array($this, 'postHandle'), $args);
		return $this->result;
	}

}

?>