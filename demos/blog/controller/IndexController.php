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
	
	/**
	 * @see WindSimpleController::run()
	 */
	public function run() {
		//获取输入数据
		$isRegister = $this->getInput("register");
		$param = $this->getFormParam($isRegister ? 'register' : 'login');
		//指定模版
		$this->setTemplate('index_run');
		
		//设置模版变量
		$this->setOutput(
			array('register' => $isRegister, 'param' => $param));
	}

	/**
	 * loginAction请求
	 */
	public function loginAction() {
		$info = '';
		if ($this->getSession()->isRegistered('status')) {
			$info = '已登录，请先注销！';
			$param = array();
		} else {
			//获得经过验证的表单
			$loginForm = $this->getInput("userForm");
			$result = $this->login($loginForm->getUsername(), $loginForm->getPassword());
			if (!$result) {
				$info = '用户名、密码不正确！';
				$param = $this->getFormParam('login');
			} else {
				$this->getSession()->set('status', 1);
				$this->getSession()->set('username', $loginForm->getUsername());
				$param = array();
			}
		}
		
		$this->setOutput(
			array(
				'info'      => $info, 
				'username'  => $this->getSession()->get("username"), 
				'status' 	=> $this->getSession()->get("status"), 
				'param' 	=> $param));
		$this->setTemplate('index_run');
	}

	/**
	 * 接收logOutAction请求
	 */
	public function logOutAction() {
		$this->getSession()->destroy();
		$this->forwardRedirect("index.php");
	}

	/**
	 * 接收registerAction请求
	 */
	public function registerAction() {
		$param = array();
		$isRegister = 0;
		if ($this->getSession()->isRegistered('status')) {
			$info = '已登录，请先注销！';
		} else {
			//获得经过验证的表单，使用和登录同一个model，见config配置
			$registerForm = $this->getInput("userForm");
			list($result, $info) = $this->register($registerForm->getUsername(), $registerForm->getPassword());
			if ($result) {
				$this->getSession()->set('status', 1);
				$this->getSession()->set('username', $registerForm->getUsername());
			} else {
				$param = $this->getFormParam('register');
				$isRegister = 1;
			}
		}
		
		$this->setOutput(array(
				'param'     => $param, 
				'info' 		=> $info, 
				'username' 	=> $this->getSession()->get("username"), 
				'status' 	=> $this->getSession()->get("status"), 
				'register' 	=> $isRegister,
			));
		$this->setTemplate('index_run');
	}

	/**
	 * 获取windSession组件实例
	 * @return WindSession
	 */
	private function getSession() {
		return $this->getSystemFactory()->getInstance('windSession');
	}

	/**
	 * 登录业务处理, 获取db组件实例
	 *
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return array
	 */
	private function login($username, $password) {
		$db = $this->getSystemFactory()->getInstance('db');
		$stmt = $db->createStatement('SELECT count(*) FROM user WHERE username=:username AND password =:password');
		return $stmt->getValue(array('username' => $username, 'password' => $password));
	}

	/**
	 * 注册业务处理
	 * 
	 * @param string $username
	 * @param string $password
	 * @return array
	 */
	private function register($username, $password) {
		$db = $this->getSystemFactory()->getInstance('db');
		$stmt = $db->createStatement('SELECT * FROM user WHERE username=:username');
		if ($stmt->getOne(array(':username' => $username))) {
			return array('0',"用户名已存在!");
		}
		$result = $db->execute(
			"INSERT INTO user SET " . $db->sqlSingle(
				array('username' => $username, 'password' => $password)));
		return array($result, $result ? "注册成功！" : "注册失败！");
	}
	
	/**
	 * 获取模版表单的参数
	 * 
	 * @param string $type
	 * @return array
	 */
	private function getFormParam($type) {
		$params = array();
		$params['login'] = array(
			'formName'  => 'loginForm', 
			'action' 	=> 'index.php?a=login', 
			'button' 	=> '登录');
		$params['register'] = array(
			'formName'  => 'registerForm', 
			'action' 	=> 'index.php?a=register', 
			'button' 	=> '注册');
	    return $params[$type];
	}

}