<?php
Wind::import("WIND:router.route.AbstractWindRoute");
/**
 * 
 * <code>
 * http://blog.p9.com/module/controller/action?id=1&name=2
 * </code>
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class MyRoute extends AbstractWindRoute {
	
	protected $pattern = '^(\w+)?(\/\w+)?(\/\w+)?(\/|\/?\?.*)*$';
	protected $reverse = '/%s/%s/%s/';
	protected $separator = '&=';
	protected $params = array(
		'm' => array('map' => 1), 
		'c' => array('map' => 2), 
		'a' => array('map' => 3));
	
	public function match($request, $response) {
		$_pathInfo = trim(str_replace($request->getBaseUrl(), '', $request->getRequestUri()), '/');
		if (!$_pathInfo || !preg_match_all('/' . $this->pattern . '/i', trim($_pathInfo, '/'), $matches)) return null;
		foreach ($this->params as $_n => $_p) {
			if (isset($_p['map']) && isset($matches[$_p['map']][0])) 
				$_value = $matches[$_p['map']][0];
			else
				$_value = isset($_p['default']) ? $_p['default'] : '';
			$this->params[$_n]['value'] = $params[$_n] = trim($_value, '-/');
		}
		$host = $request->getHostInfo();
		if ($host != ''  && ($pos1 = strpos($host, '://')) !== false && ($pos2 = strpos($host, '.')) !== false){
			$host = substr($host, $pos1+3, $pos2-$pos1-3);
		}
		$params['p'] = $host == 'www' ? '' : $host;
		list(, $_args) = explode('?', $_pathInfo, 2);
		$_args && $params = array_merge($params, WindUrlHelper::urlToArgs($_args, true, $this->separator));
		return $params;
	}
	
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
		$url .= '?' . WindUrlHelper::argsToUrl($args, true, $this->separator);
		
		$p = $_p ? $_p : $router->getApp();
		$baseUrl = Wind::getApp()->getRequest()->getBaseUrl(true);
		$_baseUrl = $this->replaceStr($baseUrl, $p);
		
		return trim($_baseUrl, '/') . '/' . trim($url, '/');
	}
	
	private function replaceStr($url, $str){
		$arr = explode('.', $url, 2);
		$arr1 = explode('://', $arr[0]);
		return $arr1[0] . '://' . $str . '.' . $arr[1];
	}

	
}

?>