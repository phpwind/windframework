<?php
Wind::import('COM:router.AbstractWindRouter');
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
	public function assemble() {
		// TODO Auto-generated method stub
	}

	/**
	 * 默认路由规则
	 */
	public function defaultRoute() {
		$params[$this->actionKey] = $this->getRequest()->getRequest($this->actionKey, $this->action);
		$params[$this->controllerKey] = $this->getRequest()->getRequest($this->controllerKey, 
			$this->controller);
		$params[$this->moduleKey] = $this->getRequest()->getRequest($this->moduleKey, $this->module);
		return $params;
	}

}

?>