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
	private $vars = array();
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
	private $args = array();

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
	public function setVars($vars, $key = '') {
		if (!$key) {
			if (is_object($vars))
				$vars = get_object_vars($vars);
			if (is_array($vars))
				$this->vars += $vars;
		} else
			$this->vars[$key] = $vars;
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
		$_tmp = $this->vars;
		foreach (func_get_args() as $arg) {
			if (is_array($_tmp) && isset($_tmp[$arg]))
				$_tmp = $_tmp[$arg];
			else
				return '';
		}
		return $_tmp;
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