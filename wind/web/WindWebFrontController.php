<?php
/**
 * 应用前端控制器
 * 
 * 应用前端控制器，负责根据应用配置启动应用，多应用管理，多应用的配置管理等.
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: WindFrontController.php 2966 2011-10-14 06:41:59Z yishuo $
 * @package wind
 */
class WindWebFrontController extends WindFrontController {

	/** 
	 * 创建并执行当前应用
	 * 
	 * @param string $appName
	 * @param string|array $config
	 * @return void
	 */
	public function multiRun() {
		$this->request || $this->request = new WindHttpRequest();
		$this->response || $this->response = new WindHttpResponse();
		
		/* @var $router WindRouter */
		$router = $this->factory->getInstance('router');
		if ($this->_appName) {
			$router->setApp($this->_appName);
		} elseif (!$this->getApp()) {
			$this->_appName = 'default';
			$router->setApp('default');
		}
		$router->route($this->request, $this->response);
		$this->_appName = $router->getApp();
		$this->_run($this->createApplication());
	}

	/**
	 * 返回对应app的配置信息
	 * 
	 * @param string $appName
	 * @return array
	 */
	public function getConfig($appName) {
		return isset($this->_config[$appName]) ? $this->_config[$appName] : array();
	}

	/**
	 * 设置app配置
	 * 
	 * 设置app配置到当前应用,适用于动态挂载一个app
	 * @param string $appName
	 * @param array $config
	 */
	public function setConfig($appName, $config) {
		if (isset($this->_config[$appName])) return;
		if (!$config || !is_array($config)) return;
		$this->_config[$appName] = $config;
	}

	/* (non-PHPdoc)
	 * @see WindFrontController::initConfig()
	 */
	protected function initConfig($config) {
		foreach ($config['web-apps'] as $key => $value) {
			$rootPath = empty($value['root-path']) ? dirname($_SERVER['SCRIPT_FILENAME']) : Wind::getRealPath(
				$value['root-path'], false);
			Wind::register($rootPath, $key, true);
			$this->_config[$key] = $value;
		}
	}

}

?>