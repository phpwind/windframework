<?php
Wind::import('WIND:token.IWindSecurityToken');
/**
 * token令牌安全类
 * 
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-19
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package utility
 */
class WindSecurityToken extends WindModule implements IWindSecurityToken {
	/**
	 * 令牌容器
	 * 
	 * 可以通过组件配置方式配置不同的容器类型
	 * @var WindSession
	 */
	protected $tokenContainer = null;

	/* (non-PHPdoc)
	 * @see IWindSecurityToken::saveToken($tokenName)
	 */
	public function saveToken($tokenName = '') {
		/* @var $tokenContainer WindSession */
		$tokenContainer = $this->_getTokenContainer();
		$tokenName = $this->getTokenName($tokenName);
		$_token = WindSecurity::createToken();
		$tokenContainer->set($tokenName, $_token);
		return $_token;
	}

	/* (non-PHPdoc)
	 * @see IWindSecurityToken::validateToken()
	 */
	public function validateToken($tokenName = '') {
		/* @var $tokenContainer WindSession */
		$tokenContainer = $this->_getTokenContainer();
		$tokenName = $this->getTokenName($tokenName);
		if ($tokenContainer->get($tokenName)) {
			$tokenContainer->delete($tokenName);
			return true;
		}
		return false;
	}

	/**
	 * token名称处理
	 * 
	 * @param string $tokenName
	 */
	protected function getTokenName($tokenName) {
		$tokenName || $tokenName = Wind::getAppName();
		return substr(md5('_token' . $tokenName), -16);
	}

}

?>