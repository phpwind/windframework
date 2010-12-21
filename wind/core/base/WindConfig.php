<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 配置对象
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindConfig {
	protected $config = array();
	
	public function __construct($config) {
		$this->init($config);
	}
	
	/**
	 * 初始化配置文件对象
	 * @param array $configSystem
	 */
	private function init($config) {
		if (empty($config)) {
			throw new Exception('config object is not exists.');
		}
		$this->config = $config;
	}
	
	/**
	 * 根据配置名取得相应的配置
	 * @param string $configName
	 * @param string $subConfigName
	 * @return string
	 */
	public function getConfig($configName = '', $subConfigName = '') {
		if (!$configName) return $this->config;
		$_config = array();
		if (isset($this->config[$configName])) {
			$_config = $this->config[$configName];
		}
		if (!$subConfigName) return $_config;
		$_subConfig = array();
		if (is_array($_config) && isset($_config[$subConfigName])) {
			$_subConfig = $_config[$subConfigName];
		}
		return $_subConfig;
	}
}