<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-24
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 请求转发及页面重定向
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindDispatcher {
	private $action;
	private $controller;
	private $module;
	
	private $forward = null;
	private $immediately = false;
	
	private $request = null;
	private $response = null;
	private $frontController = null;
	private $router = null;
	private static $instance = null;
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WindFrontController $frontController
	 */
	private function __construct($request, $response, $frontController) {
		$this->request = $request;
		$this->response = $response;
		$this->frontController = $frontController;
	}
	
	/**
	 * 请求分发处理
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function dispatch($immediately = false) {
		if ($this->getForward()->isRedirect())
			$this->dispatchWithRedirect();
		elseif (($action = $this->getForward()->getAction()) !== null) {
			$this->immediately = $immediately;
			$this->dispatchWithAction($action);
		} else
			$this->dispatchWithTemplate();
	}
	
	/**
	 * @param WindForward $forward
	 */
	public function setForward($forward) {
		$this->forward = $forward;
		if ($forward->getAction())
			$this->action = $forward->getAction();
		if (!($path = $forward->getActionPath()))
			return $this;
		if (($pos = strrpos($path, '.')) !== false) {
			$this->controller = substr($path, $pos + 1);
			$this->module = substr($path, 0, $pos);
		} else
			$this->controller = $path;
		return $this;
	}
	
	/**
	 * @param WindRouter $router
	 * @return WindDispatcher
	 */
	public function initWithRouter($router) {
		$this->module = $router->getModule();
		$this->controller = $router->getController();
		$this->action = $router->getAction();
		$this->router = $router;
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
		list($className, $realPath) = L::getRealPath($path, true);
		return array($className, $realPath);
	}
	
	/**
	 * 请求分发一个重定向请求
	 */
	private function dispatchWithRedirect() {
		$redirect = $this->getForward()->getRedirect();
		if ($redirect === '')
			$redirect = $this->buildRedirect();
		$this->response->sendRedirect($redirect);
	}
	
	/**
	 * 请求分发一个操作请求
	 * @param String $action
	 */
	private function dispatchWithAction($action) {
		//TODO
		$this->frontController->getApplicationHandle()->processRequest($this->request, $this->response);
	}
	
	/**
	 * 请求分发一个模板请求
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function dispatchWithTemplate() {
		$viewer = $this->getForward()->getView()->createViewerResolver();
		//TODO
		$viewer->windAssign($this->response->getData());
		$viewName = $this->getForward()->getTemplateName();
		if ($this->immediately)
			$viewer->immediatelyDisplay();
		else $this->response->setBody($viewer->windFetch(), $viewName);
	}
	
	/**
	 * 组装重定向URL
	 */
	private function buildRedirect() {
		return $this->router->buildUrl($this->action, $this->controller, $this->module, $this->forward->getRedirectArgs());
	}
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WindFrontController $frontController
	 * @return WindDispatcher
	 */
	static public function &getInstance($request = null, $response = null, $frontController = null) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($request, $response, $frontController);
		}
		return self::$instance;
	}
	
	public function getAction() {
		return $this->action;
	}
	
	public function getController() {
		return $this->controller;
	}
	
	public function getModule() {
		return $this->module;
	}
	
	/**
	 * 返回一个WindForward对象
	 * @return WindForward
	 */
	public function getForward() {
		return $this->forward;
	}
	
	static public function distroy() {
		self::$instance = null;
	}
}