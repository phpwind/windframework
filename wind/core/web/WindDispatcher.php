<?php
Wind::import('COM:viewer.exception.WindViewException');
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
	protected $processCache = array();
	/**
	 * @var boolean
	 */
	protected $display = false;

	/**
	 * 请求分发处理
	 * 
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 * @param boolean $display
	 * @return
	 */
	public function dispatch($forward, $router, $display) {
		$this->checkProcess($router, false);
		if ($forward->getIsRedirect())
			$this->dispatchWithRedirect($forward, $router);
		elseif ($forward->getIsReAction())
			$this->dispatchWithAction($forward, $router, $display);
		else
			$this->render($forward, $router);
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
			$_url = $this->_getUrlHelper()->createUrl($forward->getAction(), 
				$forward->getController(), $forward->getArgs());
			$router->reParse();
			if (!$this->checkProcess($router)) {
				throw new WindFinalException(
					'[core.web.WindDispatcher.dispatchWithRedirect] Duplicate request: ' . $router->getController() . ',' . $router->getAction(), 
					WindException::ERROR_SYSTEM_ERROR);
			}
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
			throw new WindException('[core.web.WindDispatcher.dispatchWithAction] forward fail.', 
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
		if (!$this->checkProcess($router)) {
			throw new WindFinalException(
				'[core.web.WindDispatcher.dispatchWithRedirect] Duplicate request: ' . $router->getController() . ',' . $router->getAction(), 
				WindException::ERROR_SYSTEM_ERROR);
		}
		Wind::getApp()->processRequest();
	}

	/**
	 * 进行视图渲染
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 * @return
	 */
	protected function render($forward, $router) {
		if ($windViewClass = $forward->getWindView()) {
			$_className = Wind::import($windViewClass);
			$view = $this->getSystemFactory()->createInstance($windViewClass);
		} else
			$view = $this->getSystemFactory()->getInstance('windView');
		$view->render($forward, $router, $this->display);
		$this->display = false;
	}

	/**
	 * 检查请求是否是重复请求
	 * @param WindUrlBasedRouter $router
	 * @param boolean $check
	 * @return boolean
	 */
	protected function checkProcess($router, $check = true) {
		if ($check === false) {
			$this->processCache['action'] = $router->getAction();
			$this->processCache['controller'] = $router->getController();
			$this->processCache['module'] = $router->getModule();
		} elseif ($router->getAction() === @$this->processCache['action'] && $router->getController() === @$this->processCache['controller'] && $router->getModule() === @$this->processCache['module'])
			return false;
		return true;
	}

}
