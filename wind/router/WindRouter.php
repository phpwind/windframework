<?php
Wind::import('WIND:router.AbstractWindRouter');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindRouter extends AbstractWindRouter {

	/* (non-PHPdoc)
	 * @see IWindRouter::route()
	 */
	public function route() {
		$this->setCallBack(array($this, 'defaultRoute'));
		$params = $this->getHandler()->handle();
		$this->setParams($params);
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
			$_baseUrl = $this->getRequest()->getScript();
			$_url = sprintf($this->reverse, $_baseUrl, ($_m ? $_m : $this->module), ($_c ? $_c : $this->controller), 
				($_a ? $_a : $this->action));
			$_url .= WindUrlHelper::argsToUrl($args);
		}
		return $_url;
	}

	/**
	 * 默认路由规则
	 */
	public function defaultRoute() {
		$_pathInfo = $this->getRequest()->getPathInfo();
		return $_pathInfo ? WindUrlHelper::urlToArgs($_pathInfo) : array();
	}
}

?>