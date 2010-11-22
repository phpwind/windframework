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
	protected $routerRule = '';
	protected $routerName = '';
	protected $method = 'run';
	
	protected $action = 'run';
	protected $controller = 'index';
	protected $module = 'default';
	
	protected $modules = array();
	
	protected $modulePath = '';
	protected $actionForm = 'actionForm';
	
	public function __construct($configObj = null) {
		$this->init($configObj);
	}
	
	/**
	 * 初始化路由配置
	 * 
	 * @param WSystemConfig $configObj
	 */
	protected function init($configObj) {
		if ($configObj === null) throw new WindException('Config object is null.');
		$this->modules = $configObj->getModulesConfig();
		$this->routerRule = $configObj->getRouterRule($this->routerName);
	}
	
	/**
	 * 通过实现该接口实现路由解析
	 * 
	 * @return WRouterContext
	 */
	abstract function doParser($request, $response);
	
	/**
	 * 获得请求处理类,返回一个数组，array('$className','$method')
	 * 
	 * @return array
	 */
	public function getActionHandle() {
		if (empty($this->modules)) throw new WindException('the modules is empty.');
		$module = $this->modules[$this->getModule()];
		$className = $this->getController();
		$method = $this->getAction();
		L::import($module . '.' . $className);
		if (!class_exists($className)) {
			$module .= $module . '.' . $className;
			$className = $this->getAction();
			L::import($module . '.' . $className);
			if (!class_exists($className)) return array(null, null);
			$method = $this->method;
		}
		if (!in_array($method, get_class_methods($className))) return array(null, null);
		$this->modulePath = $module;
		return array($className, $method);
	}
	
	/**
	 * 获得请求处理类,返回一个数组，array('$className','$method')
	 * 
	 * @return array
	 */
	abstract public function getActionFormHandle();
	
	/**
	 * 获得请求处理类,返回一个数组，array('$className','$method')
	 * 
	 * @return array
	 */
	abstract public function getDefaultViewHandle();
	
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
		return $this->controller . 'Controller';
	}
	
	/**
	 * 获得一组应用入口目录名
	 */
	public function getModule() {
		return $this->module;
	}

}