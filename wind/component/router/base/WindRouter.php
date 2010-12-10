<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 路由解析器接口
 * 职责: 路由解析, 返回路由对象
 * 实现路由解析器必须实现该接口的doParser()方法
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindRouter {
	protected $routerConfig = array();
	protected $routerRule = '';
	protected $routerName = '';
	protected $method = 'run';
	
	protected $action = 'run';
	protected $controller = 'Index';
	protected $module = 'default';
	
	protected $modulePath = '';
	protected $actionForm = 'actionForm';
	
	public function __construct($routerConfig) {
		$this->init($routerConfig);
	}
	
	/**
	 * 初始化路由配置
	 * 
	 * @param WSystemConfig $configObj
	 */
	protected function init($routerConfig) {
		$this->routerConfig = $routerConfig;
	}
	
	/**
	 * 通过实现该接口实现路由解析
	 * 
	 * @return WRouterContext
	 */
	abstract function doParser($request, $response);
	
	/**
	 * 根据路由解析，组装URL
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 */
	abstract public function buildUrl($action = '', $controller = '', $module = '');
	
	/**
	 * 获得请求处理类,返回一个数组，array('$className','$method')
	 * 
	 * @return array
	 */
	public function getActionHandle() {
		$moduleConfig = C::getModules($this->getModule());
		$module = $moduleConfig[IWindConfig::MODULE_PATH];
		$className = $this->getController();
		$method = $this->getAction();
		L::import($module . '.' . $className);
		if (!class_exists($className)) {
			$module .= $module . '.' . $className;
			$className = $this->getAction();
			L::import($module . '.' . $className);
			if (!class_exists($className))
				return array(null, null);
			$method = $this->method;
		}
		if (!in_array($method, get_class_methods($className)))
			return array(null, null);
		$this->modulePath = $module;
		return array($className, $method);
	}
	
	/**
	 * 获得业务操作
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * 获得业务对象
	 */
	public function getController() {
		return $this->controller;
	}
	
	/**
	 * 获得一组应用入口
	 */
	public function getModule() {
		return $this->module;
	}

}