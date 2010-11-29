<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-24
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
class WindDispatcher {
	private $action = 'run';
	private $controller = 'index';
	private $module = 'apps';
	
	private $path = '';
	private $mav = null;
	private $router = null;
	
	private static $instance = null;
	
	/**
	 * @param WindModelAndView $mav
	 */
	public function __construct() {}
	
	/**
	 * 请求分发处理
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function dispatch($request, $response) {
		if ($this->getMav() === null) throw new WindException('dispatch error.');
		if (($redirect = $this->getMav()->getRedirect()) !== '')
			$this->_dispatchWithRedirect($redirect, $request, $response);
		
		elseif (($action = $this->getMav()->getAction()) !== '')
			$this->_dispatchWithAction($action, $request, $response);
		
		else
			$this->_dispatchWithTemplate($request, $response);
		return;
	}
	
	/**
	 * 请求分发一个重定向请求
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithRedirect($redirect, $request, $response) {
		$response->sendRedirect($redirect);
	}
	
	/**
	 * 请求分发一个操作请求
	 * @param String $action
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithAction($action, $request, $response) {
		if (!$action) throw new WindException('action handled is empty.');
		$this->action = $action;
		$this->path = $this->getMav()->getPath();
		WindFrontController::getInstance()->getApplicationHandle()->processRequest($request, $response);
	}
	
	/**
	 * 请求分发一个模板请求
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithTemplate($request, $response) {
		$viewer = $this->getMav()->getView()->createViewerResolver();
		$viewer->windAssign($response->getData());
		$response->setBody($viewer->windFetch());
	}
	
	/**
	 * 返回一个ModelAndView对象
	 * @return WindModelAndView $mav
	 */
	public function getMav() {
		return $this->mav;
	}
	
	/**
	 * @param WindModelAndView $mav the $mav to set
	 * @return WindDispatcher
	 * @author Qiong Wu
	 */
	public function setMav($mav) {
		if ($mav instanceof WindModelAndView) {
			$this->mav = $mav;
		} else
			throw new WindException('The type of object error.');
		
		return $this;
	}
	
	/**
	 * @param WindRouter $router
	 * @return WindDispatcher
	 */
	public function setRouter($router) {
		if ($router instanceof WindRouter) {
			$this->module = $router->getModule();
			$this->controller = $router->getController();
			$this->action = $router->getAction();
			$this->router = $router;
		}
		return $this;
	}
	
	/**
	 * 返回处理操作句柄
	 * @return array($className,$method)
	 */
	public function getActionHandle($path = '') {
		if (!$path) $path = $this->path;
		if ($path == '') {
			$moduleConfig = C::getModules($this->module);
			$path = $moduleConfig[IWindConfig::MODULE_PATH] . '.' . $this->controller . 'Controller';
		}
		$method = $this->action;
		list(, $className, , $realPath) = L::getRealPath($path, true);
		if (!$realPath) {
			$path .= $this->action;
			list(, $className, , $realPath) = L::getRealPath($path, true);
			$method = 'run';
		}
		L::import($realPath);
		if (!class_exists($className) || !in_array($method, get_class_methods($className))) {
			return array(null, null);
		}
		return array($className, $method);
	}
	
	/**
	 * @return WindDispatcher
	 */
	static public function getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
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
	 * @return the $module
	 */
	public function getModule() {
		return $this->module;
	}

}