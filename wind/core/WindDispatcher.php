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
	private $moduleConfig;
	
	private $forward = null;
	private $immediately = false;
	private $views = array();
	
	private $request = null;
	private $response = null;
	private $frontController = null;
	private $router = null;
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WindFrontController $frontController
	 */
	public function __construct($request, $response, $frontController) {
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
		if (($redirecter = $this->getForward()->getRedirecter()) !== null)
			$this->dispatchWithRedirect($redirecter);
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
		$this->setAction($forward->getAction());
		if (!($path = $forward->getActionPath())) return $this;
		if (($pos = strrpos($path, '.')) !== false) {
			$this->setModule(substr($path, 0, $pos));
			$path = substr($path, $pos + 1);
		}
		$this->setController($path);
		return $this;
	}
	
	/**
	 * @param WindRouter $router
	 * @return WindDispatcher
	 */
	public function initWithRouter($router) {
		$this->setModule($router->getModule());
		$this->setController($router->getController());
		$this->setAction($router->getAction());
		$this->router = $router;
		return $this;
	}
	
	/**
	 * 返回处理操作句柄
	 * @return array($className,$method)
	 */
	public function getActionHandle() {
		$module = $this->moduleConfig[IWindConfig::MODULE_PATH];
		$suffix = $this->moduleConfig[IWindConfig::MODULE_CONTROLLER_SUFFIX];
		$path = $module . '.' . $this->controller . $suffix;
		$method = $this->action ? $this->action : $this->moduleConfig[IWindConfig::MODULE_METHOD];
		$className = L::import($path);
		if (!$className) {
			$suffix = $this->moduleConfig[IWindConfig::MODULE_ACTION_SUFFIX];
			$path .= $this->action . $suffix;
			$className = L::import($path);
			$method = $this->moduleConfig[IWindConfig::MODULE_METHOD];
		}
		if (!class_exists($className) || !in_array($method, get_class_methods($className))) return array(null, null);
		return array($className, $method);
	}
	
	private function formatName() {

	}
	
	/**
	 * 请求分发一个重定向请求
	 * @param WindRedirecter $redirecter
	 */
	private function dispatchWithRedirect($redirecter) {
		$redirect = $redirecter->buildUrl(array($this->router, 'buildUrl'), array($this->action, $this->controller, 
			$this->module));
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
		$viewResolver = $this->getView()->initViewWithForward($this->getForward())->createViewerResolver();
		$viewResolver->windAssign($this->getForward()->getVars());
		$viewName = $this->getForward()->getTemplateName();
		if ($this->immediately) {
			$viewResolver->immediatelyWindFetch();
		} else {
			$this->response->setBody($viewResolver->windFetch(), $viewName);
		}
	}
	
	/**
	 * 获得windview对象
	 * @return WindView
	 */
	private function getView() {
		if (!($templateConfigName = $this->getForward()->getTemplateConfig())) {
			$_temps = C::getModules($this->getModule());
			isset($_temps[IWindConfig::MODULE_TEMPLATE]) && $templateConfigName = $_temps[IWindConfig::MODULE_TEMPLATE];
		}
		if (!isset($this->views[$templateConfigName])) {
			L::import('WIND:component.viewer.WindView');
			$this->views[$templateConfigName] = new WindView($templateConfigName);
		}
		return $this->views[$templateConfigName];
	}
	
	/**
	 * 设置Controller
	 * @param string $controller
	 */
	private function setController($controller) {
		if (($pos = strrpos(strtolower($controller), 'controller')) !== false) $controller = substr($controller, 0, $pos);
		$this->controller = $controller;
	}
	
	/**
	 * 设置Action
	 * @param string $action
	 */
	private function setAction($action) {
		if (($pos = strrpos(strtolower($action), 'action')) !== false) $action = substr($action, 0, $pos);
		$this->action = $action;
	}
	
	/**
	 * 根据module的路径信息获取module名称
	 * @param path
	 * @param pos
	 */
	private function setModule($module) {
		$modules = C::getModules();
		if (key_exists($module, $modules)) {
			$this->module = $module;
		} else {
			foreach ($modules as $key => $value) {
				if ($module == $value[IWindConfig::MODULE_PATH]) $this->module = $key;
			}
		}
		$this->moduleConfig = $modules[$this->module];
	}
	
	/**
	 * 获得Action操作句柄
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * 获得Controller操作句柄
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}
	
	/**
	 * 获得Module操作句柄
	 * @return string
	 */
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

}