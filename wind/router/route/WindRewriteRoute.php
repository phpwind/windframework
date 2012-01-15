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
	
	//protected $pattern = '^http[s]?:\/\/[^\/]+(\/\w+)?(\/\w+)?(\/\w+)?(\/|\/?\?.*)*$';
	protected $pattern = '^http[s]?:\/\/[^\/]+(\/\w+)?(\/\w+)?(\/\w+)?.*$';
	protected $reverse = '/%s';
	protected $separator = '&=';
	protected $params = array('a' => array('map' => 3), 'c' => array('map' => 2), 'm' => array('map' => 1));

	/**
	 * 路由解析
	 *
	 * 匹配这个patten时，将试图去解析module、controller和action值，并解析二级域名。
	 * @see AbstractWindRoute::match()
	 */
	public function match($request) {
		$fullUrl = $request->getHostInfo() . $request->getRequestUri();
		$_pathInfo = trim(str_replace($request->getBaseUrl(), '', $fullUrl), '/');
		if (!$_pathInfo || !preg_match_all('/' . $this->pattern . '/i', trim($_pathInfo, '/'), $matches) || strpos(
			$_pathInfo, '.php') !== false) return null;
		
		list(, $_args) = explode('?', $_pathInfo . '?', 2);
		$_args = trim($_args, '?');
		$_args = WindUrlHelper::urlToArgs($_args, true, $this->separator);
		
		foreach ($this->params as $_n => $_p) {
			if (isset($_p['map']) && isset($matches[$_p['map']][0]))
				$_value = $matches[$_p['map']][0];
			else
				$_value = isset($_p['default']) ? $_p['default'] : '';
			$this->params[$_n]['value'] = $params[$_n] = trim($_value, '-/');
			unset($_args[$_n]); //去掉参数中的m,c,a
		}
		$host = $request->getHostInfo();
		if ($host != '' && ($pos1 = strpos($host, '://')) !== false && ($pos2 = strpos($host, '.')) !== false)
			$host = substr($host, $pos1 + 3, $pos2 - $pos1 - 3);
		else
			return null;
		$params['p'] = $host == 'www' ? '' : $host;
		unset($_args['p']); //去掉参数中的p
		$_args && $params = array_merge($params, $_args);
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
		$flag = 0;
		foreach ($this->params as $key => $val) {
			if (!isset($val['map'])) continue;
			if ($key === $router->getModuleKey()) {
				$m = $_m ? $_m : $router->getModule();
				if ($m === $router->getDefaultModule() && $flag & 2)
					$flag = 7;
				else
					$_args[$val['map']] = $m;
			} elseif ($key === $router->getControllerKey()) {
				$c = $_c ? $_c : $router->getController();
				if ($c === $router->getDefaultController() && $flag & 1)
					$flag = 3;
				else
					$_args[$val['map']] = $c;
			} elseif ($key === $router->getActionKey()) {
				$a = $_a ? $_a : $router->getAction();
				if ($a === $router->getDefaultAction())
					$flag = 1;
				else
					$_args[$val['map']] = $a;
			} else {
				if (isset($args[$key]))
					$_args[$val['map']] = $args[$key];
				elseif (isset($val['value']))
					$_args[$val['map']] = $val['value'];
				else
					$_args[$val['map']] = '';
			}
			unset($args[$key]);
		}
		$mulitipyTime = count($_args);
		$_args[0] = str_repeat($this->reverse, $mulitipyTime);
		ksort($_args);
		$url = call_user_func_array("sprintf", $_args);
		$args && $url .= '?' . WindUrlHelper::argsToUrl($args, true, $this->separator);
		
		$baseUrl = Wind::getApp()->getRequest()->getBaseUrl(true);
		$_baseUrl = $_p ? $this->replaceStr($baseUrl, $_p) : $baseUrl;
		
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