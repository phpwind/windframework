<?php
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
	
	/* app 相关配置 */
	const WEB_APPS = 'web-apps';
	const WEB_APP_ROOT_PATH = 'root-path';
	const WEB_APP_FACTORY_CLASS_DEFINITION = 'class-definition';
	const WEB_APP_FILTER = 'filters';
	const WEB_APP_ROUTER = 'router';
	const WEB_APP_MODULE = 'modules';
	const WEB_APP_TEMPLATE = 'template';

	/**
	 * @param string $config
	 * @param WindConfigParser $configParser
	 * @param string $appName
	 */
	public function __construct($config, $configParser, $appName) {
		$cacheName = $appName . '_config';
		parent::__construct($config, $configParser, $cacheName);
		$_config = $this->getConfig(self::WEB_APPS, $appName);
		$this->setConfig($_config);
	}

	/**
	 * @return the $appName
	 */
	public function getAppName() {
		return Wind::getAppName();
	}

	/**
	 * 返回当前应用的启动脚本位置
	 */
	public function getAppClass() {
		return $this->getConfig(self::CLASS_PATH, '', array(), COMPONENT_WEBAPP);
	}

	/**
	 * 返回应用路径信息
	 * 
	 * @return string
	 */
	public function getRootPath() {
		$_tmp = '_rootPath';
		if (!isset($this->$_tmp)) {
			$rootPath = $this->getConfig(self::WEB_APP_ROOT_PATH);
			if (!$rootPath) $rootPath = dirname($_SERVER['SCRIPT_FILENAME']);
			$this->$_tmp = $rootPath;
		}
		return $this->$_tmp;
	}

	/**
	 * 返回filterChain的类型
	 * 
	 * @return array
	 */
	public function getFilterClass() {
		return $this->getFilters(self::CLASS_PATH);
	}

	/**
	 * 返回配置定义中定义的过滤链列表
	 * 如果定义$name则返回在filters定义标签内对应的属性值
	 * 
	 * @param string $name
	 * @return array|string
	 */
	public function getFilters($name = '') {
		return $this->getConfig(self::WEB_APP_FILTER, $name);
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
		return $this->getConfig(self::WEB_APP_ROUTER, $name, array(), COMPONENT_ROUTER);
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
		return $this->getConfig(self::WEB_APP_MODULE, $name);
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

}