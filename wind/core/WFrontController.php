<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

/**
 * 
 * 抽象的前端控制器接口，通过集成该接口可以实现以下职责
 * 
 * 职责定义：
 * 接受客户请求
 * 处理请求
 * 向客户端发送响应
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WFrontController extends WActionServlet {
	private $config = null;
	
	function init($config = array()) {
		parent::init();
		$this->_initConfig($config);
	}
	
	/**
	 * @return WConfig
	 */
	private function _initConfig($config) {
		$realPath = W::getSystemConfigPath() . W::getSeparator() . W::$_system_config;
		if (!file_exists($realPath))
			throw new Exception('SYS Excetion ：config file ' . $realPath . ' is not exists!!!');
		W::import($realPath);
		$sysConfig = W::getVar('sysConfig');
		$config = $config ? $config : array();
		$configObj = new WSystemConfig();
		$configObj->parse($sysConfig, $config);
		$this->config = $configObj;
	}
	
	protected function process() {
		$this->beforProcess();
		
		$applicationController = new WWebApplicationController();
		
		$router = $applicationController->createRouterParser($this->config);
		$router->doParser($this->config, $this->reuqest);
		
		//		$controller = $applicationController->createController($router);
		

		$filterChain = $applicationController->createFilterChain($this->config, $router);
		
		$filterChain->doFilter(array(
			get_class($applicationController), 
			'processRequest'
		), $this->reuqest);
		
		$this->afterProcess();
	}
	
	protected function beforProcess() {

	}
	
	protected function afterProcess() {
		if(defined('LOG_RECORD')){
			WLog::flush();
		}
	}
	
	protected function doPost() {
		$this->process();
	}
	
	protected function doGet() {
		$this->process();
	}

}