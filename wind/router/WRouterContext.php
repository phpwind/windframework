<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
WBasic::import('base.WContext');

/**
 * 路由对象
 * 通过访问该对象可以获得到当前请求的应用模块以及请求的具体操作
 * 
 * 路由规则，当接收到一个请求后，路由管理器会根据路由配置初始化一个路由解析器解析该请求
 * 解析后返回一个WRouterContext对象
 * 
 * 操作访问规则：
 * 1. $action 操作名称，方法名或者类名称，根据配置的不同会产生变化，唯一不变的是该参数指向一个具体的业务操作
 * 2. $controller 应用控制器名称，类名称，该参数指向一组操作的集合，可能是某个小的业务模块
 * 3. $app1 一组应用的名称，该参数指向一个应用，比如 bbs/cms/house/dianpu 等
 * 4. $app2 一组应用的名称，如果一个应用下面有两组applicationController，则需要定义该参数，比如 前台和后台的应用分开
 * 
 * 访问顺序 ：
 * $app1 -> $app2 -> $controller -> $action
 * 
 * 以上各参数都有默认值，如果未定义该参数，则使用默认值
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WRouterContext extends WModel implements WContext {
	
	/* 操作 */
	private $action;
	
	/* 应用控制器 */
	private $controller;
	
	/* 一个应用入口1 */
	private $app1;
	
	/* 一个应用的入口2 */
	private $app2;
	
	private function _init() {
		$this->_config = array(
			'defaultAction' => 'run', 
			'defaultController' => 'index', 
			'defaultApp1' => 'bbs', 
			'defaultApp2' => 'admin'
		);
	}
	
	public function getDefaultAction() {
		if (!isset($this->_default_action)) $this->_default_action = $this->_config['defaultAction'];
		return $this->_default_action;
	}
	
	public function getDefaultController() {
		if (!isset($this->_default_controller)) $this->_default_action = $this->_config['defaultController'];
		return $this->_default_controller;
	}
	
	public function getDefaultApp1() {
		if (!isset($this->_default_app1)) $this->_default_action = $this->_config['defaultApp1'];
		return $this->_default_app1;
	}
	
	public function getDefaultApp2() {
		if (!isset($this->_default_app2)) $this->_default_action = $this->_config['defaultApp2'];
		return $this->_default_app2;
	}
	
	/**
	 * @return WRouterContext
	 */
	public static function getInstance() {
		return WBasic::getInstance(__CLASS__);
	}

}