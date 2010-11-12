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
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WRouter {
	protected $routerRule = '';
	protected $routerName = 'url';
	
	protected $action = 'run';
	protected $controller = 'index';
	protected $app1 = 'actionControllers';
	protected $app2;
	
	protected $configObj = null;
	
	function __construct($configObj = null) {
		$this->init($configObj);
	}
	
	/**
	 * 初始化路由配置
	 * @param WSystemConfig $configObj
	 */
	protected function init($configObj) {
		if ($configObj === null)
			throw new WException('Config object is null!!!');
		$this->routerRule = $configObj->getRouterRule($this->routerName);
		$this->configObj = $configObj;
	}
	
	/**
	 * 通过实现该接口实现路由解析
	 * @return WRouterContext
	 */
	abstract function doParser($request, $response);
	
	abstract function &getActionHandle();
	abstract function &getControllerHandle();
	
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
	 * 获得一组应用入口目录名
	 */
	public function getApp1() {
		return $this->app1;
	}
	
	/**
	 * 获得一组应用入口二级目录名
	 */
	public function getApp2() {
		return $this->app2;
	}

}