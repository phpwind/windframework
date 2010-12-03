<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindModelAndView {
	/* 模板视图信息 */
	private $viewName;
	private $path;
	
	/* 页面重定向请求信息 */
	private $redirect;
	
	/* 操作处理请求 */
	private $action;
	private $actionPath;
	
	/**
	 * 视图预处理类
	 * @var WindView
	 */
	private $view = null;
	
	/* 布局信息 */
	private $layoutMapping = array();
	private $layout = null;
	
	/**
	 * @param WindHttpRequest $request //path to which control should be forwarded or redirected
	 * @param WindHttpResponse $response //should we do a redirect
	 * @param string $module //module prefix
	 */
	public function __construct() {}
	
	/**
	 * 接收一个layout对象初始化ModelAndView
	 * @param WindLayout $layout
	 */
	public function setLayout($layout) {
		if ($layout instanceof WindLayout) {
			$this->layout = $layout;
		} else
			throw new WindException('object type error.');
	}
	
	/**
	 * @return WindLayout
	 */
	public function &getLayout() {
		return $this->layout;
	}
	
	public function getLayoutMapping() {
		return $this->layoutMapping;
	}
	
	/**
	 * 设置视图的重定向信息
	 * 
	 * @param string $redirect
	 */
	public function setRedirect($redirect) {
		if (!$redirect) return;
		$this->redirect = $redirect;
	}
	
	public function getRedirect() {
		return $this->redirect;
	}
	
	/**
	 * 设置视图的逻辑名称
	 * 
	 * @param string $name
	 */
	public function setViewName($viewName, $key = 'current') {
		if (!$viewName) return;
		$this->layoutMapping['key_' . $key] = $viewName;
		$this->viewName = $viewName;
	}
	
	/**
	 * 返回视图的逻辑名称
	 * 
	 * @return string
	 */
	public function getViewName() {
		return $this->viewName;
	}
	
	/**
	 * 设置view对象
	 * 
	 * @param WindView $view
	 */
	public function setView($view = null) {
		$this->view = $view;
	}
	
	/**
	 * 返回WindView对象
	 * 
	 * @return WindView
	 */
	public function getView() {
		if ($this->view == null) {
			L::import('WIND:component.viewer.WindView');
			$this->view = new WindView();
			$this->view->setViewWithObject($this);
		}
		return $this->view;
	}
	
	/**
	 * 设置视图的路径信息
	 * 
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}
	
	/**
	 * 返回视图的路径信息
	 * 
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * @return the $action
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * @param $action the $action to set
	 * @author Qiong Wu
	 */
	public function setAction($action, $path = '') {
		$this->action = $action;
		if ($path) $this->setActionPath($path);
	}
	
	/**
	 * @return the $actionPath
	 */
	public function getActionPath() {
		return $this->actionPath;
	}
	
	/**
	 * @param $actionPath the $actionPath to set
	 * @author Qiong Wu
	 */
	public function setActionPath($actionPath) {
		$this->actionPath = $actionPath;
	}

}