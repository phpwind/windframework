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
		if ($route !== null)
			return $route->build($action, $args);
		if ($this->currentRoute !== null)
			return $this->currentRoute->build($action, $args);
		list($_a, $_c, $_m, $args) = WindUrlHelper::resolveAction($action, $args);
		$_baseUrl = $this->getRequest()->getBaseUrl(true) . '/' . $this->getRequest()->getScript();
		$_url = sprintf($_baseUrl . "?$this->moduleKey=%s&$this->controllerKey=%s&$this->actionKey=%s&%s", 
			($_m ? $_m : $this->module), ($_c ? $_c : $this->controller), ($_a ? $_a : $this->action), $args);
		return WindUrlHelper::checkUrl($_url);
	}

	/**
	 * 默认路由规则
	 */
	public function defaultRoute() {
		$params[$this->actionKey] = $this->getRequest()->getRequest($this->actionKey, $this->action);
		$params[$this->controllerKey] = $this->getRequest()->getRequest($this->controllerKey, $this->controller);
		$params[$this->moduleKey] = $this->getRequest()->getRequest($this->moduleKey, $this->module);
		return $params;
	}

}

?>