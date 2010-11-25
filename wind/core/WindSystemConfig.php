<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
//L::import('WIND:');

/**
 * 配置信息解析
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WindSystemConfig extends WindConfig {
	private $globalConfig = array();
	private $config = array();
	private static $instance = null;
	
	/**
	 * 争对数组格式的解析
	 * @param array $configSystem
	 * @param array $configCustom
	 */
	public function _parse($configSystem, $configCustom = array()) {
		if (!is_array($configSystem) || !is_array($configCustom)) throw new Exception('the format of config file is error!!!');
		
		if (empty($configSystem)) throw new Exception('system config file is not exists!!!');
		
		$this->config = array_merge($configSystem, $configCustom);
		$this->system = $configSystem;
		$this->custom = $configCustom;
	}
	
	/**
	 * 针对数组格式的解析
	 * @param array $configSystem  全局缓存配置
	 * @param string $current  当前应用的名字
	 */
	public function parse($config) {
		if (!is_array($config)) 
			throw new Exception('the format of config file is error!!!');
		$this->config = $config;
	}
	
	/**
	 * 根据配置名取得相应的配置
	 * @param string $configName
	 * @return string
	 */
	public function getConfig($configName) {
		if ($configName && isset($this->config[$configName])) return $this->config[$configName];
	}
	
	/**
	 * 根据配置名的路径取得相应的配置信息
	 * 
	 * $var = array(
	 *     'templates' => array(
	 *         'template' => array(
	 *            'templateDir' => '/date';
	 *            'templateCache'  => '/cache';
	 *            )
	 *     ))
	 * 如果想获得templateDir下的值，
	 * 则如下调用WindSystemConfig::getConfigPath('templates', 'template', 'templateDir')
	 * 如果该路径中某一个节点不存在，则返回''
	 * @param mixed
	 * @return mixed
	 */
	public function getConfigPath() {
		$vars = func_get_args();
		$current = $this->config;
		foreach ($vars as $name) {
			if (isset($current[$name])) $current = $current[$name];
			return '';
		}
		return $current;
	}
	
	/**
	 * 返回过滤器
	 * @param string $name
	 */
	public function getFiltersConfig($name = '') {
		if (!$this->config[IWindConfig::FILTERS]) return array();
		if ($name == '' ) return $this->config[IWindConfig::FILTERS];
		$filters = $this->config[IWindConfig::FILTERS];
		foreach ($filters as $one) {
			if ($one[IWindConfig::FILTERNAME] == $name) return $one;
		}
	}
	
	/**
	 * 返回应用配置信息，没有任何应用配置信息则返回''
	 * @param string $name
	 * @return string
	 */
	public function getModulesConfig($name = '', $default = null) {
		if (!isset($this->config['app'])) return $default;
		if (!$name) return $this->config['app'];
		
		return $this->config['app'][$name] ? $this->config['app'][$name] : $default;
	}
	
	/**
	 * 获得路由配置信息
	 * 
	 * @param string $name
	 * @return string|null|array
	 */
	public function getRouterConfig($name = '', $default = null) {
		if (!isset($this->config['router'])) return $default;
		if (!$name) return $this->config['router'];
		
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
			throw new WindException('');
	}
	
	/**
	 * 返回路由解析器配置
	 * 
	 * @return string
	 */
	public function getRouterParser($name = '', $default = null) {
		if (!isset($this->config['routerParser'])) return $default;
		if (!$name) return $this->config['routerParser'];
		
		return $this->config['routerParser'][$name] ? $this->config['routerParser'][$name] : $default;
	}
	
	/**
	 * @return WindSystemConfig
	 */
	static public function getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}

}