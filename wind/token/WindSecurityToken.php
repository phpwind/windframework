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
	 * url token
	 *
	 * @var string
	 */
	protected $token = null;
	/**
	 * 令牌容器
	 * 
	 * 可以通过组件配置方式配置不同的容器类型
	 * @var IWindHttpContainer
	 */
	protected $tokenContainer = null;

	/* (non-PHPdoc)
	 * @see IWindSecurityToken::saveToken($tokenName)
	 */
	public function saveToken($tokenName = '') {
		if ($this->token === null) {
			/* @var $tokenContainer IWindHttpContainer */
			$tokenContainer = $this->_getTokenContainer();
			$tokenName = $this->getTokenName($tokenName);
			if ($tokenContainer->isRegistered($tokenName)) {
				$_token = $tokenContainer->get($tokenName);
			} else {
				$_token = WindSecurity::generateGUID();
				$tokenContainer->set($tokenName, $_token);
			}
			$this->token = $_token;
		}
		return $this->token;
	}

	/* (non-PHPdoc)
	 * @see IWindSecurityToken::validateToken()
	 */
	public function validateToken($token, $tokenName = '') {
		/* @var $tokenContainer IWindHttpContainer */
		$tokenContainer = $this->_getTokenContainer();
		$tokenName = $this->getTokenName($tokenName);
		$_token = $tokenContainer->get($tokenName);
		return $_token && $_token === $token;
	}

	/* (non-PHPdoc)
	 * @see IWindSecurityToken::deleteToken()
	 */
	public function deleteToken($tokenName) {
		/* @var $tokenContainer IWindHttpContainer */
		$tokenContainer = $this->_getTokenContainer();
		$tokenName = $this->getTokenName($tokenName);
		return $tokenContainer->delete($tokenName);
	}

	/* (non-PHPdoc)
	 * @see IWindSecurityToken::getToken()
	 */
	public function getToken($tokenName) {
		/* @var $tokenContainer IWindHttpContainer */
		$tokenContainer = $this->_getTokenContainer();
		$tokenName = $this->getTokenName($tokenName);
		return $tokenContainer->get($tokenName);
	}

	/**
	 * token名称处理
	 * 
	 * @param string $tokenName
	 * @param string $suffix 默认值'csrf'
	 * @return string
	 */
	protected function getTokenName($tokenName, $suffix = 'csrf') {
		$tokenName || $tokenName = Wind::getAppName();
		return substr(md5('_token' . $tokenName . '_' . $suffix), -16);
	}
}

?>