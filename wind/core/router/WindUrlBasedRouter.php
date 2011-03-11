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
	const URL_PARAM = 'url-param';

	const DEFAULT_VALUE = 'default-value';

	/* url 后缀名参数规则 */
	const CONTROLLER_SUFFIX = 'controller-suffix';

	const ACTION_SUFFIX = 'action-suffix';

	const URL_RULE_MODULE = 'module';

	const URL_RULE_CONTROLLER = 'controller';

	const URL_RULE_ACTION = 'action';

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::parse()
	 */
	public function parse() {
		$this->setModule($this->getUrlParamValue(self::URL_RULE_MODULE, $this->request, $this->module));
		$this->setController($this->getUrlParamValue(self::URL_RULE_CONTROLLER, $this->request, $this->controller));
		$this->setAction($this->getUrlParamValue(self::URL_RULE_ACTION, $this->request, $this->action));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::getHandler()
	 */
	public function getHandler() {
		$moduleConfig = $this->windSystemConfig->getModules($this->getModule());
		$_controllerPath = $this->windSystemConfig->getConfig(WIND_CONFIG_CLASSPATH, '', $moduleConfig, $this->defaultControllerPath);
		$_suffix = $this->windSystemConfig->getConfig(self::CONTROLLER_SUFFIX, WIND_CONFIG_VALUE, $moduleConfig, $this->defaultControllerSuffix);
		$_path = $this->modulePath ? $this->modulePath : $_controllerPath;
		$_path .= '.' . ucfirst($this->controller) . $_suffix;
		if (strpos($_path, ':') === false) $_path = strtoupper($this->windSystemConfig->getAppName()) . ':' . $_path;
		
		//add log
		if (IS_DEBUG) {
			/* @var $logger WindLogger */
			$logger = $this->windFactory->getInstance(COMPONENT_LOGGER);
			$logger->debug('do getHandler of ' . __CLASS__);
		}
		
		return $_path;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRouter::buildUrl()
	 */
	public function buildUrl() {
		$module = $this->getUrlParamValue(self::URL_RULE_MODULE);
		$controller = $this->getUrlParamValue(self::URL_RULE_CONTROLLER);
		$action = $this->getUrlParamValue(self::URL_RULE_ACTION);
		$url = '?' . $module . '=' . $this->getModule();
		$url .= '&' . $controller . '=' . $this->getController();
		$url .= '&' . $action . '=' . $this->getAction();
		return $url;
	}

	/**
	 * Enter description here ...
	 * @param urlParam
	 * @param request
	 * @param defaultValue
	 * @return string 
	 */
	private function getUrlParamValue($type, $request = null, $defaultValue = '') {
		if ($_param = $this->getConfig()->getConfig($type, self::URL_PARAM)) {
			if (is_null($request)) return $_param;
			$_defaultValue = $this->getConfig()->getConfig($type, self::DEFAULT_VALUE);
			$defaultValue = $_defaultValue ? $_defaultValue : $defaultValue;
			return $request->getAttribute($_param, $defaultValue);
		}
		return $defaultValue;
	}

}