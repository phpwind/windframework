<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @link WRouteParser
 * @package 
 */
class WUrlRouter extends WRouter {
	protected $parserName = 'url';
	
	protected $actionKey = '';
	protected $controllerKey = '';
	protected $app1Key = '';
	protected $app2Key = '';
	
	/**
	 * 调用该方法实现路由解析
	 * 获得到 request 的静态对象，得到request的URL信息
	 * 获得 config 的静态对象，得到URL的格式信息
	 * 解析URL，并声称RouterContext对象
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	public function doParser($request, $response) {
		if (!$this->routerRule)
			$this->routerRule = array(
				'a' => 'run', 
				'c' => 'index', 
				'app1' => 'controller1', 
				'app2' => ''
			);
		$this->_setValues($request, $response);
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	private function _setValues($request, $response) {
		$keys = array_keys($this->routerRule);
		$this->actionKey = $keys[0];
		$this->controllerKey = $keys[1];
		$this->app1Key = $keys[2];
		$this->app2Key = $keys[3];
		
		$this->_setApp2($request, $response);
		$this->_setApp1($request, $response);
		$this->_setController($request, $response);
		$this->_setAction($request, $response);
	
	}
	
	/**
	 * 获得一个操作句柄
	 * @return NULL|object
	 */
	public function &getActionHandle() {
		$path = $this->app2 . '.' . $this->app1 . '.' . $this->controller;
		$actionName = $this->action . 'Action';
		W::import(trim($path . '.' . $actionName, '.'));
		if (!class_exists($actionName))
			return null;
		$class = new ReflectionClass($actionName);
		$object = $class->newInstance();
		return $object;
	}
	
	/**
	 * 获得一个操作句柄
	 * @return NULL|object
	 */
	public function &getControllerHandle() {
		$path = $this->app2 . '.' . $this->app1 . '.' . $this->controller;
		W::import(trim($path, '.'));
		if (!class_exists($this->controller))
			return null;
		$class = new ReflectionClass($this->controller);
		$object = $class->newInstance();
		return $object;
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	private function _setAction($request, $response) {
		$action = $request->getGet($this->actionKey);
		$action && $this->action = $action;
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	private function _setController($request, $response) {
		$controller = $request->getGet($this->controllerKey);
		$controller && $this->controller = $controller;
		$this->controller .= 'Controller';
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	private function _setApp1($request, $response) {
		$app1 = $request->getGet($this->app1Key);
		$app1 && $app1 = $this->configObj->getApplicationConfig($app1);
		$app1 && $this->app1 = $app1;
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	private function _setApp2($request, $response) {
		$app2 = $request->getGet($this->app2Key);
		$app2 && $app2 = $this->configObj->getApplicationConfig($app2);
		$app2 && $this->app2 = $app2;
	}

}