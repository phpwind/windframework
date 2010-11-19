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
class WindSystemConfig extends WConfig { 
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
		if ($configName && isset($this->config[$configName]))
			return $this->config[$configName];
	}
	
	/**
	 * 返回过滤器
	 * @param string $name
	 */
	public function getFiltersConfig($name = '') {
		if (isset($this->config['filters']))
			return !$name ? $this->config['filters'] : ($this->config['filters'][$name] ? $this->config['filters'][$name] : '');
	}
	
	/**
	 * 返回应用配置信息，没有任何应用配置信息则返回''
	 * @param string $name
	 * @return string
	 */
	public function getModulesConfig($name = '', $default = null) {
		if (!isset($this->config['modules']))
			return $default;
		if (!$name)
			return $this->config['modules'];
		
		return $this->config['modules'][$name] ? $this->config['modules'][$name] : $default;
	}
	
	/**
	 * 获得路由配置信息
	 * 
	 * @param string $name
	 * @return string|null|array
	 */
	public function getRouterConfig($name = '', $default = null) {
		if (!isset($this->config['router']))
			return $default;
		if (!$name)
			return $this->config['router'];
		
		return isset($this->config['router'][$name]) ? $this->config['router'][$name] : $default;
	}
	
	/**
	 * 获得路由解析规则配置
	 * 
	 * @param string $name
	 * @return array|null
	 */
	public function getRouterRule($name = '', $default = null) {
		if ($name) {
			$name = $name . 'Rule';
			return isset($this->config[$name]) ? $this->config[$name] : $default;
		} else
			throw new WException('');
	}
	
	/**
	 * 返回路由解析器配置
	 * 
	 * @return string
	 */
	public function getRouterParser($name = '', $default = null) {
		if (!isset($this->config['routerParser']))
			return $default;
		if (!$name)
			return $this->config['routerParser'];
		
		return $this->config['routerParser'][$name] ? $this->config['routerParser'][$name] : $default;
	}

}