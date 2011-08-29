<?php
/**
 * 操作转发类，将操作句柄转发给下一个操作或者转发给一个视图处理
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindForward extends WindModule {
	/**
	 * 定义视图处理器
	 * @var WindView
	 */
	protected $windView = null;
	/**
	 * 模板变量信息
	 * @var array
	 */
	private $vars = array('G' => array());
	/**
	 * 是否为Action请求
	 * @var boolean
	 */
	private $isReAction = false;
	/**
	 * 是否是重定向请求
	 * @var boolean
	 */
	private $isRedirect = false;
	/**
	 * 跳转链接
	 * @var string
	 */
	private $url;
	private $action;
	private $controller;
	private $args;

	/**
	 * 将请求重定向到另外一个Action操作
	 * @param string $action | $action 操作
	 * @param string $controller | controller 路径 , controller 为空是则指向当前的控制器
	 * @param array $args | 参数
	 * @param boolean $isRedirect | 是否重定向
	 * 
	 * @return
	 */
	public function forwardAnotherAction($action = 'run', $controller = '', $args = array(), $isRedirect = false) {
		$this->setIsReAction(true);
		$this->setAction($action);
		$this->setController($controller);
		$this->setArgs($args);
		$this->setIsRedirect($isRedirect);
	}

	/**
	 * 将请求重定向到另外一个Action操作
	 * $action参数支持:
	 * module/controller/action/?a=&b=&c=
	 * 
	 * @param string $action | $action 操作
	 * @param array $args | 参数
	 * @param boolean $isRedirect | 是否重定向
	 * @return
	 */
	public function forwardAction($action, $args = array(), $isRedirect = false) {
		$this->setIsReAction(true);
		$this->setAction($action);
		$this->setArgs($args);
		$this->setIsRedirect($isRedirect);
	}

	/**
	 * 设置页面模板变量
	 * 
	 * @param string|array|object $vars
	 * @param string $key
	 */
	public function setVars($vars, $key = '', $isG = false) {
		if (!$key) {
			if (is_object($vars))
				$vars = get_object_vars($vars);
			if (is_array($vars))
				if ($isG)
					$this->vars['G'] = $vars;
				else
					$this->vars += $vars;
		} else {
			if ($isG)
				$this->vars['G'][$key] = $vars;
			else
				$this->vars[$key] = $vars;
		}
	}

	/**
	 * @return the $isRedirect
	 */
	public function getIsRedirect() {
		return $this->isRedirect;
	}

	/**
	 * @param boolean $isRedirect
	 */
	public function setIsRedirect($isRedirect) {
		$this->isRedirect = $isRedirect;
	}

	/**
	 * @return the $isReAction
	 */
	public function getIsReAction() {
		return $this->isReAction;
	}

	/**
	 * @param boolean $isReAction
	 */
	public function setIsReAction($isReAction) {
		$this->isReAction = $isReAction;
	}

	/**
	 * @return the $vars
	 */
	public function getVars() {
		return $this->vars;
	}

	/**
	 * @return the $url
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return the $action
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @return the $controller
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * @return the $args
	 */
	public function getArgs() {
		return $this->args;
	}

	/**
	 * @param field_type $action
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * @param field_type $controller
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * @param field_type $args
	 */
	public function setArgs($args) {
		$this->args = $args;
	}

	/**
	 * @return WindView
	 */
	public function getWindView() {
		if ($this->windView === null)
			$this->_getWindView();
		$module = Wind::getApp()->getModules();
		isset($module['template-dir']) && $this->windView->templateDir = $module['template-dir'];
		isset($module['compile-dir']) && $this->windView->compileDir = $module['compile-dir'];
		return $this->windView;
	}

	/**
	 * @param WindView $windView
	 */
	public function setWindView($windView) {
		$this->windView = $windView;
	}
}