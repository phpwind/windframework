<?php
/**
 * 框架配置对象windConfig类，
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindSystemConfig extends WindModule {
	private $appName = '';

	/**
	 * @param string $config
	 * @param string $appName
	 * @param WindFactory $factory
	 */
	public function __construct($config, $appName, $factory) {
		$this->appName = $appName;
		$this->setConfig($config, $factory);
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config, $factory = null) {
		if (is_string($config)) {
			$config = $this->parseConfig($config, 'config', '', $factory);
			if (isset($config[$this->appName]))
				$this->_config = $config[$this->appName];
		} else
			$this->_config = (array) $config;
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
		return $this->getConfig('class', '', COMPONENT_WEBAPP);
	}

	/**
	 * 返回配置定义中定义的过滤链列表
	 * 如果定义$name则返回在filters定义标签内对应的属性值
	 * 
	 * @param string $name
	 * @return array|string
	 */
	public function getFilters() {
		return $this->getConfig('filters');
	}

	/**
	 * 返回filterChain的类型
	 * 
	 * @return array
	 */
	public function getFilterClass() {
		return $this->getConfig('filters', 'class');
	}

	/**
	 * @param string $name
	 * @return array|string
	 */
	public function getRouter() {
		return $this->getConfig('router');
	}

	/**
	 * 返回当前路由的配置信息
	 * @return array
	 */
	public function getRouterConfig() {
		return $this->getConfig('router', 'config');
	}

	/**
	 * 返回路由类型定义
	 * @return string
	 */
	public function getRouterClass() {
		return $this->getConfig('router', 'class', COMPONENT_ROUTER);
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
		return $this->getConfig('modules', $name);
	}

	/**
	 * 根据module名称返回module的视图处理类
	 * @param string $name
	 * @param string $default
	 */
	public function getModuleViewClassByModuleName($name, $default = '') {
		$module = $this->getConfig('modules', $name);
		return $this->getConfig('view', 'class', $default, $module);
	}

	/**
	 * 根据module名称返回module的视图配置信息
	 * @param string $name
	 * @param string $default
	 */
	public function getModuleViewConfigByModuleName($name, $default = '') {
		$module = $this->getConfig('modules', $name);
		return $this->getConfig('view', 'config', $default, $module);
	}

	/**
	 * 根据module名称返回错误的处理句柄
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	public function getModuleErrorHandlerByModuleName($name, $default = '') {
		$module = $this->getConfig('modules', $name);
		return $this->getConfig('error-handler', '', $default, $module);
	}

	/**
	 * 返回指定moduleName的controller路径信息
	 * @param string $name
	 * @return string
	 */
	public function getModuleControllerPathByModuleName($name, $default = '') {
		$module = $this->getConfig('modules', $name);
		return $this->getConfig('controller-path', '', $default, $module);
	}

	/**
	 * 返回指定moduleName的controller后缀
	 * @param string $name
	 * @return string
	 */
	public function getModuleControllerSuffixByModuleName($name, $default = '') {
		$module = $this->getConfig('modules', $name);
		return $this->getConfig('controller-suffix', '', $default, $module);
	}

	/**
	 * 获得DB配置,根据DB名义的别名来获取DB链接配置信息.
	 * 当别名为空时,返回全部DB链接配置.
	 * 
	 * @param string $dbName
	 */
	public function getDbConfig($dbName = '') {
		$config = $this->getConfig('db');
		if (isset($config['resource']) && !empty($config['resource'])) {
			$_resource = Wind::getRealPath($config['resource'], true);
			$this->_config['db'] = $this->parseConfig($_resource, 'db');
		}
		return $this->getConfig('db', $dbName);
	}

	/**
	 * 配置解析方法
	 * @param string $config
	 * @param string $key
	 * @param string|boolean $append
	 * @param factory
	 */
	private function parseConfig($config, $key = 'config', $append = true, $factory = null) {
		if (!$config)
			return array();
		
		if ($factory === null)
			$factory = $this->getSystemFactory();
		$configParser = $factory->getInstance(COMPONENT_CONFIGPARSER);
		$append === true && $append = $this->appName . '_config';
		$config = $configParser->parse($config, $this->appName . '_' . $key, $append, 
			$factory->getInstance(COMPONENT_CACHE));
		return $config;
	}

}