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
	 *
	 * @var array
	 */
	protected $processCache = array();
	/**
	 * @var WindUrlHelper
	 */
	protected $urlHelper = null;
	
	/**
	 * @var boolean
	 */
	protected $display = false;

	/**
	 * 请求分发处理
	 * 
	 * @param WindWebApplication $app
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 * @return
	 */
	public function dispatch($app, $forward, $router) {
		$this->checkProcess($router, false);
		if ($forward->getIsRedirect())
			$this->dispatchWithRedirect($app, $forward, $router);
		elseif ($forward->getIsReAction())
			$this->dispatchWithAction($app, $forward, $router);
		else
			$this->render($app, $forward, $router);
		$this->destroy();
	}

	/**
	 * 请求分发一个重定向请求
	 * 
	 * @param WindWebApplication $app
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 * @return
	 */
	protected function dispatchWithRedirect($app, $forward, $router) {
		$_url = $forward->getUrl();
		if (!$_url && $forward->getIsReAction()) {
			$_url = $this->getUrlHelper()->createUrl($forward->getAction(), $forward->getController(), 
				$forward->getArgs());
			$router->reParse();
			if (!$this->checkProcess($router)) {
				throw new WindException('[core.web.WindDispatcher.dispatchWithRedirect] Duplicate request ', 
					WindException::ERROR_SYSTEM_ERROR);
			}
		} else
			$_url = $this->getUrlHelper()->checkUrl($_url);
		$this->getResponse()->sendRedirect($_url);
	}

	/**
	 * 请求分发一个操作请求
	 * 
	 * @param WindWebApplication $app
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 * @return
	 */
	protected function dispatchWithAction($app, $forward, $router) {
		//TODO 是否需要缓存上次请求的变量信息
		$this->getRequest()->setAttribute($forward->getVars(), 
			$router->getAction() . '_' . $router->getController());
		$this->setDisplay($forward->getDisplay());
		list($_c, $_m) = WindHelper::resolveController($forward->getController());
		$_a = $forward->getAction();
		$_a && $router->setAction($_a);
		$_c && $router->setController($_c);
		$_m && $router->setModule($_m);
		if (!$this->checkProcess($router)) {
			throw new WindException('[core.web.WindDispatcher.dispatchWithAction] Duplicate request ', 
				WindException::ERROR_SYSTEM_ERROR);
		}
		$app->processRequest();
	}

	/**
	 * 进行视图渲染
	 * 
	 * @param WindWebApplication $app
	 * @param WindForward $forward
	 * @param WindUrlBasedRouter $router
	 * @return
	 */
	protected function render($app, $forward, $router) {
		try {
			if ($windViewClass = $forward->getWindView())
				$view = $this->getSystemFactory()->createInstance($windViewClass);
			elseif ($windViewClass = $this->getSystemConfig()->getModuleViewClassByModuleName($router->getModule()))
				$view = $this->getSystemFactory()->getInstance($windViewClass);
			else
				$view = $this->getSystemFactory()->getInstance(COMPONENT_VIEW);
			$view->setConfig($this->getSystemConfig()->getModuleViewConfigByModuleName($router->getModule()));
			$view->render($forward, $router, $this->getDisplay());
		} catch (Exception $e) {
			throw new WindViewException('[core.web.WindDispatcher.render] view render fail.' . $e->getMessage());
		}
	}

	/**
	 * 检查请求是否是重复请求
	 * 
	 * @param WindUrlBasedRouter $router
	 * @param boolean $check
	 * @return boolean
	 */
	protected function checkProcess($router, $check = true) {
		if ($check === false) {
			$this->processCache['action'] = $router->getAction();
			$this->processCache['controller'] = $router->getController();
			$this->processCache['module'] = $router->getModule();
		} elseif ($router->getAction() === $this->processCache['action'] &&
			 $router->getController() === $this->processCache['controller'] &&
			 $router->getModule() === $this->processCache['module'])
				return false;
		return true;
	}

	/**
	 * 注销当前dispatcher状态
	 * 
	 * @return
	 */
	protected function destroy() {
		$this->processCache = array();
		$this->setDisplay(false);
	}

	/**
	 * @return boolean
	 */
	public function getDisplay() {
		return $this->display;
	}

	/**
	 * @param boolean $display
	 */
	public function setDisplay($display) {
		$this->display = $display;
	}

	/**
	 * @return WindUrlHelper
	 */
	public function getUrlHelper() {
		return $this->_getUrlHelper();
	}

	/**
	 * @param WindUrlHelper $urlHelper
	 */
	public function setUrlHelper($urlHelper) {
		$this->urlHelper = $urlHelper;
	}

}
