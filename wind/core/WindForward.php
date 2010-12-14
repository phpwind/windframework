<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 操作转发类，将操作句柄转发给下一个操作或者转发给一个视图处理
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindForward {
	
	/* 模板视图信息 */
	private $templateName;
	private $templatePath;
	private $templateConfig;
	
	/* 布局信息 */
	private $layout = null;
	
	/* 操作处理请求 */
	private $action;
	private $actionPath;
	
	/* 页面重定向请求信息 */
	private $redirect;
	private $isRedirect = false;
	private $redirectArgs;
	private $redirecter = null;
	
	/* 模板变量信息 */
	private $vars = array();
	
	public function setVars($vars, $key = '') {
		if (!$key) {
			if (is_object($vars)) $vars = get_object_vars($vars);
			if (is_array($vars)) $this->vars += $vars;
		} else
			$this->vars[$key] = $vars;
		return;
	}
	
	/**
	 * 设置视图的逻辑名称
	 * 
	 * @param string $name
	 */
	
	public function setTemplateName($templateName) {
		$this->templateName = $templateName;
	}
	
	/**
	 * 设置视图的路径信息
	 * 
	 * @param string $path
	 */
	public function setTemplatePath($templatePath) {
		$this->templatePath = $templatePath;
	}
	
	/**
	 * 设置模板配置
	 * 
	 * @param string $templateConfigName
	 */
	public function setTemplateConfig($templateConfigName) {
		$this->templateConfig = $templateConfigName;
	}
	
	/**
	 * @param WindLayout $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}
	
	/**
	 * 设置视图的重定向信息
	 * 
	 * @param string $redirect
	 */
	public function setRedirect($redirect = '', $args = '') {
		$this->redirect = $redirect;
		$this->isRedirect = true;
		if ($this->redirecter === null) {
			$this->redirecter = new WindRedirecter();
		}
		$this->redirecter->setRedirect;
		$this->redirecter->setRedirectArgs($args);
	}
	
	/**
	 * @param $action the $action to set
	 * @author Qiong Wu
	 */
	public function setAction($action, $path = '', $isRedirect = false, $args = '') {
		$this->action = $action;
		$this->actionPath = $path;
		if ($isRedirect) $this->setRedirect('', $args);
	}
	
	/**
	 * 获得重定向参数信息
	 * @return the $redirectArgs
	 */
	public function getRedirectArgs() {
		return $this->redirectArgs;
	}
	
	/**
	 * 获得重定向链接
	 * @return string
	 */
	public function getRedirect() {
		return $this->redirect;
	}
	
	/**
	 * 返回视图的逻辑名称
	 * @return string
	 */
	public function getTemplateName() {
		return $this->templateName;
	}
	
	/**
	 * 返回视图的路径信息
	 * @return string
	 */
	public function getTemplatePath() {
		return $this->templatePath;
	}
	
	/**
	 * 获得Action操作句柄
	 * @return the $action
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * 获取Action请求的路径信息
	 * @return the $actionPath
	 */
	public function getActionPath() {
		return $this->actionPath;
	}
	
	/**
	 * 获取布局对象
	 * @return WindLayout
	 */
	public function getLayout() {
		return $this->layout;
	}
	
	/**
	 * 是否为重定向请求
	 * @return the $isRedirect
	 */
	public function isRedirect() {
		return $this->isRedirect;
	}
	
	/**
	 * 获得模板配置名称
	 * @return the $templateConfig
	 */
	public function getTemplateConfig() {
		return $this->templateConfig;
	}
	
	/**
	 * 获得操作输出数据变量
	 * @return array
	 */
	public function getVars() {
		return $this->vars;
	}
	
	/**
	 * @return WindRedirecter $redirecter
	 */
	public function getRedirecter() {
		$redirecter = new WindRedirecter($this);
		return $redirecter;
	}

}