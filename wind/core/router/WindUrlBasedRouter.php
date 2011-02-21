<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.router.AbstractWindRouter');
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
	const URL_RULE = 'url-pattern';

	const URL_PARAM = 'url-param';

	const DEFAULT_VALUE = 'default-value';

	/* url 后缀名参数规则 */
	const CONTROLLER_SUFFIX = 'controller-suffix';

	const ACTION_SUFFIX = 'action-suffix';

	const URL_RULE_MODULE = 'module';

	const URL_RULE_CONTROLLER = 'controller';

	const URL_RULE_ACTION = 'action';

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::doParser()
	 */
	public function doParse() {
		$this->module = $this->getUrlPatternValue($this->request, $this->module, self::URL_RULE_MODULE);
		$this->controller = $this->getUrlPatternValue($this->request, $this->controller, self::URL_RULE_CONTROLLER);
		$this->action = $this->getUrlPatternValue($this->request, $this->action, self::URL_RULE_ACTION);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::getHandler()
	 */
	public function getHandler() {
		$moduleConfig = $this->windSystemConfig->getModules($this->getModule());
		if (!$moduleConfig) {
			throw new WindException('Incorrect module config. undefined module ' . $this->getModule());
		}
		$controllerSuffix = $this->getConfig()->getConfig(self::CONTROLLER_SUFFIX, WindSystemConfig::VALUE);
		$controllerPath = $moduleConfig[WindSystemConfig::PATH] . '.' . ucfirst($this->controller) . $controllerSuffix;
		if (strpos($controllerPath, ':') === false) {
			$controllerPath = $this->windSystemConfig->getAppName() . ':' . $controllerPath;
		}
		return $controllerPath;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::buildUrl()
	 */
	public function buildUrl() {
		$module = $this->getUrlParamConfig(self::URL_RULE_MODULE);
		$controller = $this->getUrlParamConfig(self::URL_RULE_CONTROLLER);
		$action = $this->getUrlParamConfig(self::URL_RULE_ACTION);
		$url = '?' . $module . '=' . $this->getModule();
		$url .= '&' . $controller . '=' . $this->getController();
		$url .= '&' . $action . '=' . $this->getAction();
		return $url;
	}
    
	private function getUrlParamConfig($type) {
		$_config = $this->getConfig()->getConfig(self::URL_RULE);
		if ($_param = $this->getConfig()->getConfig($type, self::URL_PARAM, $_config)) {
			return $_param;
		}
		return $type;
	}
	
	/**
	 * Enter description here ...
	 * @param request
	 * @param urlParam
	 * @param defaultValue
	 */
	private function getUrlPatternValue($request, $defaultValue, $type) {
		$_config = $this->getConfig()->getConfig(self::URL_RULE);
		if ($_param = $this->getConfig()->getConfig($type, self::URL_PARAM, $_config)) {
			$_defaultValue = $this->getConfig()->getConfig($type, self::DEFAULT_VALUE, $_config);
			$defaultValue = $_defaultValue ? $_defaultValue : $defaultValue;
			return $request->getAttribute($_param, $defaultValue);
		}
		return $defaultValue;
	}

}