<?php
/**
 * 基于rewrite和二级域名的路由协议
 * 
 * 该类继承了抽象类{@see AbstractWindRoute},实现了{@see AbstractWindRoute::match()},
 * {@see AbstractWindRoute::build()}.
 * 要启用此路由协议，需要开启服务器的rewrite功能
 * 支持多应用，解析二级域名为app的值，如blog.p9.com则指向另外一个应用blog
 * 默认路由规则：<code>^http[s]?:\/\/[^\/]+\/(\w+)?(\/\w+)?(\/\w+)?(\/|\/?\?.*)*$
 * 例如：请求http://blog.p9.com/myModule/myController/myAction?id=1&name=2，
 * 则解析为app => blog, module => myModule, controller => myController, action => myAction,
 * GET参数id => 1, name => 2
 * </code>
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package router
 * @subpackage route
 */
class WindRewriteRoute extends AbstractWindRoute {
	
	protected $pattern = '^http[s]?:\/\/[^\/]+(\/\w+)?(\/\w+)?(\/\w+)?(\/|\/?\?.*)*$';
	protected $reverse = '/%s/%s/%s/';
	protected $separator = '&=';
	protected $params = array(
		'm' => array('map' => 1), 
		'c' => array('map' => 2), 
		'a' => array('map' => 3));

	/**
	 * 路由解析
	 * 
	 * 匹配这个patten时，将试图去解析module、controller和action值，并解析二级域名。
	 * @see AbstractWindRoute::match()
	 */
	public function match($request, $response) {
		$fullUrl = $request->getHostInfo() . $request->getRequestUri();
		$_pathInfo = trim(str_replace($request->getBaseUrl(), '', $fullUrl), '/');
		if (!$_pathInfo || !preg_match_all('/' . $this->pattern . '/i', trim($_pathInfo, '/'), 
			$matches)) return null;
		foreach ($this->params as $_n => $_p) {
			if (isset($_p['map']) && isset($matches[$_p['map']][0]))
				$_value = $matches[$_p['map']][0];
			else
				$_value = isset($_p['default']) ? $_p['default'] : '';
			$this->params[$_n]['value'] = $params[$_n] = trim($_value, '-/');
		}
		$host = $request->getHostInfo();
		if ($host != '' && ($pos1 = strpos($host, '://')) !== false && ($pos2 = strpos($host, '.')) !== false)
			$host = substr($host, $pos1 + 3, $pos2 - $pos1 - 3);
		else
			return null;
		$params['p'] = $host == 'www' ? '' : $host;
		list(, $_args) = explode('?', $_pathInfo . '?', 2);
		$_args = trim($_args, '?');
		$_args && $params = array_merge($params, 
			WindUrlHelper::urlToArgs($_args, true, $this->separator));
		return $params;
	}

	/**
	 * 在此路由协议的基础上组装url
	 * 
	 * @param AbstractWindRouter $router
	 * @param string $action 格式为app/module/controller/action
	 * @param array $args 附带的参数 
	 * @return string
	 * @see AbstractWindRoute::build()
	 */
	public function build($router, $action, $args = array()) {
		list($_a, $_c, $_m, $_p, $args) = WindUrlHelper::resolveAction($action, $args);
		$_args[] = $this->reverse;
		foreach ($this->params as $key => $val) {
			if (!isset($val['map'])) continue;
			if ($key === $router->getModuleKey())
				$_args[$val['map']] = $_m ? $_m : $router->getModule();
			elseif ($key === $router->getControllerKey())
				$_args[$val['map']] = $_c ? $_c : $router->getController();
			elseif ($key === $router->getActionKey())
				$_args[$val['map']] = $_a ? $_a : $router->getAction();
			else {
				if (isset($args[$key]))
					$_args[$val['map']] = $args[$key];
				elseif (isset($val['value']))
					$_args[$val['map']] = $val['value'];
				else
					$_args[$val['map']] = '';
			}
			unset($args[$key]);
		}
		ksort($_args);
		$url = call_user_func_array("sprintf", $_args);
		$args && $url .= '?' . WindUrlHelper::argsToUrl($args, true, $this->separator);
		
		$p = $_p ? $_p : ($router->getApp() ? $router->getApp() : 'www');
		$baseUrl = Wind::getApp()->getRequest()->getBaseUrl(true);
		$_baseUrl = $this->replaceStr($baseUrl, $p);
		
		return trim($_baseUrl, '/') . '/' . trim($url, '/');
	}

	/**
	 * 替换二级域名，生成baseurl
	 * 
	 * @param string $url
	 * @param string $str
	 */
	private function replaceStr($url, $str) {
		$host = Wind::getApp()->getRequest()->getHostInfo();
		if (strpos($host, '://') === false || strpos($host, '.') === false) return $url;
		$arr = explode('.', $host, 2);
		$arr1 = explode('://', $arr[0]);
		$_host = $arr1[0] . '://' . $str . '.' . $arr[1];
		return str_replace($host, $_host, $url);
	}
	/* (non-PHPdoc)
	 * @see AbstractWindRoute::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->separator = $this->getConfig('separator', '', $this->separator);
	}
}

?>