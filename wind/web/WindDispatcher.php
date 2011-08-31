<?php
/**
 * 职责描述：负责请求的分发
 * 分发类型：
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindDispatcher extends WindModule {
	/**
	 * 将上一次请求信息缓存在这个变量中
	 * @var array
	 */
	protected $token;
	/**
	 * @var boolean
	 */
	protected $display = false;

	/**
	 * 请求分发处理
	 * 
	 * @param WindForward $forward
	 * @param WindRouter $router
	 * @param boolean $display
	 * @return
	 */
	public function dispatch($forward, $router, $display) {
		$this->checkToken($router, false);
		if ($forward->getIsRedirect())
			$this->dispatchWithRedirect($forward, $router);
		elseif ($forward->getIsReAction())
			$this->dispatchWithAction($forward, $router, $display);
		else {
			$view = $forward->getWindView();
			if ($view->templateName) {
				$vars = $forward->getVars();
				Wind::getApp()->getResponse()->setData($vars, $view->templateName);
				Wind::getApp()->getResponse()->setData($vars['G'], '', true);
				$view->render($this->display);
			}
			$this->display = false;
		}
	}

	/**
	 * 请求分发一个重定向请求
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 * @return
	 */
	protected function dispatchWithRedirect($forward, $router) {
		$_url = $forward->getUrl();
		if (!$_url && $forward->getIsReAction()) {
			$_url = $this->_getUrlHelper()->createUrl($forward->getAction(), $forward->getController(), 
				$forward->getArgs());
			if ($this->checkToken($router))
				throw new WindFinalException(
					'[web.WindDispatcher.dispatchWithRedirect] Duplicate request: ' . $this->token, 
					WindException::ERROR_SYSTEM_ERROR);
		
		} else
			$_url = $this->_getUrlHelper()->checkUrl($_url);
		$this->getResponse()->sendRedirect($_url);
	}

	/**
	 * 请求分发一个操作请求
	 * module/controller/action/?param
	 * @param WindForward $forward
	 * @param WindRouter $router
	 * @param boolean $display
	 * @return
	 */
	protected function dispatchWithAction($forward, $router, $display) {
		if (!$action = $forward->getAction())
			throw new WindException('[web.WindDispatcher.dispatchWithAction] forward fail.', 
				WindException::ERROR_PARAMETER_TYPE_ERROR);
		
		$args = $forward->getArgs();
		$this->display = $display;
		list($action, $_args) = explode('?', $action . '?');
		$action = trim($action, '/') . '/';
		$action = explode('/', $action);
		end($action);
		if ($_tmp = prev($action))
			$router->setAction($_tmp);
		if ($_tmp = prev($action))
			$router->setController($_tmp);
		if ($_tmp = prev($action))
			$router->setModule($_tmp);
		if ($this->checkToken($router))
			throw new WindFinalException(
				'[web.WindDispatcher.dispatchWithRedirect] Duplicate request: ' . $this->token, 
				WindException::ERROR_SYSTEM_ERROR);
		
		Wind::getApp()->processRequest();
	}

	/**
	 * 检查请求是否是重复请求
	 * @param WindUrlBasedRouter $router
	 * @param boolean $check
	 * @return boolean
	 */
	protected function checkToken($router, $check = true) {
		$token = $router->getModule() . '/' . $router->getController() . '/' . $router->getAction();
		if ($check === false) {
			$this->token = $token;
		} else
			return !strcasecmp($token, $this->token);
	}

}
