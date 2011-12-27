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
class WindFrontController extends AbstractWindFrontController {
	const DF_COMPONENT_CONFIG = 'WIND:components_config.php';

	/**
	 * @param string $appName 默认appName
	 * @param array|string $config 默认配置
	 * @return void
	 */
	protected function initApplication($appName, $config) {
		$this->request = new WindHttpRequest();
		$this->response = new WindHttpResponse();
		$this->factory || $this->factory = new WindFactory(@include (Wind::getRealPath(self::DF_COMPONENT_CONFIG, true)));
		$config && $this->initConfig($config, $this->factory);
		$this->_config['defaultApp'] = $appName;
		empty($this->_config['router']) || $this->factory->loadClassDefinitions(
			array('router' => $this->_config['router']));
	}

	/**
	 * 解析应用配置
	 *
	 * @param array|string $config
	 * @param WindFactory $factory
	 */
	private function initConfig($config, $factory) {
		is_array($config) || $config = $factory->getInstance('configParser')->parse($config);
		foreach ($config['web-apps'] as $key => $value) {
			if (isset($this->_config['web-apps'][$key])) continue;
			$rootPath = empty($value['root-path']) ? dirname($_SERVER['SCRIPT_FILENAME']) : Wind::getRealPath(
				$value['root-path'], false);
			Wind::register($rootPath, $key, true);
		}
		$this->_config = $config;
	}
}

?>