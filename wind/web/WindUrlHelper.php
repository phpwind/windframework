<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindUrlHelper {
	private static $_sep = '_array_';

	/**
	 * @param string $url
	 * @return string
	 */
	public static function checkUrl($url) {
		return $url;
	}

	/**
	 * @param unknown_type $args
	 */
	public static function urlToArgs($url, $decode = true) {
		if (false !== ($pos = strpos($url, '?')))
			$url = substr($url, $pos + 1);
		$url = explode('&', $url . '&');
		$args = array();
		foreach ($url as $value) {
			list($_k, $_v) = explode('=', $value . '=');
			if ($_k) {
				$decode && $_v = urldecode($_v);
				if (strpos($_k, self::$_sep) === 0) {
					$_k = substr($_k, strlen(self::$_sep));
					$_v = unserialize($_v);
				}
				$args[$_k] = $_v;
			}
		}
		return $args;
	}

	/**
	 * 将数组格式的参数列表转换为Url格式，并将url进行编码处理
	 * @param array $args
	 * @return string
	 */
	public static function argsToUrl($args) {
		$_tmp = array();
		foreach ((array) $args as $key => $value) {
			if (is_array($value)) {
				$_tmp[] = self::$_sep . "$key=" . urlencode(serialize($value));
				continue;
			}
			$_tmp[] = "$key=" . urlencode($value);
		}
		return implode('&', $_tmp);
	}

	/**
	 * 解析ControllerPath
	 * /module/controller/action/?a=a&b=b&c=c&
	 * 返回解析后的controller信息，controller，module，app
	 * 
	 * @param string $controllerPath
	 * @return array
	 */
	public static function resolveAction($action, $args = array()) {
		list($action, $_args) = explode('?', $action . '?');
		$args = array_merge($args, ($_args ? self::urlToArgs($_args, false) : array()));
		$action = explode('/', trim($action, '/') . '/');
		end($action);
		return array(prev($action), prev($action), prev($action), $args);
	}

	/**
	 * 构造返回Url地址
	 * 将根据是否开启url重写来分别构造相对应的url
	 * @param string $action 执行的操作
	 * @param array $args 附带的参数
	 * @param AbstractWindRoute $route
	 * @return string
	 */
	public static function createUrl($action, $args = array(), $route = null) { 
		/* @var $router AbstractWindRouter */
		$router = Wind::getApp()->getComponent('router');
		return $router->assemble($action, $args, $route);
	}
}
?>