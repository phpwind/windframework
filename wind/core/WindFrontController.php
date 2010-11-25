<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

L::import('WIND:core.base.WindServlet');
L::import('WIND:component.exception.WindException');
L::import('WIND:component.filter.WindFilterFactory');
L::import('WIND:core.WindSystemConfig');
L::import('WIND:core.WindWebApplication');
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
class WindFrontController extends WindServlet {
	private $config = null;
	private static $instance = null;
	
	protected function __construct($config = array()) {
		parent::__construct();
		echo '<pre/>';
		$this->_initConfig($config);
		exit();
	}
	
	public function run() {
		if ($this->config === null) throw new WindException('init system config failed!');
		$this->beforProcess();
		$filters = $this->config->getConfig('filters');
		if (!class_exists('WindFilterFactory') || empty($filters))
			parent::run();
		else
			$this->_initFilter();
		$this->afterProcess();
	}
	
	protected function beforProcess() {

	}
	
	function process($request, $response) {
		/* 初始化一个应用服务器 TODO重构此代码 */
		$applicationController = new WindWebApplication();
		$applicationController->init();
		
		$applicationController->processRequest($request, $response);
		
		$applicationController->destory();
	}
	
	protected function afterProcess() {
		if (defined('LOG_RECORD')) WindLog::flush();
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
		WindFilterFactory::getFactory()->setExecute(array(get_class($this), 'process'), $this->reuqest, $this->response);
		$filter = WindFilterFactory::getFactory()->create($this->config);
		if (is_object($filter)) $filter->doFilter($this->reuqest, $this->response);
	}
	
	/**
	 * 初始化系统配置信息
	 * 
	 * @param array $config
	 */
	private function _initConfig($config) {
		$configParser = new WindConfigParser($this->request);
		$appName = $configParser->parser();//执行解析
		W::parserConfig();//设置全局apps
		W::setCurrentApp($appName);
		$configObj = WindSystemConfig::getInstance();
		$configObj->parse((array) W::getSystemConfig(), W::getCurrentApp()); 
//		$configObj->parse((array) W::getSystemConfig(), (array) $config);
		$this->config = $configObj;
	}
	
	/**
	 * @param array $config
	 * @return WindFrontController
	 */
	static public function &getInstance(array $config = array()) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($config);
		}
		return self::$instance;
	}
}