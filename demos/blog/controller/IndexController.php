<?php
/**
 * 默认的 controller
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package demos.blog.controller
 */
class IndexController extends WindController {

	/* (non-PHPdoc)
	 * @see WindSimpleController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$this->setLayout('layout');
		$this->setOutput('utf8', 'charset');
		$this->setGlobal($this->getRequest()->getBaseUrl(true) . '/template/images', 'images');
		$this->setGlobal($this->getRequest()->getBaseUrl(true) . '/template/images', 'css');
	}

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		Wind::import('service.UserForm');
		$userService = $this->load();
		$userInfo = $userService->isLogin();
		$this->setOutput($userInfo, 'userInfo');
		$this->setTemplate('index');
	}

	/**
	 * 访问用户注册页面
	 */
	public function regAction() {
		$this->setTemplate('reg');
	}

	/**
	 * 用户登录
	 */
	public function loginAction() {
		$userService = $this->load();
		$userInfo = $userService->isLogin();
		if ($userInfo) $this->showMessage('已登录~');
		
		/* @var $userForm UserForm */
		$userForm = $this->getInput("userForm");
		if (!$userForm) $this->showMessage('获取用户登录数据失败');
		
		if (!$userService->login($userForm)) $this->showMessage('登录失败.');
		$this->forwardRedirect(WindUrlHelper::createUrl('run'));
	}

	/**
	 * 处理用户注册表单
	 */
	public function dregAction() {
		$userService = $this->load();
		$userForm = $this->getInput("userForm");
		if (!$userService->register($userForm)) $this->showMessage('注册失败.');
		$this->setOutput($userForm, 'userInfo');
		$this->setTemplate('reg');
	}

	/**
	 * 用户退出
	 */
	public function logoutAction() {
		$this->load()->logout();
		$this->forwardRedirect(WindUrlHelper::createUrl('run'));
	}

	/**
	 * @return UserService
	 */
	private function load() {
		return Wind::getApp()->getWindFactory()->createInstance(Wind::import('service.UserService'));
	}
}