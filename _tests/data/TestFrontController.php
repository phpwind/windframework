<?php

require_once ('web/WindFrontController.php');

class TestFrontController extends WindFrontController {
	public function afterRun() {
		restore_error_handler();
		restore_exception_handler();
		$this->getApp()->getResponse()->sendResponse();
		$this->getApp()->getWindFactory()->executeDestroyMethod();
		array_pop($this->_currentApp);
		$this->_currentAppName = end($this->_currentApp);
	}
	
	public function beforRun($appName) {
		if (in_array($appName, $this->_currentApp)) {
			throw new WindException('[wind.beforRun] Nested request', WindException::ERROR_SYSTEM_ERROR);
		}
		$appName && $this->_currentAppName = $appName;
		array_push($this->_currentApp, $this->_currentAppName);
		set_error_handler('WindHelper::errorHandle');
		set_exception_handler('WindHelper::exceptionHandle');
	}
	
	public function createApplication($appName) {
		if (!isset($this->_app[$appName])) {
			$config = $this->getAppConfig($appName);
			if (!empty($config['components'])) {
				unset($config['components']['router']);
				$this->factory->loadClassDefinitions($config['components']);
			}
			$application = $this->factory->getInstance('windApplication', 
				array($this->request, $this->response, $this->factory));
			$application->setConfig($config);
			$this->request = $this->response = $this->factory = null;
			$this->_app[$appName] = $application;
		}
		return $this->_app[$appName];
	}
	
	public function initConfig($config, $factory) {
		is_array($config) || $config = $factory->getInstance('configParser')->parse($config);
		foreach ($config['web-apps'] as $key => $value) {
			if (isset($this->_config['web-apps'][$key])) continue;
			$rootPath = empty($value['root-path']) ? dirname($_SERVER['SCRIPT_FILENAME']) : Wind::getRealPath(
				$value['root-path'], true);
			Wind::register($rootPath, $key, true);
			if ('default' !== $key && !empty($config['default'])) {
				$value = WindUtility::mergeArray($config['default'], $value);
			}
			$this->setConfig($key, $value);
		}
		$this->_config['router'] = isset($config['router']) ? $config['router'] : array();
	}
}

?>