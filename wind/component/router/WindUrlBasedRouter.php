<?php
/**
 * 基于URL的路由解析器.
 * 该解析器通过访问一个Http请求的Request对象来获得URL的参数信息
 * 并将其参数根据已定义的路由规则进行解析.
 * 通过该方法的getActionHandle方法返回一个，操作处理的句柄信息
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @link WRouteParser
 * @package 
 */
class WindUrlBasedRouter extends AbstractWindRouter {
	/* url 路由参数规则 */
	const URL_PARAM = 'url-param';
	const DEFAULT_VALUE = 'default-value';
	/* 后缀名参数规则 */
	const CONTROLLER_SUFFIX = 'controller-suffix';
	const ACTION_SUFFIX = 'action-suffix';
	/* 路由信息 */
	const URL_RULE_MODULE = 'module';
	const URL_RULE_CONTROLLER = 'controller';
	const URL_RULE_ACTION = 'action';

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::parse()
	 */
	public function parse() {
		$urlHelper = $this->getSystemFactory()->getInstance(COMPONENT_URLHELPER);
		$urlHelper->parseUrl();
		$this->setModule($this->getUrlParamValue(self::URL_RULE_MODULE, $this->getModule()));
		$this->setController($this->getUrlParamValue(self::URL_RULE_CONTROLLER, $this->getController()));
		$this->setAction($this->getUrlParamValue(self::URL_RULE_ACTION, $this->getAction()));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::buildUrl()
	 */
	public function buildUrl() {
		$module = $this->getConfig(self::URL_RULE_MODULE, self::URL_PARAM);
		$controller = $this->getConfig(self::URL_RULE_CONTROLLER, self::URL_PARAM);
		$action = $this->getConfig(self::URL_RULE_ACTION, self::URL_PARAM);
		$url = '?' . $module . '=' . $this->getModule();
		$url .= '&' . $controller . '=' . $this->getController();
		$url .= '&' . $action . '=' . $this->getAction();
		return $url;
	}

	/**
	 * 返回路由的配置信息
	 * 
	 * @param urlParam
	 * @param defaultValue
	 * @return string 
	 */
	private function getUrlParamValue($type, $defaultValue = '') {
		if ($_param = $this->getConfig($type, self::URL_PARAM)) {
			$_defaultValue = $this->getConfig($type, self::DEFAULT_VALUE, $defaultValue);
			return $this->getRequest()->getRequest($_param, $defaultValue);
		}
		return $defaultValue;
	}
}