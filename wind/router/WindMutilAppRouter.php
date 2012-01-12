<?php
/**
 * 多应用支持路由协议解析器
 *
 * @author Qiong Wu <papa0924@gmail.com> 2012-1-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package router
 */
class WindMutilAppRouter extends WindRouter {
	protected $appKey = 'p';
	protected $_app;
	protected $app = 'default';

	/* (non-PHPdoc)
	 * @see WindRouter::route()
	 */
	public function route($request, $response) {
		$this->_app = $this->app;
		parent::route($request, $response);
	}
	
	public function assemble($action, $args = array(), $route = null) {
		$route || $route = $this->defaultRoute;
		if ($route && (null !== $route = $this->getRoute($route))) {
			$_url = $route->build($this, $action, $args);
		} else {
			list($_a, $_c, $_m, $_p, $args) = WindUrlHelper::resolveAction($action, $args);
			$_p || $_p = $this->getApp();
			if ($_p && $_p !== $this->getDefaultApp()) $args[$this->appKey] = $_p;
			if ($_m && $_m !== $this->getDefaultModule()) $args[$this->moduleKey] = $_m;
			if ($_c && $_c !== $this->getDefaultController()) $args[$this->controllerKey] = $_c;
			if ($_a && $_a !== $this->getDefaultAction()) $args[$this->actionKey] = $_a;
			$_url = $this->request->getScript() . '?' . WindUrlHelper::argsToUrl($args);
		}
		return $_url;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::setParams()
	 */
	protected function setParams($params, $request) {
		parent::setParams($params, $request);
		$app = isset($params[$this->appKey]) ? $params[$this->appKey] : $request->getRequest(
			$this->appKey);
		$app && $this->setApp($app);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->app = $this->getConfig('app', 'default-value', $this->app);
		$this->appKey = $this->getConfig('app', 'url-param', $this->appKey);
	}

	/**
	 * @return string
	 */
	public function getApp() {
		return $this->app;
	}

	/**
	 * 设置当前要访问的appname
	 *
	 * @param string $appName
	 */
	public function setApp($appName) {
		$this->app = $appName;
	}

	/**
	 * @return string
	 */
	public function getAppKey() {
		return $this->appKey;
	}

	/**
	 * @param string $appKey
	 */
	public function setAppKey($appKey) {
		$this->appKey = $appKey;
	}

	/**
	 * @param string $app
	 */
	public function setDefaultApp($app) {
		$this->_app = $app;
	}

	/**
	 * @return string
	 */
	public function getDefaultApp() {
		return $this->_app;
	}

}

?>