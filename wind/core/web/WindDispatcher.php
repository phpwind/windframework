<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.WindComponentModule');
/**
 * 请求分发
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindDispatcher extends WindComponentModule {

	protected $oldRouter = null;

	protected $display = false;

	/**
	 * 请求分发处理
	 * @param WindForward $forward
	 */
	public function dispatch($forward) {
		$this->oldRouter = clone $this->windFactory->getInstance(COMPONENT_ROUTER);
		if ($forward->getIsRedirect())
			$this->dispatchWithRedirect($forward);
		elseif ($forward->getIsReAction())
			$this->dispatchWithAction($forward);
		else
			$this->render($forward);
		$this->destroy();
	}

	/**
	 * 请求分发一个重定向请求
	 * @param WindForward $forward
	 */
	protected function dispatchWithRedirect($forward) {
		$_url = $forward->getUrl();
		//TODO check $_url 在urlHelper中添加url检测方法，检查是否是一个正确的Url形式，包括包含不包含域名等
		$urlHelper = $this->windFactory->getInstance(COMPONENT_URLHELPER);
		if (!$_url && $forward->getIsReAction()) {
			/* @var $urlHelper WindUrlHelper */
			$_url = $urlHelper->createUrl($forward->getAction(), $forward->getController(), $forward->getArgs());
		}
		$_url = $urlHelper->checkUrl($_url);
		$_router = $this->windFactory->getInstance(COMPONENT_ROUTER);
		$_router->reParse();
		if (!$this->checkProcess($_router)) {
			throw new WindException('Duplicate request ' . $_router->getAction() . '_' . $_router->getController() . '.' . $_router->getModule());
		}
		$this->response->sendRedirect($_url);
	}

	/**
	 * 请求分发一个操作请求
	 * @param WindForward $forward
	 */
	protected function dispatchWithAction($forward) {
		$_a = $forward->getAction();
		list($_c, $_m) = W::resolveController($forward->getController());
		
		/* @var $_router WindUrlBasedRouter */
		$_router = $this->windFactory->getInstance(COMPONENT_ROUTER);
		$_a && $_router->setAction($_a);
		$_c && $_router->setController($_c);
		$_m && $_router->setModule($_m);
		if (!$this->checkProcess($_router)) {
			throw new WindException('Duplicate request ' . $_router->getAction() . '_' . $_router->getController() . '.' . $_router->getModule());
		}
		
		$appName = $this->windSystemConfig->getAppClass();
		$application = $this->windFactory->getInstance($appName);
		$application->processRequest();
	}

	/**
	 * 检查请求是否是重复请求
	 * @param AbstractWindRouter $router
	 * @param string $action
	 * @param string $controller
	 */
	protected function checkProcess($router) {
		if ($router->getAction() !== $this->oldRouter->getAction()) return true;
		if ($router->getController() !== $this->oldRouter->getController()) return true;
		if ($router->getModule() !== $this->oldRouter->getModule()) return true;
		return false;
	}

	/**
	 * 进行视图渲染
	 * @param WindForward $forward
	 */
	protected function render($forward) {
		if ($forward && null !== ($windView = $forward->getWindView())) {
			if ($windView->getTemplateName() === '') return;
			$viewResolver = $windView->getViewResolver();
			$this->response->setData($forward->getVars(), $windView->getTemplateName());
			if ($this->display === false)
				$this->response->setBody($viewResolver->windFetch(), $windView->getTemplateName());
			else
				$viewResolver->displayWindFetch();
		} else
			throw new WindException('unable to create the object with forward.');
	}

	/* (non-PHPdoc)
	 * @see WindModule::getWriteTableForGetterAndSetter()
	 */
	public function getWriteTableForGetterAndSetter() {
		return array('display');
	}

	/**
	 * 注销当前dispatcher状态
	 */
	protected function destroy() {
		$this->display = false;
		$this->oldRouter = null;
	}
}
