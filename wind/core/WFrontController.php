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
	
	/**
	 * 系统初始化操作
	 * @param array $config
	 * @return null
	 */
	function init($config = array()) {
		parent::init();
		$this->_initConfig($config);
	}
	
	function run() {
		if ($this->config === null)
			throw new WException('init system config failed!');
		$this->beforProcess();
		if (!class_exists('WFilterFactory'))
			parent::run();
		else
			$this->_initFilter();
		$this->afterProcess();
	}
	
	protected function beforProcess() {

	}
	
	function process($request, $response) {
		$config = W::getInstance('WSystemConfig');
		/* 初始化一个应用服务器 */
		$applicationController = new WWebApplicationController();
		$router = $applicationController->createRouter($config);
		$router->doParser($response, $request);
		
		
		$applicationController->destory();
	}
	
	protected function afterProcess() {
		if (defined('LOG_RECORD'))
			WLog::flush();
		restore_exception_handler();
	}
	
	protected function doPost($request, $response) {
		$this->process($request, $response);
	}
	
	protected function doGet($request, $response) {
		$this->process($request, $response);
	}
	
	/**
	 * 初始化过滤器，并将程序执行句柄指向一个过滤器入口
	 */
	private function _initFilter() {
		WFilterFactory::getFactory()->setExecute(array(
			get_class($this), 
			'process'
		), $this->reuqest, $this->response);
		$filter = WFilterFactory::getFactory()->create($this->config);
		if (is_object($filter))
			$filter->doFilter($this->reuqest, $this->response);
	}
	
	/**
	 * 初始化系统配置信息
	 * @param array $config
	 */
	private function _initConfig($config) {
		$realPath = W::getSystemConfigPath();
		if (!file_exists($realPath))
			throw new Exception('SYS Excetion ：config file ' . $realPath . ' is not exists!!!');
		
		W::import($realPath);
		$sysConfig = W::getVar('sysConfig');
		$configObj = W::getInstance('WSystemConfig');
		$configObj->parse($sysConfig, (array) $config);
		$this->config = $configObj;
	}

}