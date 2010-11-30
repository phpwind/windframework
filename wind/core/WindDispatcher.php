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
	
	private $mav = null;
	private $mavs = array();
	
	private $request = null;
	private $response = null;
	
	private static $instance = null;
	
	/**
	 * @param WindModelAndView $mav
	 */
	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
	}
	
	/**
	 * 请求分发处理
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function dispatch() {
		if ($this->getMav() === null) throw new WindException('dispatch error.');
		if (($redirect = $this->getMav()->getRedirect()) !== '')
			$this->_dispatchWithRedirect($redirect);
		
		elseif (($action = $this->getMav()->getAction()) !== '')
			$this->_dispatchWithAction($action);
		
		else
			$this->_dispatchWithTemplate();
		return;
	}
	
	/**
	 * 请求分发一个重定向请求
	 * 
	 */
	private function _dispatchWithRedirect($redirect) {
		$this->response->sendRedirect($redirect);
	}
	
	/**
	 * 请求分发一个操作请求
	 * @param String $action
	 */
	private function _dispatchWithAction($action) {
		WindFrontController::getInstance()->getApplicationHandle()->processRequest($this->request, $this->response);
	}
	
	/**
	 * 请求分发一个模板请求
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithTemplate() {
		$viewer = $this->getMav()->getView()->createViewerResolver();
		$viewer->windAssign($this->response->getData());
		$viewName = $this->getMav()->getViewName();
		$this->response->setBody($viewer->windFetch(), $viewName);
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
			$this->mavs[] = $mav;
			$this->initWithModelAndView($mav);
		} else
			throw new WindException('The type of object error.');
		
		return $this;
	}
	
	/**
	 * @param WindModelAndView $mav
	 */
	private function initWithModelAndView($mav) {
		$this->mav = $mav;
		$this->action = $mav->getAction();
		$path = $this->getMav()->getActionPath();
		if (!$path) return;
		if (($pos = strrpos($path, '.')) !== false) {
			$this->controller = substr($path, $pos + 1);
			$this->module = substr($path, 0, $pos);
		} else
			$this->controller = $path;
	}
	
	/**
	 * @param WindRouter $router
	 * @return WindDispatcher
	 */
	public function initWithRouter($router) {
		if ($router instanceof WindRouter) {
			$this->module = $router->getModule();
			$this->controller = $router->getController();
			$this->action = $router->getAction();
		}
		return $this;
	}
	
	/**
	 * 返回处理操作句柄
	 * @return array($className,$method)
	 */
	public function getActionHandle() {
		$moduleConfig = C::getModules($this->module);
		$module = $moduleConfig ? $moduleConfig[IWindConfig::MODULE_PATH] : $this->module;
		$path = $module . '.' . $this->controller;
		$method = $this->action;
		list($className, $realPath) = $this->matchActionHandle($path);
		if (!$realPath) {
			list($className, $realPath) = $this->matchActionHandle($path, 'Controller');
		}
		if (!$realPath) {
			$path .= $this->action;
			list($className, $realPath) = $this->matchActionHandle($path, 'Action');
			$method = 'run';
		}
		L::import($realPath);
		if (!class_exists($className) || !in_array($method, get_class_methods($className))) {
			return array(null, null);
		}
		return array($className, $method);
	}
	
	private function matchActionHandle($path, $match = '') {
		if ($match && !preg_match("/" . $match . "$/i", $path)) {
			$path .= $match;
		}
		list(, $className, , $realPath) = L::getRealPath($path, true);
		return array($className, $realPath);
	}
	
	/**
	 * @return WindDispatcher
	 */
	static public function getInstance($request = null, $response = null) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($request, $response);
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