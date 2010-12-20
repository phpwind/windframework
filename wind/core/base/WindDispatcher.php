<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
abstract class WindDispatcher {
	public $action;
	public $controller;
	public $module;
	public $forward = null;
	public $router = null;
	
	protected $immediately = false;
	protected $moduleConfig;
	protected $request = null;
	protected $response = null;
	
	protected $application = null;
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WindFrontController $frontController
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
	public function dispatch($immediately = false) {
		if (($redirecter = $this->forward->getRedirecter()) !== null)
			$this->dispatchWithRedirect($redirecter);
		elseif (($action = $this->forward->getAction()) !== null) {
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
		$suffix = ucfirst($this->moduleConfig[IWindConfig::MODULE_CONTROLLER_SUFFIX]);
		$method = $this->action ? $this->action : $this->moduleConfig[IWindConfig::MODULE_METHOD];
		$path = $module . '.' . $this->controller . $suffix;
		$className = L::import($module . '.' . ucfirst($this->controller) . $suffix);
		if (!$className) {
			$suffix = ucfirst($this->moduleConfig[IWindConfig::MODULE_ACTION_SUFFIX]);
			$className = L::import($path . ucfirst($this->action) . $suffix);
			$method = $this->moduleConfig[IWindConfig::MODULE_METHOD];
		}
		if (!class_exists($className) || !in_array($method, get_class_methods($className))) return array(
			null, 
			null);
		return array(
			$className, 
			$method);
	}
	
	/**
	 * 请求分发一个重定向请求
	 * @param WindRedirecter $redirecter
	 */
	protected function dispatchWithRedirect($redirecter) {
		$redirect = $redirecter->buildUrl(array(
			$this->router, 
			'buildUrl'), array(
			$this->action, 
			$this->controller, 
			$this->module));
		$this->response->sendRedirect($redirect);
	}
	
	/**
	 * 请求分发一个操作请求
	 * @param String $action
	 */
	protected function dispatchWithAction($action) {
		if (!($this->application instanceof WindApplication)) {
			throw new WindException('dispatch action failed.');
		}
		$this->application->processRequest($this->request, $this->response);
	}
	
	/**
	 * 请求分发一个模板请求
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	protected function dispatchWithTemplate() {

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
	 * @param $application the $application to set
	 * @author Qiong Wu
	 */
	public function setApplication($application) {
		$this->application = $application;
	}

}