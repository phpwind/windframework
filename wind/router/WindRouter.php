<?php
Wind::import('WIND:router.AbstractWindRouter');
/**
 * wind路由基础实现
 * 
 * 该路由是框架默认路由实现继承自{@see AbstractWindRouter},
 * 'WindRouter'是利用路由链机制实现了多路由协议支持.在没有任何路由协议定义的情况下,直接进行参数解析.
 * 路由的使用方式举例:<code>
 * //路由支持的配置如下:
 * 'module' => array(	//module相关配置
 * 'url-param' => 'm',
 * 'default-value' => 'default',
 * ),
 * 'controller' => array(	//controller相关配置
 * 'url-param' => 'c',
 * 'default-value' => 'index',
 * ),
 * 'action' => array(	//action相关配置
 * 'url-param' => 'a',
 * 'default-value' => 'run',
 * ),
 * //如果无需复杂的路由协议支持,或urlrewrite支持,无需配置下面下面信息
 * 'rules' => array(
 * 'WindRoute' => array(	//路由协议名称
 * 'class' => 'WIND:router.route.WindRoute',	//路由协议具体实现类
 * 'regex' => '',	//用于匹配的正则表达式
 * 'params' => array(),	//参数mapping
 * 'reverse' => '')),	//反向解析
 * </code>
 * 
 * @author Qiong Wu <papa0924@gmail.com> 2011-9-23
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package router
 */
class WindRouter extends AbstractWindRouter {
	/**
	 * @var WindHttpRequest
	 */
	private $request = null;

	/* (non-PHPdoc)
	 * @see IWindRouter::route()
	 */
	public function route($request, $response) {
		$this->_action = $this->action;
		$this->_controller = $this->controller;
		$this->_module = $this->module;
		$this->_app = $this->app;
		$this->request = $request;
		$this->setCallBack(array($this, 'defaultRoute'));
		$params = $this->getHandler()->handle($request, $response);
		$params && $this->setParams($params, $request);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::assemble()
	 */
	public function assemble($action, $args = array(), $route = null) {
		$route || $route = $this->defaultRoute;
		if ($route && (null !== $route = $this->getRoute($route))) {
			$_url = $route->build($this, $action, $args);
		} else {
			list($_a, $_c, $_m, $_p, $args) = WindUrlHelper::resolveAction($action, $args);
			$_p || $_p = $this->getApp();
			if ($_p && $_p !== $this->getDefaultApp()) $args[$this->appKey] = $_p;
			$_m || $_m = $this->getModule();
			if ($_m && $_m !== $this->getDefaultModule()) $args[$this->moduleKey] = $_m;
			$_c || $_c = $this->getController();
			if ($_c && $_c !== $this->getDefaultController()) $args[$this->controllerKey] = $_c;
			$_a || $_a = $this->getAction();
			if ($_a && $_a !== $this->getDefaultAction()) $args[$this->actionKey] = $_a;
			$_url = $this->request->getScript() . '?' . WindUrlHelper::argsToUrl($args);
		}
		return $_url;
	}

	/**
	 * 默认路由规则
	 * 
	 * 默认情况下仅仅解析路由相关参数值
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @return array
	 */
	public function defaultRoute($request, $response) {
		$action = $request->getRequest($this->actionKey);
		$controller = $request->getRequest($this->controllerKey);
		$module = $request->getRequest($this->moduleKey);
		$app = $request->getRequest($this->appKey);
		$action && $this->setAction($action);
		$controller && $this->setController($controller);
		$module && $this->setModule($module);
		$app && $this->setApp($app);
		return;
	}
}

?>