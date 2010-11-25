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
	private $viewName = '';
	private $path = '';
	private $isRedirect = false;
	private $redirect = '';
	private $model = array();
	private $view = null;
	
	private $layoutMapping = array();
	private $layout = null;
	
	/**
	 * @param string $name //name of this forward
	 * @param string $path //path to which control should be forwarded or redirected
	 * @param boolean $redirect //should we do a redirect
	 * @param string $module //module prefix
	 */
	public function __construct($viewName = '', $redirect = '') {
		$this->setViewName($viewName);
		$this->setRedirect($redirect);
	}
	
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
	 * 返回全局数据对象
	 * @return array
	 */
	public function getModel() {
		return $this->model;
	}
	
	/**
	 * 设置变量信息
	 * 
	 * @param object|array|string $model
	 */
	public function setModel($model, $key = '') {
		if (is_array($model))
			$this->setModelWithArray($model, $key);
		elseif (is_object($model))
			$this->setModelWithObject($model, $key);
		else
			$this->setModelWithSimple($model, $key);
	}
	
	/**
	 * @param $model
	 * @param string $key
	 */
	public function setModelWithSimple($model, $key = '') {
		if (!$key) return;
		$this->model[$key] = $model;
	}
	
	/**
	 * @param object $model
	 * @param string $key
	 */
	public function setModelWithObject($model, $key = '') {
		if (!is_object($model)) return;
		if ($key && is_string($key))
			$this->model[$key] = $this->model;
		else
			$this->model += get_object_vars($model);
	}
	
	/**
	 * 设置视图变量信息
	 * 
	 * @param array $model
	 */
	public function setModelWithArray($model, $key = '') {
		if (!is_array($model)) return;
		if ($key && is_string($key))
			$this->model[$key] = $model;
		else
			$this->model += $model;
	}
	
	/**
	 * 返回是否为重定向链接
	 * 
	 * @return string
	 */
	public function isRedirect() {
		return $this->isRedirect;
	}
	
	/**
	 * 设置视图的重定向信息
	 * 
	 * @param string $redirect
	 */
	public function setRedirect($redirect) {
		if (!$redirect) return;
		$this->redirect = $redirect;
		$this->isRedirect = true;
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
		if ($path) return;
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

}