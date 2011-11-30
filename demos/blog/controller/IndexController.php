<?php
Wind::import('model.UserForm');
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
	/**
	 * @var UserForm
	 */
	private $userInfo;
	/**
	 * @var WindSession
	 */
	private $session;

	/* (non-PHPdoc)
	 * @see WindSimpleController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$this->session = $this->getSystemFactory()->getInstance('windSession');
		$this->userInfo = $this->session->get('user');
		$this->setLayout('layout');
		$this->setTheme('default', $this->getRequest()->getBaseUrl(true) . '/' . 'static');
		$this->setOutput('utf8', 'charset');
	}

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$this->setOutput($this->userInfo, 'userInfo');
		$this->setTemplate('index');
	}

	/**
	 * 用户登录
	 */
	public function loginAction() {
		if ($this->userInfo) $this->showMessage('已登录，请先注销.');
		/* @var $userForm UserForm */
		$userForm = $this->getInput("userForm");
		if (!$userForm) $this->showMessage('获取用户登录数据失败');
		
		/* @var $db WindConnection */
		$db = $this->getSystemFactory()->getInstance('db');
		$stmt = $db->createStatement('SELECT userid FROM user WHERE username=:username AND password =:password');
		if (!$stmt->getValue(array('username' => $userForm->getUsername(), 'password' => $userForm->getPassword()))) {
			$this->showMessage('登录失败.');
		}
		$this->session->set('user', $userForm);
		$this->forwardRedirect(WindUrlHelper::createUrl('run'));
	}

	/**
	 * 访问用户注册页面
	 */
	public function regAction() {
		$this->setTemplate('reg');
	}

	/**
	 * 处理用户注册表单
	 */
	public function dregAction() {
		$this->session->destroy();
		if ($userForm = $this->getInput("userForm")) {
			$db = $this->getSystemFactory()->getInstance('db');
			$stmt = $db->createStatement('SELECT * FROM user WHERE username=:username');
			if ($stmt->getOne(array(':username' => $userForm->getUsername()))) $this->showMessage('该用户已经注册.');
			if (!$db->execute(
				"INSERT INTO user SET " . $db->sqlSingle(
					array('username' => $userForm->getUsername(), 'password' => $userForm->getPassword())))) $this->showMessage(
				'注册失败.');
			$this->setOutput($userForm, 'userInfo');
		}
		$this->setTemplate('reg');
	}

	/**
	 * 用户退出
	 */
	public function logoutAction() {
		$this->session->destroy();
		$this->forwardRedirect(WindUrlHelper::createUrl('run'));
	}
}