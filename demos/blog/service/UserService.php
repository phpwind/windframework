<?php
Wind::import('WIND:utility.WindCookie');
/**
 * @author Qiong Wu <papa0924@gmail.com> 2012-3-15
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package demos
 * @subpackage blog.service
 */
class UserService {
	protected $cookieName = 'blogloginUser';

	/**
	 * 判断用户是否登录
	 * 
	 * @return boolean|UserForm
	 */
	public function isLogin() {
		/* @var $user UserForm */
		$user = WindCookie::get($this->cookieName);
		if (!$user) return false;
		$stmt = $this->_getConnecion()->createStatement('SELECT * FROM user WHERE username=:username');
		if (!$stmt->getValue(array('username' => $user->getUsername()))) return false;
		
		return $user;
	}

	/**
	 * 用户退出
	 * 
	 * @return boolean
	 */
	public function logout() {
		return WindCookie::set($this->cookieName, '', -1);
	}

	/**
	 * 用户登录服务
	 * 
	 * @param UserForm $userInfo
	 * @return boolean
	 */
	public function login($userInfo) {
		$db = $this->_getConnecion();
		$stmt = $db->createStatement('SELECT * FROM user WHERE username=:username AND password =:password');
		if (!$stmt->getValue(array('username' => $userInfo->getUsername(), 'password' => $userInfo->getPassword()))) {
			return false;
		}
		return WindCookie::set($this->cookieName, $userInfo);
	}

	/**
	 * 用户注册服务
	 * 
	 *@param UserForm $userInfo
	 *@return boolean
	 */
	public function register($userInfo) {
		$db = $this->_getConnecion();
		$stmt = $db->createStatement('SELECT * FROM user WHERE username=:username');
		if ($stmt->getOne(array(':username' => $userInfo->getUsername()))) $this->showMessage('该用户已经注册.');
		return $db->execute(
			"INSERT INTO user SET " . $db->sqlSingle(
				array('username' => $userInfo->getUsername(), 'password' => $userInfo->getPassword())));
	}

	/**
	 * @return WindConnection
	 */
	private function _getConnecion() {
		return Wind::getApp()->getWindFactory()->getInstance('db');
	}

}

?>