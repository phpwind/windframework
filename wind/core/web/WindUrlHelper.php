<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindUrlHelper extends WindModule {
	private $urlPatttern = '';
	private $keyValueSep = '';
	private $separator = '';
	private $suffix = '';
	private $isRewrite = 0;
	private $keyPrefix = '';
	private $baseUrl = '';
	private $patterns = array();

	/**
	 * 解析Url
	 * 
	 * 没有配置解析规则，直接返回
	 * 获得则匹配RequestUri，根据用户的配置分隔符分割信息
	 * 同时设置到超全局变量$_GET中
	 */
	public function parseUrl() {
		if (!$this->isRewrite()) return;
		$url = array();
		if ($this->getRequest()->getServer('SERVER_PROTOCOL')) {//http协议
			$pathInfo = $this->getRequest()->getServer('PATH_INFO');
			if ($pathInfo && !empty($pathInfo)) {
				$url = rtrim($pathInfo, $this->suffix);
			} elseif ('' != ($url = $this->getRequest()->getRequestUri())) {
				$scriptName = $this->getRequest()->getScriptUrl();
				if (0 === strpos($url, $scriptName)) {
					$url = substr($url, strlen($scriptName));
				}
				$url = rtrim($url, $this->suffix);
			}
			$url && $params = $this->doParserUrl(trim($url, '?/'));
		} else {// 命令行下
			$i = 0;
			$args = $this->getRequest()->getServer('argv', array());
			while (isset($args[$i]) && isset($args[$i + 1])) {
				$params[$args[$i]] = $args[$i + 1];
				$i += 2;
			}
		}
		foreach ($params as $k => $v) {
			!isset($_GET[$k]) && $_GET[$k] = $v;
		}
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
		list($controller, $module) = WindHelper::resolveController($controller);
		$urlRouter = $this->getSystemFactory()->getInstance(COMPONENT_ROUTER);
		if (!$this->isRewrite()) {
			$urlRouter->setAction($action);
			$urlRouter->setController($controller);
			$urlRouter->setModule($module);
			return $this->baseUrl . '/' . $urlRouter->buildUrl() . '&' . http_build_query($params, '', '&');
		}
		$m = $urlRouter->getConfig('module', WindUrlBasedRouter::URL_PARAM);
		$c = $urlRouter->getConfig('controller', WindUrlBasedRouter::URL_PARAM);
		$a = $urlRouter->getConfig('action', WindUrlBasedRouter::URL_PARAM);
		$params = array_merge(array($m => $module, $c => $controller, $a => $action), $params);
		return $this->buildRewriteUrl($params);
	}
	
	/**
	 * 构造重写的url
	 * @param string $m
	 * @param string $c
	 * @param string $action
	 * @param string $params
	 * @return string
	 */
	private function buildRewriteUrl($params) {
		$url = $this->urlPatttern;
		foreach ($this->patterns as $key => $value) {
			if ('*' == $value[0]) {
				$url = str_replace($value, $this->buildCommonKeys($params), $url);
			} else {
				$url = $this->buildVars($value, $params, $url);
			}
		}
		return $this->baseUrl  . '/' . $url . $this->suffix;
	}
	
	/**
	 * 构建变量 不是*
	 * 
	 * @param string $value
	 * @param array $params
	 * @param string $url
	 * @return string
	 */
	private function buildVars($value, &$params, $url) {
		$keys = explode($this->keyValueSep, $value);
		$values = array();
		foreach ($keys as $v) {
			if (!isset($params[$v])) continue;
			$values[] = $params[$v];
			unset($params[$v]);
		}
		return str_replace($keys, $values, $url);
	}
	
	/**
	 * 构建rewriteurl的参数部分
	 * @param array $params
	 * @param string $parentKey
	 * @param boolean $first 只有第一级的变量才加前缀
	 * @return string
	 */
	private function buildCommonKeys($params, $parentKey = '', $first = true) {
		$tmp = array();
        foreach ($params as $k => $v) { 
            if (is_int($k) && $this->keyPrefix != null && $first) {
                $k = urlencode($this->keyPrefix . $k);
            }
            if (!empty($parentKey))  $k = $parentKey . '[' . $k . ']';
            if (is_array($v)) {
                array_push($tmp, $this->buildCommonKeys($v, $k, false)); 
            } else {
                array_push($tmp, $k . $this->keyValueSep . urlencode($v)); 
            }
        }
		return implode($this->separator, $tmp);
	}


	/**
	 * 执行匹配
	 * patterns中的匹配模式去匹配url中的信息
	 * urlPatterns中可以根据需求将mca进行组合配置。
	 * <config>
			<url-pattern>m-c-a/*</url-pattern>  <!-- 这里的mca可以根据自己的喜欢的格式用separator和key-value-step的形式组合配置例如：m/c/a 或者m/c-a -->
			<suffix>htm</suffix>
			<separator>/</separator>
			<key-value-sep>-</key-value-sep>
			<key-prefix>myvar_</key-prefix>
			<is-rewrite>1</is-rewrite>
		</config>
	 * url-pattern中：*匹配表示其他的变量信息
	 * 用户也可以将自己的其他信息添加到格式中比如m/c-a/tid/*
	 * suffix ： url的后缀
	 * separator: 变量分隔符
	 * key-value-sep: key和value的分隔符，默认和separator一致
	 * key-prefix: 数字索引的前缀
	 * is-rewrite: 是否采用rewrite机制
	 * 
	 * 遍历url模式：采用separator配置的分隔符获得该模式数组
	 * 如果模式是*,则代表该位置开始往后为为传递的变量信息
	 * 如果模式不是*，则代表该位置为特殊模式。
	 *    如果含有key-value-sep配置的分隔符： 则分别对url中相同key下的值和模式中的值分别做分割，对应的模式下获得的数组作为key,url中获得数组作为value
	 *    如果不含：则该值就作为Key,url对应位置上的值作为value
	 * 
	 * @param string 待分析匹配的路径信息
	 * @return array
	 */
	private function doParserUrl($url) {
		if (is_string($url)) {
			if (!$url || false === strrpos($url, $this->separator)) return array();
			$url = explode($this->separator, trim($url, $this->separator));
		}
		
		$vars = array();
		foreach ($this->patterns as $key => $value) {
			if ('*' == $value[0]) $this->parseCommonKey($key, $url, $vars);
			else {
				if (!isset($url[$key])) continue;
				if (false === strrpos($value, $this->keyValueSep)) {
					$vars[$value] = $url[$key];
					continue;
				}
				$keys = explode($this->keyValueSep, $value);
				$values = explode($this->keyValueSep, $url[$key]);
				$vars = array_merge($vars, array_combine($keys, $values));
			}
		}
		return $vars;
	}
	
	/**
	 * 解析url普通参数
	 * 如果separator和key-value-sep配置相同：则采用每两个元素为一对key-value的规则获得变量
	 * 如果不相同：则将会以每个元素为key-value的组合，之间的分隔符以key-value-sep划分，如果没有该分隔符，则默认该值的索引为数字索引。
	 *    如果为没有分隔符：则该值索引以数字索引给出，同时该数字索引将会根据用户是否配置key前缀key-prefix来给出key。
	 *    如果有分隔符：则该值将会用分隔符分割获得的两个值来分别作为key和value.
	 * @param int $key
	 * @param array $urlParams
	 * @param array $params
	 */
	private function parseCommonKey($key, $urlParams, &$params) {
		$pos = 0;
		while (isset($urlParams[$key])) {
			if ($this->separator == $this->keyValueSep) {
				if (isset($urlParams[$key+1])) {
					$this->parseKey($params, $urlParams[$key], urldecode($urlParams[$key + 1]));
					$key += 2;
				}
				continue;
			}
			if (false === strrpos($urlParams[$key], $this->keyValueSep)) {
				$params[$this->keyPrefix . $pos] = urldecode($urlParams[$key]);
				$pos ++;
			} else {
				list($k, $v) = explode($this->keyValueSep, $urlParams[$key], 2);
				$this->parseKey($params, $k, urldecode($v));
			}
			$key += 1;
		}
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
	
	/*
	 * (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		$usrConfig = $this->getSystemConfig()->getConfig('router', 'rewrite');
		$usrConfig && $config = array_merge($config, $usrConfig);
		parent::setConfig($config);
		$this->urlPatttern = $this->getConfig('url-pattern');
		$this->separator = $this->getConfig('separator');
		$this->keyValueSep = $this->getConfig('key-value-sep');
		$this->keyValueSep == "" && $this->keyValueSep = $this->separator;
		$this->suffix = '.' . trim($this->getConfig('suffix'), '.');
		$this->isRewrite = $this->getConfig('is-rewrite');
		$this->keyPrefix = $this->getConfig('key-prefix');
		$this->patterns = explode($this->separator, trim($this->urlPatttern, $this->separator));
		$this->getBaseUrl();
	}
	
	/**
	 * 返回域名及请求路径
	 * 
	 * @return string 
	 */
	private function getBaseUrl() {
		$this->baseUrl = rtrim($this->getRequest()->getBaseUrl(true), '/');
		if (!$this->isRewrite()) 
			$this->baseUrl = $this->baseUrl . $this->getRequest()->getScriptUrl();
	}
	
	/**
	 * 是否开启重写
	 * 
	 * @return boolean
	 */
	public function isRewrite() {
		return $this->isRewrite == '1' || $this->isRewrite == 'true';
	}
}
?>