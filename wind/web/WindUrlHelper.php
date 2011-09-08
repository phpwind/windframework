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
	 * @param string $url
	 * @param boolean $decode
	 * @param string $separator
	 * @return array
	 */
	public static function urlToArgs($url, $decode = true, $separator = '&=') {
		!$separator && $separator = '&=';
		false !== ($pos = strpos($url, '?')) && $url = substr($url, $pos + 1);
		$_sep1 = substr($separator, 0, 1);
		if ($_sep2 = substr($separator, 1, 1)) {
			$url = preg_replace('/' . preg_quote($_sep1) . '[\w+]' . preg_quote($_sep1) . '/i', $_sep1, $url);
			$url = str_replace($_sep2, $_sep1, $url);
		}
		$url = explode($_sep1, trim($url, $_sep1) . $_sep1);
		$args = array();
		for ($i = 0; $i < count($url); $i = $i + 2) {
			if (!isset($url[$i]) || !isset($url[$i + 1]))
				continue;
			$_v = $decode ? urldecode($url[$i + 1]) : $url[$i + 1];
			$_k = $url[$i];
			if (strpos($_k, self::$_sep) === 0) {
				$_k = substr($_k, strlen(self::$_sep));
				$_v = unserialize($_v);
			}
			$args[$_k] = $_v;
		}
		return $args;
	}

	/**
	 * 将数组格式的参数列表转换为Url格式，并将url进行编码处理
	 * @param array $args
	 * @return string
	 */
	public static function argsToUrl($args, $encode = true, $separator = '&=') {
		!$separator && $separator = '&=';
		$_sep1 = substr($separator, 0, 1);
		$_sep2 = substr($separator, 1, 1);
		!$_sep2 && $_sep2 = $_sep1;
		$_tmp = '';
		foreach ((array) $args as $key => $value) {
			if (is_array($value))
				$_tmp .= self::$_sep . "$key" . $_sep2 . urlencode(serialize($value));
			else
				$_tmp .= "$key" . $_sep2 . urlencode($value);
			$_tmp .= $_sep1;
		}
		return $_tmp;
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
	public static function createUrl($action, $args = array(), $anchor = '', $route = null) {
		/* @var $router AbstractWindRouter */
		$router = Wind::getApp()->getComponent('router');
		return $router->assemble($action, $args, $route) . ($anchor ? '#' . $anchor : '');
	}
}
?>