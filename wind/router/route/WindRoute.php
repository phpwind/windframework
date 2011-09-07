<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindRoute extends AbstractWindRoute {
	protected $pattern = '\?(\w+)(\/\w+)?(\/\w+)?';
	protected $reverse = '%s/%s/%s/&';
	protected $params = array('a' => array('map' => 1), 'c' => array('map' => 2), 'm' => array('map' => 3));
	private $separator = '&';
	private $keyValue = '=';

	/* (non-PHPdoc)
	 * @see IWindRoute::match()
	 */
	public function match() {
		echo $this->getRequest()->getPathInfo();exit;
		if (!preg_match_all('/' . $this->pattern . '/i', $this->getRequest()->getRequestUri(), $matches))
			return null;
		$params = array();
		foreach ($this->params as $_n => $_p) {
			if (isset($_p['map']) && isset($matches[$_p['map']][0]))
				$_value = $matches[$_p['map']][0];
			else
				$_value = isset($_p['default']) ? $_p['default'] : '';
			$params[$_n] = trim($_value, '-/');
		}
		$_pathInfo = $this->getRequest()->getPathInfo();
		$_pathInfo && $params += WindUrlHelper::urlToArgs($_pathInfo);
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
		$url .= WindUrlHelper::argsToUrl($args);
		return $url;
	}

	/**
	 * 从url转化为数组
	 * @param string $pathinfo
	 * @return boolean
	 */
	private function urlToArgs($pathinfo) {
		if (!$pathinfo)
			return array();
		$params = explode($this->separator, $pathinfo);
		$num = count($params);
		$args = array();
		for ($i = 0; $i < $num; $i++) {
			if ($this->separator == $this->keyValue) {
				$key = $params[$i];
				$value = isset($params[$i + 1]) ? urldecode($params[$i + 1]) : null;
				$i++;
			} else {
				list($key, $value) = explode($this->keyValue, $params[$i], 2);
				$value = urldecode($value);
			}
			
			if (strpos($key, $this->arrayKey) === 0) {
				$key = substr($key, strlen($this->arrayKey));
				$value = unserialize($value);
			}
			$args[$key] = $value;
		}
		return $args;
	}

	/**
	 * (non-PHPdoc)
	 * @see AbstractWindRoute::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->separator = $this->getConfig('var-separator', '', '&');
		$this->keyValue = $this->getConfig('key-separator', '', '=');
	}
}
?>