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

	const URL_RULE = 'url-rule';

	const URL_PARAM = 'url-param';

	const DEFAULT_VALUE = 'default-value';

	const URL_RULE_MODULE = 'module';

	const URL_RULE_CONTROLLER = 'controller';

	const URL_RULE_ACTION = 'action';

	protected $controllerSuffix = 'Controller';

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::doParser()
	 */
	public function doParse($request) {
		$this->module = $this->getValue($request, $this->module, self::URL_RULE_MODULE);
		$this->controller = $this->getValue($request, $this->controller, self::URL_RULE_CONTROLLER);
		$this->action = $this->getValue($request, $this->action, self::URL_RULE_ACTION);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::getHandler()
	 */
	public function getHandler($request, $response) {
		$windConfig = $request->getAttribute(WindFrontController::WIND_CONFIG);
		$moduleConfig = $windConfig->getModules($this->getModule());
		if (!$moduleConfig) {
			throw new WindException('Incorrect module config. undefined module ' . $this->getModule());
		}
		$controllerPath = $moduleConfig[WindSystemConfig::PATH] . '.' . ucfirst($this->controller) . $this->controllerSuffix;
		if (strpos($controllerPath, ':') === false) {
			$controllerPath = $windConfig->getAppName() . ':' . $controllerPath;
		}
		$controllerClassName = L::import($controllerPath);
		if (!class_exists($controllerClassName)) {
			throw new WindException($controllerClassName, WindException::ERROR_CLASS_NOT_EXIST);
		}
		return $controllerClassName;
	}

	/**
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param string $args 
	 */
	public function buildUrl($action = '', $controller = '', $module = '') {
		$keys = array_keys($this->rule);
		$baseUrl = $this->request->getBaseUrl(true);
		$script = $this->request->getScript();
		$url = '';
		if ($action && $action !== $this->rule[$keys[0]]) $url .= '&' . $keys[0] . '=' . $action;
		if ($controller && $controller !== $this->rule[$keys[1]]) $url .= '&' . $keys[1] . '=' . $controller;
		if ($module && $module !== $this->rule[$keys[2]]) $url .= '&' . $keys[2] . '=' . $module;
		if ($url !== '')
			$url = $baseUrl . '/' . $script . '?' . trim($url, '&');
		else
			$url = $baseUrl . '/' . $script;
		return $url;
	}

	/**
	 * Enter description here ...
	 * @param request
	 * @param urlParam
	 * @param defaultValue
	 */
	private function getValue($request, $defaultValue, $type) {
		if ($this->getConfig()->getConfig($type, self::URL_PARAM)) {
			$defaultValue = $this->getConfig()->getConfig($type, self::DEFAULT_VALUE) ? $this->getConfig()->getConfig($type, self::DEFAULT_VALUE) : $defaultValue;
			return $request->getAttribute($this->getConfig()->getConfig($type, self::URL_PARAM), $defaultValue);
		}
		return $defaultValue;
	}

}