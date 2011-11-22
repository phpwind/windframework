<?php
/**
 * 应用的配置对象
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package config
 */
class WindAppConfig {
	
	const ROOT_PATH = 'root-path';
	
	const COMPONENTS = 'components';
	
	const FILTERS = 'filters';
	
	const MODULES = 'modules';
	
	protected $appName = '';
	
	/**
	 * @var WindConfigParser
	 */
	protected $parser = null;
	
	protected $config = array();
	
	/**
	 * 构造方法
	 *
	 * @param string $appName
	 * @param array|string $config
	 * @param WindConfigParser $parser
	 */
	public function __construct($appName, $config, $parser){
		$this->appName = $appName;
		$this->parser = $parser;
		is_array($config) || $config = $this->parser->parse($config);
		$this->config = $config;
	}
	
	/**
	 * 获取app根目录
	 *
	 * @return string
	 */
	public function getRootPath(){
		return empty($this->config[self::ROOT_PATH]) ? dirname($_SERVER['SCRIPT_FILENAME']) : Wind::getRealPath(
				$this->config[self::ROOT_PATH], false);
	}
	
	/**
	 *获取模块配置
	 *
	 * @param string $moduleName
	 * @return array
	 */
	public function getModules($moduleName = ''){
		return $moduleName === '' ? $this->config[self::MODULES] : $this->config[self::MODULES][$moduleName];
	}
	
	/**
	 * 获取过滤器配置
	 *
	 * @param string $filterName
	 * @return array
	 */
	public function getFilters($filterName = ''){
		return $filterName === '' ? $this->config[self::FILTERS] : $this->config[self::FILTERS][$moduleName];
	}
	
	/**
	 * 获取组件配置
	 *
	 * @param string $componentName
	 * @return array
	 */
	public function getComponentsConfig($componentName = ''){
		if (isset($this->config[self::COMPONENTS]['resource'])) {
			$path = Wind::getRealPath($this->config['components']['resource'], true, true);
			$components = $this->parser->parse($path);
			unset($components['router']);
			$this->config[self::COMPONENTS] = $components;
		}
		return $componentName === '' ? $this->config[self::COMPONENTS] : $this->config[self::COMPONENTS][$componentName];
	} 
	
}
