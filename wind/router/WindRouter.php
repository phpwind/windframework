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
 * @package wind.router
 */
class WindRouter extends AbstractWindRouter {

	/* (non-PHPdoc)
	 * @see IWindRouter::route()
	 */
	public function route() {
		if ($this->hasRoute) {
			$this->setCallBack(array($this, 'defaultRoute'));
			$params = $this->getHandler()->handle();
		} else
			$params = $this->defaultRoute();
		$params && $this->setParams($params);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::assemble()
	 */
	public function assemble($action, $args = array(), $route = null) {
		$route || $route = $this->defaultRoute;
		if ($route && (null !== $route = $this->getRoute($route))) {
			$_url = $route->build($this, $action, $args);
		} else {
			list($_a, $_c, $_m, $args) = WindUrlHelper::resolveAction($action, $args);
			$args[$this->moduleKey] = $_m ? $_m : $this->module;
			$args[$this->controllerKey] = $_c ? $_c : $this->controller;
			$args[$this->actionKey] = $_a ? $_a : $this->action;
			$_baseUrl = $this->getRequest()->getScript();
			$_url = $_baseUrl . '?' . WindUrlHelper::argsToUrl($args);
		}
		return $_url;
	}

	/**
	 * 默认路由规则
	 * 
	 * 默认情况下仅仅解析路由相关参数值
	 * @return array
	 */
	public function defaultRoute() {
		$action = $this->getRequest()->getRequest($this->actionKey);
		$controller = $this->getRequest()->getRequest($this->controllerKey);
		$module = $this->getRequest()->getRequest($this->moduleKey);
		$action && $this->setAction($action);
		$controller && $this->setController($controller);
		$module && $this->setModule($module);
		return;
		/*$_pathInfo = $this->getRequest()->getPathInfo();
		return $_pathInfo ? WindUrlHelper::urlToArgs($_pathInfo) : array();*/
	}
}

?>