<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-21
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:core.config.WindConfig');
/**
 * 框架配置对象windConfig类，
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindSystemConfig extends WindConfig {
	/* 通用配置 */
	const CLASS_PATH = 'class';
	const PATH = 'path';
	const VALUE = 'value';
	/* import 外部配置文件包含 */
	const IMPORTS = 'imports';
	const IMPORTS_RESOURCE = 'resource';
	const IMPORTS_IS_APPEND = 'is-append';
	/* app 相关配置 */
	const WEB_APPS = 'web-apps';
	const WEB_APP_ROOT_PATH = 'root-path';
	const WEB_APP_FACTORY = 'factory';
	const WEB_APP_FACTORY_CLASS_DEFINITION = 'class-definition';
	const WEB_APP_FILTER = 'filters';
	const WEB_APP_ROUTER = 'router';
	const WEB_APP_MODULE = 'modules';
	const WEB_APP_TEMPLATE = 'template';
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

	/* (non-PHPdoc)
	 * @see AbstractWindConfig::getConfig()
	 */
	public function getConfig($configName = '', $subConfigName = '', $config = array(), $default = null) {
		$imports = parent::getConfig(self::IMPORTS);
		if (key_exists($configName, (array) $imports)) {
			return $this->parseImport($configName);
		}
		return parent::getConfig($configName, $subConfigName, $config, $default);
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
		$_tmp = $this->getConfig(self::CLASS_PATH, '', $_config);
		return $_tmp ? $_tmp : COMPONENT_WEBAPP;
	}

	/**
	 * 返回应用路径信息
	 * @return string
	 */
	public function getRootPath($appName = '') {
		if ($appName === '') $appName = $this->appName;
		$_tmp = $appName . '_RootPath';
		if (!isset($this->$_tmp)) {
			$appConfig = $this->getConfig(self::WEB_APPS, $appName);
			if (isset($appConfig[self::WEB_APP_ROOT_PATH]) && !empty($appConfig[self::WEB_APP_ROOT_PATH]))
				$rootPath = $appConfig[self::WEB_APP_ROOT_PATH];
			else
				$rootPath = dirname($_SERVER['SCRIPT_FILENAME']);
			$this->$_tmp = $rootPath;
		}
		return $this->$_tmp;
	}

	/**
	 * @param string $name
	 */
	public function getFactory($name = '') {
		$_config = $this->getConfig(self::WEB_APPS, $this->appName);
		return $this->getConfig(self::WEB_APP_FACTORY, $name, $_config);
	}

	/**
	 * 返回filterChain的类型
	 * @return array
	 */
	public function getFilterClass() {
		return $this->getFilters(self::CLASS_PATH);
	}

	/**
	 * 返回配置定义中定义的过滤链列表
	 * 如果定义$name则返回在filters定义标签内对应的属性值
	 * @param string $name
	 * @return array|string
	 */
	public function getFilters($name = '') {
		$_config = $this->getConfig(self::WEB_APPS, $this->appName);
		return $this->getConfig(self::WEB_APP_FILTER, $name, $_config);
	}

	/**
	 * 返回路由类型定义
	 * @return string
	 */
	public function getRouterClass() {
		return $this->getRouter(WIND_CONFIG_CLASS);
	}

	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getRouter($name = '') {
		$_config = $this->getConfig(self::WEB_APPS, $this->appName);
		$_router = $this->getConfig(self::WEB_APP_ROUTER, $name, $_config);
		return $_router ? $_router : COMPONENT_ROUTER;
	}

	/**
	 * <modules>
	 * <!-- name: 模块的名字    path: 模块的路径（采用命名空间的方式) -->
	 * <module name='default' path='controller'>
	 * <!-- 指定该模块下的controller的后缀格式 -->
	 * <controller-suffix value='Controller' />
	 * <!-- 配置该模块的error处理的action controller类 -->
	 * <error-handler class='WIND:core.web.WindErrorHandler'/>
	 * <!-- 试图相关配置，config中配置可以根据自己的需要进行配置或是使用缺省 -->
	 * <view>
	 * <config>
	 * <!-- 指定模板路径 -->
	 * <template-dir value='template' />
	 * <!-- 指定模板后缀 -->
	 * <template-ext value='htm' />
	 * <!-- 模板编译文件存放路径 -->
	 * <compile-dir value='compile.template' />
	 * </config>
	 * </view>
	 * </module>
	 * </modules>
	 * @param string $name
	 * @return array|string
	 */
	public function getModules($name = '') {
		$_config = $this->getConfig(self::WEB_APPS, $this->appName);
		return $this->getConfig(self::WEB_APP_MODULE, $name, $_config);
	}

	/**
	 * 根据module名称返回module的视图处理类
	 * @param string $name
	 * @param string $default
	 */
	public function getModuleViewClassByModuleName($name, $default = '') {
		$module = $this->getModules($name);
		return $this->getConfig('view', WIND_CONFIG_CLASS, $module, $default);
	}

	/**
	 * 根据module名称返回module的视图配置信息
	 * @param string $name
	 * @param string $default
	 */
	public function getModuleViewConfigByModuleName($name, $default = '') {
		$module = $this->getModules($name);
		return $this->getConfig('view', WIND_CONFIG_CONFIG, $module, $default);
	}

	/**
	 * 根据module名称返回错误的处理句柄
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	public function getModuleErrorHandlerByModuleName($name, $default = '') {
		$module = $this->getModules($name);
		return $this->getConfig('error-handler', WIND_CONFIG_CLASS, $module, $default);
	}

	/**
	 * 返回指定moduleName的controller路径信息
	 * @param string $name
	 * @return string
	 */
	public function getModuleControllerPathByModuleName($name, $default = '') {
		$module = $this->getModules($name);
		return $this->getConfig(WIND_CONFIG_CLASSPATH, '', $module, $default);
	}

	/**
	 * 返回指定moduleName的controller后缀
	 * @param string $name
	 * @return string
	 */
	public function getModuleControllerSuffixByModuleName($name, $default = '') {
		$module = $this->getModules($name);
		return $this->getConfig('controller-suffix', WIND_CONFIG_VALUE, $module, $default);
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
				$configPath = Wind::getRealPath($import[self::IMPORTS_RESOURCE]);
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