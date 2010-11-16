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
	protected $routerName = 'url';
	
	protected $module = '';
	
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
			throw new WException('The url parser rule is empty.');
		
		$this->_setValues($request, $response);
	}
	
	/**
	 * 通过实现WAction接口的调用该方法
	 * 
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	public function getActionHandle() {
		if (empty($this->modules))
			throw new WException('the modules is empty.');
		
		$module = $this->getModule() ? $this->modules[$this->getModule()] : 'actionControllers';
		$module .= '.' . $this->getController() . 'Controller';
		$className = $this->getAction() . 'Action';
		$method = 'run';
		W::import($module . '.' . $className);
		if (!class_exists($className))
			throw new WException('The class ' . $className . ' is not exists.');
		
		if (!in_array(get_class_methods($method, $className)))
			throw new WException('The mehtod ' . $method . ' is not exists in class ' . $className . '.');
		
		$this->modulePath = $module;
		return array(
			$className, 
			$method
		);
	}
	
	/**
	 * 通过实现WActionController接口的调用该方法
	 */
	public function getControllerHandle() {
		if (empty($this->modules))
			throw new WException('the modules is empty.');
		
		$module = $this->getModule() ? $this->modules[$this->getModule()] : 'actionControllers';
		$className = $this->getController() . 'Controller';
		$method = $this->getAction();
		W::import($module . '.' . $className);
		if (!class_exists($className))
			throw new WException('The class ' . $className . ' is not exists.');
		
		elseif (!in_array($method, get_class_methods($className)))
			throw new WException('The mehtod ' . $method . ' is not exists in class ' . $className . '.');
		
		$this->modulePath = $module;
		return array(
			$className, 
			$method
		);
	}
	
	/**
	 * 返回请求的ActionForm句柄，如果未定义则返回null
	 */
	public function getActionFormHandle() {
		if (!$this->modulePath)
			throw new WException('The path of module is not exists.');
		
		try {
			$formPath = $this->modulePath . '.' . 'actionForm';
			$className = $this->controller . $this->action . 'Form';
			if (!is_file($formPath . '.' . $className . '.php')) return null;
			W::import($formPath . '.' . $className);
			if (class_exists($className))
				return $className;
		} catch (Exception $exception) {
			return null;
		}
		return null;
	}
	
	/**
	 * 返回请求的ActionForm句柄，如果未定义则返回null
	 */
	public function getDefaultViewHandle() {
		if (!$this->modulePath)
			throw new WException('The path of module is not exists.');
		
		return $this->controller . '_' . $this->action;
	}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	private function _setValues($request, $response) {
		$keys = array_keys($this->routerRule);
		$this->action = $request->getGet($keys[0], $this->action);
		$this->controller = $request->getGet($keys[1], $this->controller);
		$this->module = $request->getGet($keys[2], $this->module);
	}

}