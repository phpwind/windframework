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
	private $modules = array();

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
		if (empty($config)) return;
		if (is_string($config)) {
			$configParser = $factory->getInstance('configParser');
			$config = $configParser->parse($config);
			if (isset($config[$this->appName])) $this->_config = $config[$this->appName];
		} else
			$this->_config = $this->_config ? array_merge($this->_config, $config) : (array) $config;
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
	public function getAppClass($default = '') {
		return $this->getConfig('class', '', $default);
	}

	/**
	 * 返回应用编码信息
	 */
	public function getCharset() {
		return $this->getConfig('charset', '', 'utf-8');
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
		return $this->getConfig('modules', $name, array());
	}

	/**
	 * 添加module
	 * <controller-path>controller</controller-path>
	 * <!-- 指定该模块下的controller的后缀格式 -->
	 * <controller-suffix>Controller</controller-suffix>
	 * <!-- 配置该模块的error处理的action controller类 -->
	 * <error-handler>WIND:core.web.WindErrorHandler</error-handler>
	 * <!-- 试图相关配置，config中配置可以根据自己的需要进行配置或是使用缺省 -->
	 * <view class='windView'>
	 * <!-- 可以在这里进行view的配置，该配置只会影响该module下的view行为，该配置可以设置也可以不设置 -->
	 * <config>
	 * <!-- 指定模板路径 -->
	 * <template-dir>template</template-dir>
	 * <!-- 指定模板后缀 -->
	 * <template-ext>htm</template-ext>
	 * <!-- 模板编译文件存放路径 -->
	 * <compile-dir>data.template</compile-dir>
	 * </config>
	 * </view>
	 * 
	 * @param string $name
	 * @param array $config
	 * @return
	 */
	public function setModules($name, $config = array()) {
		$this->_config['modules'][$name] = WindUtility::mergeArray($this->getDefaultConfigStruct('modules'), $config);
	}

	/**
	 * 根据module名称返回module的视图处理类
	 * @param string $name
	 * @param string $default
	 */
	public function getModuleViewClass($name, $default = '') {
		return $this->getConfig('view', 'class', $default, $this->getModules($name));
	}

	/**
	 * 根据module名称返回module的视图配置信息
	 * @param string $name
	 * @param string $default
	 */
	public function getModuleViewConfig($name, $default = '') {
		return $this->getConfig('view', 'config', $default, $this->getModules($name));
	}

	/**
	 * 根据module名称返回错误的处理句柄
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	public function getModuleErrorHandler($name, $default = '') {
		return $this->getConfig('errorhandler', '', $default, $this->getModules($name));
	}

	/**
	 * 返回指定moduleName的controller路径信息
	 * @param string $name
	 * @return string
	 */
	public function getModuleControllerPath($name, $default = '') {
		return $this->getConfig('controller-path', '', $default, $this->getModules($name));
	}

	/**
	 * 返回指定moduleName的controller后缀
	 * @param string $name
	 * @return string
	 */
	public function getModuleControllerSuffix($name, $default = '') {
		return $this->getConfig('controller-suffix', '', $default, $this->getModules($name));
	}

	/**
	 * 返回指定ComponentName的Component配置信息
	 * @param string $name
	 * @return string
	 */
	public function getComponents($name = '', $default = array()) {
		return $this->getConfig('components', $name, $default);
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
	private function parseConfig($config, $key = 'config', $append = true) {
		if (!$config) return array();
		$configParser = $this->getSystemConfig()->getInstance('configParser');
		return $configParser->parse($config);
	}

	/**
	 * 返回对应的配置结构及默认值
	 * @param string $configName
	 * @throws WindException
	 * @return string
	 */
	public function getDefaultConfigStruct($configName) {
		$_tmp = array();
		$_tmp['modules'] = array('controller-path' => 'controller', 'controller-suffix' => 'Controller', 
			'error-handler' => 'WIND:core.web.WindErrorHandler', 'template-dir' => 'template', 'template-ext' => 'htm', 
			'compile-dir' => 'data.template', 'compile-suffix' => 'tpl');
		return $configName ? (isset($_tmp[$configName]) ? $_tmp[$configName] : array()) : array();
	}
}