<?php
Wind::import('WIND:router.AbstractWindRouter');
/**
 * 命令行路由，默认路由规则 php index.php [-m default] [-c index] [-a run] [-p id1 id2] [--help]
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $id$
 * @package command
 */
class WindCommandRouter extends AbstractWindRouter {
	protected $paramKey = 'p';
	/**
	 * @var WindCommandRequest
	 */
	private $request = null;
	
	/* (non-PHPdoc)
	 * @see AbstractWindRouter::route()
	 */
	public function route($request) {
		$this->request = $request;
		$this->_action = $this->action;
		$this->_controller = $this->controller;
		$this->_module = $this->module;
		$this->setCallBack(array($this, 'defaultRoute'));
		$params = $this->getHandler()->handle($request);
		$params && $this->setParams($params, $request);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::assemble()
	 */
	public function assemble($action, $args = array(), $route = '') {
		return '';
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindRouter::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->paramKey = $this->getConfig('paramKey', '', $this->paramKey);
	}
	
	/**
	 * 默认路由规则
	 * 
	 * @param WindCommandRequest $request
	 * @return array
	 */
	public function defaultRoute($request) {
		$args = $request->getAttribute('argv', array());
		$help = false;
		$sort = array();
		$continue_k = '';
		foreach ($args as $k => $v) {
			if ($v === '--help') {
				$help = true;
				continue;
			}
			if (strpos($v, '-') === 0) {
				$continue_k = substr($v, 1);
			} elseif ($continue_k !== '') {
				if ($continue_k == $this->paramKey) {
					$sort[$continue_k] = (array)$sort[$continue_k];
					$sort[$continue_k][] = $v;
				} 
				else
					$sort[$continue_k] = $v;
			}
		}
		if ($help) {
			$params = array();
			isset($sort[$this->moduleKey]) && $params[$this->moduleKey] = $sort[$this->moduleKey];
			isset($sort[$this->controllerKey]) && $params[$this->controllerKey] = $sort[$this->controllerKey];
			isset($sort[$this->actionKey]) && $params[$this->actionKey] = $sort[$this->actionKey];
			$sort[$this->actionKey] = 'help';
			$sort[$this->paramKey] = array($params);
			return $sort;
		}
		return $sort;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindRouter::setParams()
	 */
	protected function setParams($params, $request) {
		/* @var $request WindCommandRequest */
		isset($params[$this->paramKey]) && $_SERVER['argv'] = $params[$this->paramKey];
		isset($params[$this->moduleKey]) && $this->setModule($params[$this->moduleKey]);
		isset($params[$this->controllerKey]) && $this->setController($params[$this->controllerKey]);
		isset($params[$this->actionKey]) && $this->setAction($params[$this->actionKey]);
	}
	
	/**
	 * @return string
	 */
	public function getParamKey() {
		return $this->paramKey;
	}
}

?>