<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.config.AbstractWindConfig');
/**
 * 框架配置对象windConfig类，
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindConfig extends AbstractWindConfig {
	/**
	 * import 外部配置文件包含
	 * */
	const IMPORTS = 'imports';
	const IMPORTS_RESOURCE = 'resource';
	const IMPORTS_SUFFIX = 'suffix';
	const IMPORTS_IS_APPEND = 'is-append';
	
	/**
	 * 应用的入口地址
	 */
	const ROOTPATH = 'rootPath';
	const APPLICATIONS = 'applications';
	const APPLICATIONS_CLASS = 'class';
	const ERROR = 'error';
	const ERROR_ERRORACTION = 'errorAction';
	const ERROR_CLASS = 'class';
	
	/**
	 * 模快設置
	 */
	const MODULES = 'modules';
	const MODULE_PATH = 'path';
	const MODULE_TEMPLATE = 'template';
	const MODULE_CONTROLLER_SUFFIX = 'controllerSuffix';
	const MODULE_ACTION_SUFFIX = 'actionSuffix';
	const MODULE_METHOD = 'method';
	
	/**
	 * 过滤器链
	 */
	const FILTERS = 'filters';
	const FILTER_CLASS = 'class';
	
	/**
	 * 模板相关配置信息
	 * 1.模板文件存放路径
	 * 2.默认的模板文件名称
	 * 3.模板文件后缀名
	 * 4.视图解析器
	 * 5.模板文件的缓存路径
	 * 6.模板编译路径
	 */
	const TEMPLATE = 'templates';
	const TEMPLATE_DIR = 'dir';
	const TEMPLATE_DEFAULT = 'default';
	const TEMPLATE_EXT = 'ext';
	const TEMPLATE_RESOLVER = 'resolver';
	const TEMPLATE_ISCACHE = 'isCache';
	const TEMPLATE_CACHE_DIR = 'cacheDir';
	const TEMPLATE_COMPILER_DIR = 'compileDir';
	
	/**
	 * 模板引擎配置信息
	 */
	const VIEWER_RESOLVERS = 'viewerResolvers';
	
	/**
	 * 路由策略配置
	 */
	const ROUTER = 'router';
	const ROUTER_PARSER = 'parser';
	
	/**
	 * 路由解析器配置
	 */
	const ROUTER_PARSERS = 'routerParsers';
	const ROUTER_PARSERS_RULE = 'rule';
	const ROUTER_PARSERS_CLASS = 'class';
	
	protected $rootPath = '';
	protected $appName = 'windApp';
	protected $imports = array();
	
	public function __construct($config, $configParser, $appName) {
		$cacheName = $appName . '_config';
		$this->appName = $appName;
		parent::__construct($config, $configParser, $cacheName);
	}
	
	/**
	 * @param string $appName | 应用名称
	 * @param string $config | 配置文件路径信息
	 */
	public function initConfig($config) {
		if (!is_array($config)) {
			if ($this->getConfigParser() === null) throw new WindException('configParser is null.');
			$config = $this->getConfigParser()->parseConfig($config, $this->getCacheName());
		}
		$this->setConfig($config);
	}
	
	/**
	 * 返回应用路径信息
	 * 
	 * @return string
	 */
	public function getRootPath() {
		if (empty($this->rootPath) && !($this->rootPath = $this->getConfig(self::ROOTPATH))) {
			$this->rootPath = dirname($_SERVER['SCRIPT_FILENAME']);
		}
		return $this->rootPath;
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getImports($name = '') {
		return $name === '' ? $this->getConfig(self::IMPORTS) : $this->parseImport($name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getModules($name = '') {
		return $this->getConfig(self::MODULES, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getTemplate($name = '') {
		return $this->getConfig(self::TEMPLATE, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getFilters($name = '') {
		return $this->getConfig(self::FILTERS, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getViewerResolvers($name = '') {
		return $this->getConfig(self::VIEWER_RESOLVERS, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getRouter($name = '') {
		return $this->getConfig(self::ROUTER, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getRouterParsers($name = '') {
		return $this->getConfig(self::ROUTER_PARSERS, $name);
	}
	
	/**
	 * @param string $name
	 * @return Ambigous <string, multitype:, unknown>
	 */
	public function getApplications($name = '') {
		return $this->getConfig(self::APPLICATIONS, $name);
	}
	
	/**
	 * @param string $name
	 * @return Ambigous <string, multitype:, unknown>
	 */
	public function getErrorMessage($name = '') {
		return $this->getConfig(self::ERROR, $name);
	}
	
	/**
	 * @return the $appName
	 */
	public function getAppName() {
		return $this->appName;
	}
	
	/**
	 * @param name
	 * @param cacheName
	 * @param configPath
	 * @param append
	 */
	protected function parseImport($name) {
		if (!isset($this->imports[$name])) {
			$imports = $this->getConfig(self::IMPORTS);
			if (!isset($imports[$name])) return array();
			$import = $imports[$name];
			$config = array();
			if (is_array($import) && !empty($import)) {
				$configPath = L::getRealPath($import[self::IMPORTS_RESOURCE], $import[self::IMPORTS_SUFFIX]);
				if (!isset($import[self::IMPORTS_IS_APPEND]) || $import[self::IMPORTS_IS_APPEND] === 'true') {
					$append = $this->configCache;
				} elseif ($import[self::IMPORTS_IS_APPEND] === 'false' || $import[self::IMPORTS_IS_APPEND] === '') {
					$append = false;
				} else {
					$append = $import[self::IMPORTS_IS_APPEND];
				}
				$cacheName = $append ? $name : $this->appName . '_' . $name . '_config';
				$config = $this->parseConfig($configPath, $cacheName, $append);
			}
			$this->imports[$name] = $config;
		}
		return $this->imports[$name];
	}

}