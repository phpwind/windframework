<?php
/**
 * 
 * Enter description here ...
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class IndexController extends WindController {
	
	protected $viewer = array();
	const LOGIN_SUCCESS = 1;
  
	/**
	 * @see WindSimpleController::run()
	 */
	public function run() {
		//指定模版
		$this->setTemplate('index_run');
	}

	/**
	 * postAction请求
	 */
	public function postAction() {
		if ($this->getSession()->isRegistered('status')) {
			$this->viewer['info'] = '已登录，请先注销！';
		} else {
			//获得经过验证的表单
			$loginForm = $this->getInput("loginForm");
			$result = $this->login($loginForm->getUsername(), $loginForm->getPassword());
			if (!$result) {
				$this->viewer['info'] = '用户名、密码不正确！';
			} else {
				$this->getSession()->set('status', self::LOGIN_SUCCESS);
				$this->getSession()->set('username', $loginForm->getUsername());
			}
		}
		$this->setTemplate('index_run');
	}

	/**
	 * logOutAction请求
	 */
	public function logOutAction() {
		$this->getSession()->destroy();
		$this->forwardRedirect("index.php");
	}

	/**
	 * 处理每个Action请求前先调用的方法
	 * @see WindSimpleController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		$this->definedShareVars();
	}

	private function definedShareVars() {
		$baseUrl = $this->getRequest()->getBaseUrl(true);
		$this->viewer['baseUrl'] = $baseUrl;
	}

	/**
	 * 完成每个Action请求后调用的方法
	 * @see WindSimpleController::afterAction()
	 */
	public function afterAction($handler) {
		parent::afterAction($handler);
		$this->setOutput($this->viewer);
	}

	/**
	 * 获取windSession组件实例
	 * @return
	 */
	public function getSession() {
		return $this->getSystemFactory()->getInstance('windSession');
	}
	
	/**
	 * 登录业务处理,获取db组件实例
	 *
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return array
	 */
	public function login($username, $password) {
		$db = $this->getSystemFactory()->getInstance('db');
		if (!$db instanceof WindConnection) $this->addMessage("database connect error");
		$sql = "SELECT * FROM user WHERE username = " . $db->quote($username) . " AND password = " . $db->quote($password);
		return $db->query($sql)->fetch();
	}

}