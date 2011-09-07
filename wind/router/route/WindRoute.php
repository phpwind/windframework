<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindRoute extends AbstractWindRoute {
	private $separator = '&';
	private $keyValue = '=';
	private $arrayKey = '_array_';
	
	/* (non-PHPdoc)
	 * @see IWindRoute::match()
	 */
	public function match() {
		$requireUri = $this->getRequest()->getBaseUrl(true) . '/' . trim($this->getRequest()->getPathInfo(), '?/');
		if (!preg_match_all('/' . $this->regex . '/', $requireUri, $matches)) return null;
		$this->interceptorChain->setCurrentRoute($this);
		$args = array();
		foreach ($this->params as $key => $value) {
		    if (!isset($matches[$value])) continue;
			$temp = $matches[$value][0];
			$args[$key] = $temp;
		}
		$args = array_merge($args, $this->urlToArgs($args['*']));
		unset($args['*']);
		$_GET = array_merge($_GET, $args);
		return $args;
	}

	/* (non-PHPdoc)
	 * @see IWindRoute::build()
	 */
	public function build() {
		list($action, $args)   = func_get_args();
		list($params, $anchor) = $this->resolveParaments($action, $args);
		
		$temp = array();
		foreach ($this->params as $key => $val) {
			if ($key != '*') {
				$temp[$key] = $params[$key];
				unset($params[$key]);
			}
		}
		
		$temp['*'] = str_replace(array('&', '='), array($this->separator, $this->keyValue), WindUrlHelper::argsToUrl($params));
		$url = strtr($this->reverse, $temp);
		
		return $this->getRequest()->getBaseUrl(true) . '/' . $url . $anchor;
	}
	
	/**
	 * 解析
	 * 解析传入的action和参数，及锚点
	 * 
	 * @param string $action
	 * @param array $args
	 * @return array
	 */
	private function resolveParaments($action ,$args) {
		$temp = explode('#', $action, 2);
		$anchor = isset($temp[1]) ? $temp[1] : '';
		$action = $temp[0];
		
		list($_a, $_c, $_m, $params) = WindUrlHelper::resolveAction($action, $args);
		if (isset($params['#'])) {
			$anchor = $params['#'];
			unset($params['#']);
		}
		
		$params[$this->interceptorChain->getControllerKey()] = $_c;
		$params[$this->interceptorChain->getModuleKey()] 	 = $_m;
		$params[$this->interceptorChain->getActionKey()] 	 = $_a;
		return array($params, $anchor ? '#' . $anchor : '');
	}

	/**
	 * 从url转化为数组
	 * @param string $pathinfo
	 * @return boolean
	 */
	private function urlToArgs($pathinfo) {
		if (!$pathinfo) return array();
		$params = explode($this->separator, $pathinfo);
		$num = count($params);
		$args = array();
		for($i = 0; $i < $num; $i++) {
			if ($this->separator == $this->keyValue) {
				$key = $params[$i];
				$value = isset($params[$i+1]) ? urldecode($params[$i+1]) : null;
				$i ++;
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
		if (!$config) return null;
 		parent::setConfig($config);
		$this->separator = $this->getConfig('var-separator', '', '&');
		$this->keyValue  = $this->getConfig('key-separator', '', '=');
	}
}
?>