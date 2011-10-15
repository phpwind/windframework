<?php
/**
 * 路由协议
 * 
 * 该类继承了抽象类{@see AbstractWindRoute},实现了{@see AbstractWindRoute::match()},
 * {@see AbstractWindRoute::build()}.该类,可以通过配置更改Url匹配规则,实现多种url rewrite形式的支持.
 * 默认路由规则：<code>^(\w+\.\w+\?|\?|\w+\.\w+\?\/)*(\w+\/)(\w+\/)?(\w+\/)?(&[\w+\/]*)*$
 * 匹配格式：index.php?a/c/m/&id=aaaaa 或者 ?a/c/m/&id=aaaaa</code>
 * @author Qiong Wu <papa0924@gmail.com> 2011-9-23
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package router
 * @subpackage route
 */
class WindRoute extends AbstractWindRoute {
	protected $pattern = '^([\w-_\.]+\.\w+[\?\/]{1,2}|\?)*(\w+)(\/\w+)?(\/\w+)?(\/|\/?&.*)*$';
	protected $reverse = '%s/%s/%s/%s/';
	protected $separator = '&=';
	protected $params = array(
		'script' => array('map' => 1), 
		'a' => array('map' => 2), 
		'c' => array('map' => 3), 
		'm' => array('map' => 4));

	/* (non-PHPdoc)
	 * @see AbstractWindRoute::match()
	 */
	public function match() {
		$_pathInfo = trim(
			str_replace($this->getRequest()->getBaseUrl(), '', $this->getRequest()->getRequestUri()), 
			'/');
		if (!$_pathInfo || !preg_match_all('/' . $this->pattern . '/i', trim($_pathInfo, '/'), 
			$matches)) return null;
		foreach ($this->params as $_n => $_p) {
			if (isset($_p['map']) && isset($matches[$_p['map']][0]))
				$_value = $matches[$_p['map']][0];
			else
				$_value = isset($_p['default']) ? $_p['default'] : '';
			$this->params[$_n]['value'] = $params[$_n] = trim($_value, '-/');
		}
		list(, $_args) = explode('&', $_pathInfo . '&', 2);
		$_args && $params = array_merge($params, 
			WindUrlHelper::urlToArgs($_args, true, $this->separator));
		return $params;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindRoute::build()
	 */
	public function build($router, $action, $args = array()) {
		list($_a, $_c, $_m, $args) = WindUrlHelper::resolveAction($action, $args);
		$_args[] = $this->reverse;
		foreach ($this->params as $key => $val) {
			if (!isset($val['map'])) continue;
			if ($key === $router->getModuleKey())
				$_args[$val['map']] = $_m ? $_m : $router->getModule();
			elseif ($key === $router->getControllerKey())
				$_args[$val['map']] = $_c ? $_c : $router->getController();
			elseif ($key === $router->getActionKey())
				$_args[$val['map']] = $_a ? $_a : $router->getAction();
			else
				$_args[$val['map']] = isset($args[$key]) ? $args[$key] : $val['value'];
			unset($args[$key]);
		}
		ksort($_args);
		$url = call_user_func_array("sprintf", $_args);
		$url .= '&' . WindUrlHelper::argsToUrl($args, true, $this->separator);
		return $url;
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