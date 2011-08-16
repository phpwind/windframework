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
	 *
	 * @var string
	 */
	private $windView;
	/**
	 * 模板名称
	 *
	 * @var strig
	 */
	private $templateName;
	/**
	 * 模板路径
	 *
	 * @var string
	 */
	private $templatePath = null;
	/**
	 * 模板扩展名
	 *
	 * @var string
	 */
	private $templateExt = null;
	/**
	 * 模板布局
	 *
	 * @var string
	 */
	private $layout;
	/**
	 * 模板变量信息
	 * 
	 * @var array
	 */
	private $vars = array();
	/**
	 * 是否为Action请求
	 * 
	 * @var boolean
	 */
	private $isReAction = false;
	/**
	 * 是否是重定向请求
	 * 
	 * @var boolean
	 */
	private $isRedirect = false;
	/**
	 * 跳转链接
	 * 
	 * @var string
	 */
	private $url;
	private $action;
	private $controller;
	private $args;
	private $display = false;

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
		return;
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
	 * @return the $templateName
	 */
	public function getTemplateName() {
		return $this->templateName;
	}

	/**
	 * @return the $templatePath
	 */
	public function getTemplatePath() {
		return $this->templatePath;
	}

	/**
	 * @return the $templateExt
	 */
	public function getTemplateExt() {
		return $this->templateExt;
	}

	/**
	 * @return the $layout
	 */
	public function getLayout() {
		return $this->layout;
	}

	/**
	 * @param strig $templateName
	 */
	public function setTemplateName($templateName) {
		$this->templateName = $templateName;
	}

	/**
	 * @param string $templatePath
	 */
	public function setTemplatePath($templatePath) {
		$this->templatePath = $templatePath;
	}

	/**
	 * @param string $templateExt
	 */
	public function setTemplateExt($templateExt) {
		$this->templateExt = $templateExt;
	}

	/**
	 * @param string $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}

	/**
	 * @return string
	 */
	public function getWindView() {
		return $this->windView;
	}

	/**
	 * @param string $windView
	 */
	public function setWindView($windView) {
		$this->windView = $windView;
	}

	/**
	 * @return the $display
	 */
	public function getDisplay() {
		return $this->display;
	}

	/**
	 * @param field_type $display
	 */
	public function setDisplay($display) {
		$this->display = $display;
	}

}