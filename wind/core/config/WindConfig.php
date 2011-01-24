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

	const CLASS_PATH = 'class';

	const PATH = 'path';

	/**
	 * import 外部配置文件包含
	 * */
	const IMPORTS = 'imports';

	const IMPORTS_RESOURCE = 'resource';

	const IMPORTS_SUFFIX = 'suffix';

	const IMPORTS_IS_APPEND = 'is-append';

	/*
	 * app 相关配置
	 * */
	const WEB_APPS = 'web-apps';

	const WEB_APP_ROOT_PATH = 'root-path';

	const WEB_APP_FACTORY = 'factory';

	const WEB_APP_FACTORY_CLASS_DEFINITION = 'class-definition';

	const WEB_APP_FILTER = 'filters';

	const WEB_APP_ROUTER = 'router';

	const WEB_APP_MODULE = 'modules';

	const WEB_APP_TEMPLATE = 'template';

	/**
	 * 应用的入口地址
	 */
	const ERROR = 'error';

	const ERROR_ERRORACTION = 'errorAction';

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
	 * 路由解析器配置
	 */
	const ROUTER_PARSERS = 'routerParsers';

	const ROUTER_PARSERS_RULE = 'rule';

	const ROUTER_PARSERS_CLASS = 'class';

	protected $appName = '';

	protected $imports = array();

	/**
	 * Enter description here ...
	 * 
	 * @param string $config
	 * @param WindConfigParser $configParser
	 * @param string $appName
	 */
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
		$this->checkWindConfig();
	}

	/* (non-PHPdoc)
	 * @see AbstractWindConfig::getConfig()
	 */
	public function getConfig($configName = '', $subConfigName = '', $config = array()) {
		$imports = parent::getConfig(self::IMPORTS);
		if (key_exists($configName, $imports)) {
			return $this->parseImport($configName);
		}
		return parent::getConfig($configName, $subConfigName, $config);
	}

	/**
	 * @return the $appName
	 */
	public function getAppName() {
		return $this->appName;
	}

	/**
	 * 返回当前应用的启动脚本位置
	 */
	public function getAppClass() {
		$_config = $this->getConfig(self::WEB_APPS, $this->appName);
		return $this->getConfig(self::CLASS_PATH, '', $_config);
	}

	/**
	 * 返回应用路径信息
	 * 
	 * @return string
	 */
	public function getRootPath($appName = '') {
		if ($appName === '') {
			$appName = $this->appName;
		}
		$propertyName = $appName . 'RootPath';
		if (!isset($this->$propertyName)) {
			$appConfig = $this->getConfig(self::WEB_APPS, $appName);
			$rootPath = isset($appConfig[self::WEB_APP_ROOT_PATH]) ? $appConfig[self::WEB_APP_ROOT_PATH] : dirname($_SERVER['SCRIPT_FILENAME']);
			//TODO 绝对路径相对路径判断，相对于webroot，支持自定义路径
			$this->$propertyName = $rootPath;
		}
		return $this->$propertyName;
	}

	/**
	 * @param string $name
	 */
	public function getFactory($name = '') {
		$_config = $this->getConfig(self::WEB_APPS, $this->appName);
		return $this->getConfig(self::WEB_APP_FACTORY, $name, $_config);
	}

	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getFilters($name = '') {
		$_config = $this->getConfig(self::WEB_APPS, $this->appName);
		return $this->getConfig(self::WEB_APP_FILTER, $name, $_config);
	}

	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getRouter($name = '') {
		$_config = $this->getConfig(self::WEB_APPS, $this->appName);
		return $this->getConfig(self::WEB_APP_ROUTER, $name, $_config);
	}

	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getModules($name = '') {
		$_config = $this->getConfig(self::WEB_APPS, $this->appName);
		return $this->getConfig(self::WEB_APP_MODULE, $name, $_config);
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
	public function getViewerResolvers($name = '') {
		return $this->getConfig(self::VIEWER_RESOLVERS, $name);
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
	 * 配置信息合法性检查
	 */
	protected function checkWindConfig() {
		if (!$this->getConfig(self::WEB_APPS, $this->appName)) {
			throw new WindException('config error.');
		}
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
					$append = $this->cacheName;
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