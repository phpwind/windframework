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
	private static $instance = null;
	
	protected function __construct($config = array()) {
		parent::__construct();
		$this->_initConfig($config);
	}
	
	/**
	 * @param array $config
	 * @return WFrontController
	 */
	static public function &getInstance(array $config = array()) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($config);
		}
		return self::$instance;
	}
	
	public function run() {
		if ($this->config === null)
			throw new WException('init system config failed!');
		$this->beforProcess();
		$filters = $this->config->getConfig('filters');
		if (!class_exists('WFilterFactory') || empty($filters))
			parent::run();
		else
			$this->_initFilter();
		$this->afterProcess();
	}
	
	protected function beforProcess() {

	}
	
	function process($request, $response) {
		/* 初始化一个应用服务器 */
		$applicationController = new WWebApplicationController();
		$applicationController->init();
		
		$applicationController->processRequest($request, $response);
		
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
		WFilterFactory::getFactory()->setExecute(array(get_class($this), 'process'), $this->reuqest, $this->response);
		$filter = WFilterFactory::getFactory()->create($this->config);
		if (is_object($filter))
			$filter->doFilter($this->reuqest, $this->response);
	}
	
	/**
	 * 初始化系统配置信息
	 * 
	 * @param array $config
	 */
	private function _initConfig($config) {
		$realPath = W::getSystemConfigPath();
		W::import($realPath);
		$sysConfig = W::getVar('sysConfig');
		$configObj = W::getInstance('WSystemConfig');
		$configObj->parse($sysConfig, (array) $config);
		$this->config = $configObj;
	}

}