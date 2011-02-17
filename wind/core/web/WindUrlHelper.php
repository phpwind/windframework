<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindUrlHelper extends WindComponentModule {

	const URL_PATTERN = 'url-pattern';

	const ROUTE_SUFFIX = 'route-suffix';

	const ROUTE_PARAM = 'route-param';

	protected $routeSuffix = '';

	protected $routeParam = '';

	protected $urlPattern = '';

	protected $windRouter = null;

	/**
	 * 解析Url
	 */
	public function parseUrl() {
		//TODO
	}

	/**
	 * 返回Url地址
	 * 
	 * @return string
	 */
	public function createUrl($action, $controller, $params = array()) {
		//TODO
	}

	/**
	 * 检查Url地址的正确性，并返回正确的URL地址
	 * 
	 * @param string $url
	 */
	public function checkUrl($url) {
		//TODO
		

		return $url;
	}

	/**
	 * @return the $routeSuffix
	 */
	public function getRouteSuffix() {
		if ($this->routeSuffix === '') {
			$this->routeSuffix = $this->getConfig()->getConfig(self::ROUTE_SUFFIX, WindSystemConfig::VALUE);
		}
		return $this->routeSuffix;
	}

	/**
	 * @return the $routeParam
	 */
	public function getRouteParam() {
		if ($this->routeParam === '') {
			$this->routeParam = $this->getConfig()->getConfig(self::ROUTE_PARAM, WindSystemConfig::VALUE);
		}
		return $this->routeParam;
	}

	/**
	 * @return the $urlPattern
	 */
	public function getUrlPattern() {
		if ($this->urlPattern === '') {
			$this->urlPattern = $this->getConfig()->getConfig(self::URL_PATTERN, WindSystemConfig::VALUE);
		}
		return $this->urlPattern;
	}

	/**
	 * @param field_type $routeSuffix
	 */
	public function setRouteSuffix($routeSuffix) {
		$this->routeSuffix = $routeSuffix;
	}

	/**
	 * @param field_type $routeParam
	 */
	public function setRouteParam($routeParam) {
		$this->routeParam = $routeParam;
	}

	/**
	 * @param field_type $urlPattern
	 */
	public function setUrlPattern($urlPattern) {
		$this->urlPattern = $urlPattern;
	}

	/**
	 * @return the $windRouter
	 */
	public function getWindRouter() {
		return $this->windRouter;
	}

	/**
	 * @param field_type $windRouter
	 */
	public function setWindRouter($windRouter) {
		$this->windRouter = $windRouter;
	}

}

?>