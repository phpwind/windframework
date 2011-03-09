<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.WindModule');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindConfig extends WindModule {

	/* 配置解析信息 */
	protected $configParser = null;

	protected $cacheName;

	protected $append;

	protected $config = array();

	/**
	 * @param string $config | 配置文件路径信息
	 * @param string $configParser	| 配置解析器
	 * @param string $cacheName | 配置文件缓存文件名称
	 * @param WindConfigParser $configParser | 配置解析器
	 */
	public function __construct($config, $configParser = null, $cacheName = '', $append = false) {
		$this->setConfigParser($configParser);
		$this->setCacheName($cacheName);
		$this->setAppend($append);
		$this->initConfig($config);
	}

	/**
	 * 根据配置名取得相应的配置
	 * 
	 * @param string $configName
	 * @param string $subConfigName
	 * @return string
	 */
	public function getConfig($configName = '', $subConfigName = '', $config = array()) {
		if (!$config) $config = $this->config;
		if ($configName === '') return $config;
		
		$_config = array();
		if (isset($config[$configName])) {
			$_config = $config[$configName];
		}
		if ($subConfigName === '') return $_config;
		
		$_subConfig = array();
		if (is_array($_config) && isset($_config[$subConfigName])) {
			$_subConfig = $_config[$subConfigName];
		}
		return $_subConfig;
	}

	/**
	 * 初始化配置文件对象,如果参数是非数据格式，
	 * 则该方法会尝试调用注册进来的配置解析器进行配置解析
	 * 如果没有注册过任何配置解析器，则抛出异常
	 * 
	 * @param array $config
	 */
	protected function initConfig($config) {
		if (!$config) return;
		if (!is_array($config)) {
			$config = $this->parseConfig($config, $this->getCacheName(), $this->getAppend());
		}
		$this->setConfig($config);
	}

	/**
	 * 解析配置信息,返回解析后的配置结果
	 * @param string $config | 配置文件源路径
	 * @param string $cacheName | 缓存文件名称 | key值
	 * @param string $append | 配置缓存是否追加到该缓存文件下面
	 * @return array
	 */
	protected function parseConfig($config, $cacheName, $append) {
		if ($this->getConfigParser() === null) {
			throw new WindException('configParser is null.');
		}
		return $this->getConfigParser()->parse($config, $cacheName, $append);
	}

	/**
	 * @param $config the $config to set
	 * @author Qiong Wu
	 */
	public function setConfig($config, $merage = false) {
		if (!is_array($config)) throw new WindException('config error.');
		if ($merage)
			$this->config = array_merge($this->config, $config);
		else
			$this->config = $config;
	}

	/**
	 * @return WindConfigParser $configParser
	 */
	public function getConfigParser() {
		return $this->configParser;
	}

	/**
	 * @param WindConfigParser $configParser
	 * @author Qiong Wu
	 */
	public function setConfigParser($configParser) {
		if ($this->configParser || $configParser == null) return;
		$this->configParser = $configParser;
	}

	/**
	 * @return the $cacheName
	 */
	public function getCacheName() {
		return $this->cacheName;
	}

	/**
	 * @return the $append
	 */
	public function getAppend() {
		return $this->append;
	}

	/**
	 * @param $cacheName the $cacheName to set
	 * @author Qiong Wu
	 */
	public function setCacheName($cacheName) {
		$this->cacheName = $cacheName;
	}

	/**
	 * @param $append the $append to set
	 * @author Qiong Wu
	 */
	public function setAppend($append) {
		$this->append = $append;
	}

}