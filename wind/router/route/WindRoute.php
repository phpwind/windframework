<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindRoute extends AbstractWindRoute {
	protected $pattern = '(\w+)(\/\w+)?(\/\w+)?\/*(&[\w+\/]*)*';
	protected $reverse = '%s/%s/%s/&';
	protected $params = array('a' => array('map' => 1), 'c' => array('map' => 2), 'm' => array('map' => 3));
	protected $separator = '/';

	/* (non-PHPdoc)
	 * @see IWindRoute::match()
	 */
	public function match() {
		$_pathInfo = $this->getRequest()->getPathInfo();
		if (!preg_match_all('/' . $this->pattern . '/i', $_pathInfo, $matches))
			return null;
		$params = array();
		foreach ($this->params as $_n => $_p) {
			if (isset($_p['map']) && isset($matches[$_p['map']][0]))
				$_value = $matches[$_p['map']][0];
			else
				$_value = isset($_p['default']) ? $_p['default'] : '';
			$params[$_n] = trim($_value, '-/');
		}
		list(, $_args) = explode('&', $_pathInfo . '&');
		$_args && $params += WindUrlHelper::urlToArgs($_args, true, $this->separator);
		return $params;
	}

	/* (non-PHPdoc)
	 * @see IWindRoute::build()
	 */
	public function build($router, $action, $args = array()) {
		list($_a, $_c, $_m, $args) = WindUrlHelper::resolveAction($action, $args);
		$_args[] = $this->reverse;
		foreach ($this->params as $key => $val) {
			if (!isset($val['map']))
				continue;
			if ($key === $router->getModuleKey())
				$_args[$val['map']] = $_m ? $_m : $router->getModule();
			elseif ($key === $router->getControllerKey())
				$_args[$val['map']] = $_c ? $_c : $router->getController();
			elseif ($key === $router->getActionKey())
				$_args[$val['map']] = $_a ? $_a : $router->getAction();
			else
				$_args[$val['map']] = $args[$key];
			unset($args[$key]);
		}
		ksort($_args);
		$url = call_user_func_array("sprintf", $_args);
		$url .= WindUrlHelper::argsToUrl($args, true, $this->separator);
		return $url;
	}
}
?>