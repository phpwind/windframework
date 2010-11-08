<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 配置信息解析
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WSystemConfig extends WConfig {
	private $system = array();
	private $custom = array();
	private $config = array();
	
	/**
	 * 争对数组格式的解析
	 * @param array $configSystem
	 * @param array $configCustom
	 */
	public function parse($configSystem, $configCustom = array()) {
		if (!is_array($configSystem) || !is_array($configCustom))
			throw new Exception('the format of config file is error!!!');
		if (empty($configSystem))
			throw new Exception('system config file is not exists!!!');
		
		$this->config = array_merge($configSystem, $configCustom);
		$this->system = $configSystem;
		$this->custom = $configCustom;
	}
	
	/**
	 * 争对xml格式配置的解析
	 * @param xml $configSystem
	 * @param xml $configCustom
	 */
	public function parseXML($configSystem, $configCustom = null) {

	}
	
	/**
	 * 根据配置名取得相应的配置
	 * @param string $configName
	 * @return string
	 */
	public function getConfig($configName) {
		if ($configName && isset($this->config[$configName])) {
			return $this->config[$configName];
		} else {
			throw new Exception("{$configName}配置信息不存在", 5);
		}
	}
	
	/**
	 * 返回过滤器
	 * @param string $name
	 */
	public function getFiltersConfig($name = '') {
		if (isset($this->config['filters']))
			return !$name ? $this->config['filters'] : $this->config['filters'][$name] ? $this->config['filters'][$name] : '';
		else
			throw new Exception("the filter config is not exists!!!");
	}
	
	/**
	 * 返回过滤器链
	 * @param string $name
	 * @return Ambigous <string, multitype:>
	 */
	public function getFilterChainConfig($name = '') {
		if (isset($this->config['filterChain']))
			return !$name ? $this->config['filterChain'] : $this->config['filterChain'][$name] ? $this->config['filterChain'][$name] : '';
		else
			throw new Exception("the filter config is not exists!!!");
	}
	
	/**
	 * 获得路由配置信息
	 * @return string
	 */
	public function getRouterConfig($name = '') {
		if (isset($this->config['router']))
			return !$name ? $this->config['router'] : $this->config['router'][$name] ? $this->config['router'][$name] : '';
		else
			throw new Exception("the router config is not exists!!!");
	}
	
	/**
	 * 获得路由规则配置
	 * @param unknown_type $name
	 * @return Ambigous <string, multitype:>
	 */
	public function getRouterRule($name = '') {
		if (empty($name))
			$name = $this->getRouterConfig('parser');
		
		$name = $name . 'Rule';
		if (isset($this->config[$name]))
			return $this->config[$name];
		else
			throw new Exception("the routerParser config is not exists!!!");
	}
	
	/**
	 * 返回路由解析器配置
	 * @return string
	 */
	public function getRouterParser($name = '') {
		if (isset($this->config['routerParser']))
			return !$name ? $this->config['routerParser'] : $this->config['routerParser'][$name] ? $this->config['routerParser'][$name] : '';
		else
			throw new Exception("the routerParser config is not exists!!!");
	}

}