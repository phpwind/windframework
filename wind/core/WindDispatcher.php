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
		if ($this->getMav()->isRedirect())
			$this->_dispatchWithRedirect($request, $response);
		elseif ($this->getMav()->getAction())
			$this->_dispatchWithAction($request, $response);
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
	private function _dispatchWithRedirect($request, $response) {
		$response->sendRedirect($this->getMav()->getRedirect());
	}
	
	/**
	 * 请求分发一个操作请求
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithAction($request, $response) {

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
		if ($mav instanceof WindModelAndView)
			$this->mav = $mav;
		else
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
	public function getActionHandle() {
		$moduleConfig = C::getModules($this->module);
		$module = $moduleConfig[IWindConfig::MODULE_PATH];
		$className = $this->controller . 'Controller';
		$method = $this->action;
		L::import($module . '.' . $className);
		if (!class_exists($className)) {
			$module .= $module . '.' . $className;
			$className = $this->getAction();
			L::import($module . '.' . $className);
			if (!class_exists($className)) return array(null, null);
			$method = 'run';
		}
		if (!in_array($method, get_class_methods($className))) return array(null, null);
		return array($className, $method);
	}
	
	/**
	 * @return WindDispatcher
	 */
	static public function getInstance($mav = null) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($mav);
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