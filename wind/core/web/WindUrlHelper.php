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

	const REWRITE = false;

	const ROUTE_SEPARATOR = '_';

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
	 * 获得则匹配RequestUri，根据用户的配置分隔符分割信息
	 * 同时设置到超全局变量$_GET中
	 */
	public function parseUrl() {
		if ((($uri = $this->request->getServer('QUERY_STRING')) == '') || !$this->isRewrite()) return;
		if (($pattern = $this->getUrlPattern()) == '') return;
		$seperator = isset($pattern[1]) ? $pattern[1] : $pattern[0];
		$uri = explode($seperator, $uri);
		if (strcasecmp($pattern, "=&") != 0) $params = $this->parseUrlToParams($uri, $seperator, $pattern[0]);
		$_GET = array_merge($_GET, $params);
		$this->matchRouter(array_pop($uri));
	}

	/**
	 * 构造返回Url地址
	 * 
	 * 将根据是否开启url重写来分别构造相对应的url
	 * 
	 * @param string $action 执行的操作
	 * @param string $controller 执行的controller
	 * @param array $params 附带的参数
	 * @return string
	 */
	public function createUrl($action, $controller, $params = array()) {
		$action && $this->getWindRouter()->setAction($action);
		list($_c, $_m) = WindBase::resolveController($controller);
		$_c && $this->getWindRouter()->setController($_c);
		$_m && $this->getWindRouter()->setModule($_m);
		$url = $this->getWindRouter()->buildUrl();
		$server = $this->getUrlServer();
		if ($this->isRewrite()) {
			$server = substr($server, 0, strrpos($server, '/'));
			$url = $server . $this->buildRewriteURL($params, $url);
		} else {
			$url = $server . $url . '&' . $this->buildUrl($params);
		}
		return $url;
	}

	/**
	 * 返回域名及请求路径
	 * 
	 * @param boolean $hasPath 是否含有路径信息
	 * @return string 
	 */
	private function getUrlServer($hasPath = true) {
		list($protocol, ) = explode('/', $this->request->getProtocol());
		$protocol = strtolower($protocol) . '://' . $this->request->getServer('SERVER_NAME');
		($hasPath) && $protocol .= $this->request->getServer('PHP_SELF');
		return $protocol;
	}

	/**
	 * 执行匹配
	 * 
	 * 获得匹配的结果
	 * 对于路由信息，
	 * 如果没有路由信息，则表示缺省
	 * 如果路由中只有一个值，则代表缺省的是m和a
	 * 如果路由中只有两个值，则代表缺省的是m
	 * 
	 * @param string 待分析匹配的路由信息
	 */
	private function matchRouter($mca) {
		if (strrpos($mca, '.' . $this->getRouteSuffix()) === false) return;
		$mca = trim(rtrim($mca, '.' . $this->getRouteSuffix()));
		if ($mca == '') return;
		$mca = explode(self::ROUTE_SEPARATOR, $mca);
		$m = $this->getUrlParamConfig(WindUrlBasedRouter::URL_RULE_MODULE);
		$c = $this->getUrlParamConfig(WindUrlBasedRouter::URL_RULE_CONTROLLER);
		$a = $this->getUrlParamConfig(WindUrlBasedRouter::URL_RULE_ACTION);
		if (count($mca) == 1) {
			$_GET[$c] = $mca[0];
		} elseif (count($mca) == 2) {
			$_GET[$c] = $mca[0];
			$_GET[$a] = $mca[1];
		} else {
			($mca[0]) && $_GET[$m] = $mca[0];
			($mca[1]) && $_GET[$c] = $mca[1];
			($mca[2]) && $_GET[$a] = $mca[2];
		}
		return;
	}

	/**
	 * 解析uri参数成key-value关联数组形式
	 * 
	 * 如果key-value和参数之间，两种的分隔符相同，则采用配对匹配的模式，如果不相同，则对每一对key-value的值进行再次解析
	 * 
	 * 如果key是字符串，则直接赋值，
	 * 如果key是数组：
	 * 如果key的数组没有键值，则采用数组索引自增的方式
	 * 如果key的数组拥有键值，则将该键值作为key来传递
	 * 
	 * @param array $url
	 * @param string $seprator
	 * @param string $keyAsValue
	 * @return array
	 */
	private function parseUrlToParams($url, $seprator = '', $keyAsValue = '=') {
		$params = array();
		if ($seprator == $keyAsValue) {
			$n = count($url);
			for ($i = 0; $i < $n / 2; $i++) {
				$k = 2 * $i;
				$v = $k + 1;
				if (isset($url[$v])) {
					$this->parseKey($params, $url[$k], $url[$v]);
				}
			}
			return $params;
		}
		foreach ((array) $url as $key => $value) {
			if (strpos($value, $keyAsValue) === false) continue;
			list($key, $value) = explode($keyAsValue, $value);
			$this->parseKey($params, $key, $value);
		}
		return $params;
	}

	/**
	 * 解析url的parama信息中的key值
	 * 
	 * 如果key值不存在'[',']'字符，则该key为字符串，直接返回
	 * 如果key值存在，并且'['和']'之前没有字符，则表示该key是将是一个数组，并且键值自增，返回array($key)
	 * 如果key值存在，并且'['和']'之前有字符，则表示该key是一个数组，并且'['和']'其中的字符是该数组中的键值,返回array(key值, 键值)
	 * 
	 * //TODO 需要考虑多维数组的情况
	 * @param string $key
	 * @return string|array 返回匹配的结果
	 */
	private function parseKey(&$params, $key, $value) {
		if (($pos = strpos($key, '[')) === false || ($pos2 = strpos($key, ']', $pos + 1)) === false) {
			$params[$key] = $value;
			return;
		}
		$name = substr($key, 0, $pos);
		if ($pos2 === $pos + 1) {
			$params[$name][] = $value;
			return;
		} else {
			$key = substr($key, $pos + 1, $pos2 - $pos - 1);
			$params[$name][$key] = $value;
			return;
		}
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
		$_config = $this->getWindRouter()->getConfig(WindUrlBasedRouter::URL_RULE);
		if ($_param = $this->getConfig($type, WindUrlBasedRouter::URL_PARAM, $_config)) {
			return $_param;
		}
		return $type;
	}

	/**
	 * 获得分割符
	 * 
	 * @return array array(key-value分割符，参数之间分隔符)
	 */
	private function getSeparator() {
		(($pattern = $this->getUrlPattern()) == '') && $pattern = '=&';
		$separator = isset($pattern[1]) ? $pattern[1] : $pattern[0];
		return array($pattern[0], $separator);
	}

	/**
	 * 构造重写的url
	 *
	 * @param array $params
	 * @param string $routerInfo
	 * @return string 返回重写后的url
	 */
	private function buildRewriteURL($params, $routerInfo) {
		$routerInfo = $this->parseUrlToParams(explode('&', trim($routerInfo, '?')), '&', '=');
		$routerInfo = implode(self::ROUTE_SEPARATOR, $routerInfo) . '.' . $this->getRouteSuffix();
		$separator = $this->getSeparator();
		if (empty($params)) return $separator[1] . $routerInfo;
		$url = '';
		foreach ((array)$params as $key => $value) {
			$url .= $this->buildKey($key, $value, $separator[0], $separator[1]) . $separator[1];
		}
		return $separator[1] . $url . $routerInfo;
	}

	/**
	 * 构造url的辅助函数
	 * 
	 * 支持数组的传递(建议最多传递一维)
	 *
	 * @param string $parentKey  key 
	 * @param string $parentValue key对应的值
	 * @param string $keyAsValue  key-value的分隔符
	 * @param string $separator   参数之间的分割符
	 * @param boolean $flag   标志
	 * @return string 
	 */
	private function buildKey($parentKey, $parentValue, $keyAsValue, $separator, $flag = false) {
		$flag && $parentKey = is_numeric($parentKey) ? '[]' : '[' . $parentKey . ']';
		if (!is_array($parentValue)) return $parentKey . $keyAsValue . urlencode($parentValue);
		$keys = array();
		foreach ($parentValue as $key => $value) {
			$keys[] = $parentKey . $this->buildKey($key, $value, $keyAsValue, $separator, true);
		}
		return implode($separator, $keys);
	}

	/**
	 * 构造普通的url
	 * 
	 * @param array $params
	 * @return string 
	 */
	private function buildUrl($params) {
		if (empty($params)) return '';
		$url = '';
		foreach ((array) $params as $key => $value) {
			$url .= $this->buildKey($key, $value, '=', '&', false) . '&';
		}
		return trim($url, '&');
	}

	/**
	 * 检查Url地址的正确性，并返回正确的URL地址
	 * 
	 * @param string $url
	 * @return string $url
	 */
	public function checkUrl($url) {
		list($protocal, $serverName) = explode('://', $this->getUrlServer(false));
		$pos1 = stripos($url, $protocal);
		$pos2 = stripos($url, $serverName);
		if (false === $pos1 && false === $pos2) return $protocal . '://' . $serverName . '/' . ltrim($url, '/');
		if (false === $pos1) return $protocal . '://' . ltrim($url, '/');
		return $url;
	}

	/**
	 * @return the $routeSuffix
	 */
	public function getRouteSuffix() {
		if ($this->routeSuffix === '') {
			$this->routeSuffix = $this->getConfig(self::ROUTE_SUFFIX, WindSystemConfig::VALUE);
		}
		return $this->routeSuffix;
	}

	/**
	 * @return the $routeParam
	 */
	public function getRouteParam() {
		if ($this->routeParam === '') {
			$this->routeParam = $this->getConfig(self::ROUTE_PARAM, WindSystemConfig::VALUE);
		}
		return $this->routeParam;
	}

	/**
	 * @return the $urlPattern
	 */
	public function getUrlPattern() {
		if ($this->urlPattern === '') {
			$this->urlPattern = $this->getConfig(self::URL_PATTERN, WindSystemConfig::VALUE);
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
	 * @return AbstractWindRouter $windRouter
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