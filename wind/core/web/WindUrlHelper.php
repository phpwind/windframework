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
	
	const REWRITE = true;

	protected $routeSuffix = '';

	protected $routeParam = '';

	protected $urlPattern = '';

	protected $windRouter = null;
	
	public function isRewrite() {
		return self::REWRITE;
	}

	/**
	 * 解析Url
	 * 
	 * 没有配置解析规则，直接返回
	 * 获得则匹配RequestUri，与用户配置的url正则匹配
	 * 同时设置到超全局变量$_GET中
	 */
	public function parseUrl() {
		if ((($uri = $this->request->getServer('QUERY_STRING')) == '') || !$this->isRewrite()) return;
		if (($pattern = $this->getUrlPattern()) == '') return;
		if (count($match = $this->matchPattern($uri, $pattern)) == 0) ;//return;
		$_GET = array_merge($_GET, $match);
	}
	
	/**
	 * 执行匹配
	 * 
	 * 获得匹配的结果
	 * @return array 返回匹配的结果
	 */
	private function matchPattern($uri, $pattern) {
		$seperator = isset($pattern[1]) ? $pattern[1] : $pattern[0];
		$uri = explode($seperator, $uri);
		if (strcasecmp($pattern, "=&") != 0) $params = $this->parseUrlToParams($uri, $seperator, $pattern[0]);
		if (strrpos($uri[count($uri)-1], '.' . $this->getRouteSuffix()) !== false) {
			$mca = rtrim(array_pop($uri), '.' . $this->getRouteSuffix());
			if ($mca == '') return $params;
			$mca = explode('-', $mca);
			(count($mca) == 2) && array_unshift($mca, '');
			$mcaConfig = array();
			$mcaConfig[] = $this->getUrlParamConfig(WindUrlBasedRouter::URL_RULE_MODULE);
			$mcaConfig[] = $this->getUrlParamConfig(WindUrlBasedRouter::URL_RULE_CONTROLLER);
			$mcaConfig[] = $this->getUrlParamConfig(WindUrlBasedRouter::URL_RULE_ACTION);
			$params = array_merge($params, array_combine($mcaConfig, $mca));
		}
		return $params;
	}
	
	/**
	 * 获得配置
	 * 
	 * 获得用户对于module  controller action的对应配置（url-parmar)
	 * 
	 * @param string $type 查找类型（module,controller,action);
	 * @param string $type 返回用户对应的设置，如果不存在则返回本身
	 */
	private function getUrlParamConfig($type) {
		$_config = $this->getWindRouter()->getConfig()->getConfig(WindUrlBasedRouter::URL_RULE);
		if ($_param = $this->getConfig()->getConfig($type, WindUrlBasedRouter::URL_PARAM, $_config)) {
			return $_param;
		}
		return $type;
	}
	
	private function buildRewriteURL($params, $mca) {
		(($pattern = $this->getUrlPattern()) == '') && $pattern = '=&';
		$seprator = isset($pattern[1]) ? $pattern[1] : $pattern[0];
		$url = '';
		foreach ($params as $key => $value) {
			$url .= $key . $pattern[0] . $value . $seprator;
		}
		$mca = $this->parseUrlToParams(explode('&', trim($mca, '?')), '&', '=');
		$mca = implode('-', $mca) . '.' . $this->getRouteSuffix();
		return $seprator . $url . $mca;
	}
	
    private function parseUrlToParams($url, $seprator = '',  $keyAsValue = '=') {
    	$params = array();
    	if ($seprator == $keyAsValue) {
    		$n = count($url);
    		for($i = 0; $i < $n/2; $i++) {
    			$k = 2 * $i;
    			$v = $k + 1;
    			isset($url[$v]) && $params[$url[$k]] = $url[$v];
    		}
    		return $params;
    	}
		foreach ((array)$url as $key => $value) {
			(strpos($value, $keyAsValue) !== false) && list($key, $value) = explode($keyAsValue, $value);
			$params[$key] = $value;
		}
		return $params;
    }
    
	/**
	 * 返回Url地址
	 * 
	 * @return string
	 */
	public function createUrl($action, $controller, $params = array()) {
		$this->getWindRouter()->setAction($action);
		$this->getWindRouter()->setController($controller);
		$url = $this->getWindRouter()->buildUrl();
		$server = $this->request->getServer('PHP_SELF');
		if ($this->isRewrite()) {
			$server = substr($server, 0, strrpos($server, '/'));
			$url = $server . $this->buildRewriteURL($params, $url);
		} else {
			$url = $server . $url . '&' . $this->buildParams($params);
		}
		return $url;
	}
	
	private function buildParams($params) {
		$url = '';
		foreach ((array)$params as $key => $value) {
			$url .= $key . '=' . $value . '&';
		}
		return trim($url, '&');
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