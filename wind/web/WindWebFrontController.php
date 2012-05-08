<?php
Wind::import('WIND:base.AbstractWindFrontController');
/**
 * 应用前端控制器
 *
 * 应用前端控制器，负责根据应用配置启动应用，多应用管理，多应用的配置管理等.
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: WindFrontController.php 2966 2011-10-14 06:41:59Z yishuo $
 * @package wind
 */
class WindWebFrontController extends AbstractWindFrontController {
	protected $components = 'WIND:web.web.components';
	protected $request = 'WIND:web.WindHttpRequest';
	private $_configStateQueue = array();

	/**
	 * 创建并执行当前应用
	 *
	 * @param string $appName        	
	 * @param string|array $config        	
	 * @return void
	 */
	public function multiRun() {
		/* @var $router WindRouter */
		$router = $this->factory->getInstance('router');
		if ($this->_appName) {
			$router->setApp($this->_appName);
		} elseif (!$this->getApp()) {
			$this->_appName = 'default';
			$router->setApp('default');
		}
		$router->route($this->request);
		$this->_appName = $router->getApp();
		if (!in_array($this->_appName, $this->_configStateQueue) && $this->_appName !== 'default' && isset(
			$this->_config['default']) && isset($this->_config[$this->_appName])) {
			$this->_config[$this->_appName] = WindUtility::mergeArray($this->_config['default'], 
				$this->_config[$this->_appName]);
			$this->_configStateQueue[] = $this->_appName;
		}
		$this->_run();
	}
	
	/*
	 * (non-PHPdoc) @see AbstractWindFrontController::createApplication()
	 */
	protected function _createApplication() {
		Wind::import('WIND:web.WindWebApplication');
		$application = new WindWebApplication($this->request, $this->factory);
		$application->setDelayAttributes(
			array(
				'dispatcher' => array('ref' => 'dispatcher'), 
				'handlerAdapter' => array('ref' => 'router')));
		return $application;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindFrontController::_loadBaseLib()
	 */
	protected function _loadBaseLib() {
		Wind::$_classes += array(
			'WindController' => 'web/WindController', 
			'WindDispatcher' => 'web/WindDispatcher', 
			'WindErrorHandler' => 'web/WindErrorHandler', 
			'WindForward' => 'web/WindForward', 
			'WindHttpRequest' => 'web/WindHttpRequest', 
			'WindHttpResponse' => 'web/WindHttpResponse', 
			'WindSimpleController' => 'web/WindSimpleController', 
			'WindUrlHelper' => 'web/WindUrlHelper', 
			'WindWebApplication' => 'web/WindWebApplication', 
			'WindFormFilter' => 'web/filter/WindFormFilter');
	}

}

?>