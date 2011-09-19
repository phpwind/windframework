<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class AbstractWindRoute extends WindHandlerInterceptor {
	protected $pattern = '';
	protected $reverse = '';
	protected $params = array();

	/**
	 * 根据匹配的路由规则，构建Url
	 * @param AbstractWindRouter $router
	 * @param string $action
	 * @param array $args
	 * @return string
	 */
	abstract public function build($router, $action, $args = array());

	/**
	 * 路由规则匹配方法，返回匹配到的参数列表
	 * @return array
	 */
	abstract public function match();

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		return $this->match();
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->pattern = $this->getConfig('pattern', '', $this->pattern); //trim($this->getConfig('regex'), '/');
		$this->reverse = $this->getConfig('reverse', '', $this->reverse); //trim($this->getConfig('reverse'), '/');
		$this->params = $this->getConfig('params', '', $this->params);
	}
}

?>