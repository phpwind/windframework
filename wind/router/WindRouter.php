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
		if (null !== ($route = $this->getRoute($route)))
			return $route->build($action, $args);
		if ($this->currentRoute !== null)
			return $this->currentRoute->build($action, $args);
		list($_a, $_c, $_m, $args) = WindUrlHelper::resolveAction($action, $args);
		$_baseUrl = $this->getRequest()->getBaseUrl(true) . '/' . $this->getRequest()->getScript();
		$_url = sprintf($this->reverse, $_baseUrl, $this->moduleKey, ($_m ? $_m : $this->module),  $this->controllerKey, ($_c ? $_c : $this->controller), 
			$this->actionKey, ($_a ? $_a : $this->action));
		return WindUrlHelper::checkUrl($_url . WindUrlHelper::argsToUrl($args));
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