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
	private $viewPath = '';
	private $isRedirect = false;
	private $redirect = '';
	private $model = '';
	private $view = null;
	
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
			foreach ($model as $key => $value) {
				$this->model[$key] = $value;
			}
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
	public function setViewName($viewName) {
		if (!$viewName) return;
		$this->viewName = $viewName;
	}
	
	public function getViewName() {
		return $this->viewName;
	}
	
	public function setView($view = null) {
		$this->view = $view;
	}
	
	public function getView() {
		return $this->view;
	}
	
	/**
	 * 设置视图的路径信息
	 * 
	 * @param string $path
	 */
	public function setViewPath($viewPath) {
		if ($viewPath) return;
		$this->viewPath = $viewPath;
	}
	
	public function getViewPath() {
		return $this->viewPath;
	}
	
	/**
	 * 清理该类的状态信息
	 */
	public function clear() {
		$this->viewName = '';
		$this->viewPath = '';
		$this->isRedirect = false;
		$this->redirect = '';
		$this->model = null;
		$this->view = null;
	}

}