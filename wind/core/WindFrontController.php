<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

L::import('WIND:core.base.WindServer');
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
class WindFrontController extends WindServer {
	private $applicationType;
	private $application;
	
	public function __construct() {
		parent::__construct();
		$this->initConfig();
	}
	
	public function run($applicationType = 'web') {
		$this->applicationType = $applicationType;
		$this->beforProcess();
		parent::run();
		$this->afterProcess();
	}
	
	protected function beforProcess() {}
	
	public function process($request, $response) {
		if ($this->initFilter()) return;
		$application = $this->createApplication();
		$application->init();
		$application->processRequest($request, $response);
		$application->destory();
	}
	
	protected function afterProcess() {
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
	private function initFilter() {
		$filters = C::getConfig(IWindConfig::FILTERS);
		if (empty($filters)) return;
		WindFilterFactory::getFactory()->setExecute(array($this, 'process'), $this->request, $this->response);
		$filter = WindFilterFactory::getFactory()->create();
		if ($filter instanceof WindFilter) {
			$filter->doFilter($this->request, $this->response);
			return false;
		}
		return true;
	}
	
	/**
	 * 初始化系统配置信息
	 * 
	 * @param array $config
	 */
	private function initConfig() {
		L::import('WIND:component.config.WindConfigParser');
		$configParser = new WindConfigParser();
		$appConfig = $configParser->parser($this->request);
		C::init($appConfig);
	}
	
	/**
	 * 初始化Dispatcher
	 */
	protected function initDispatcher() {
		if ($this->dispatcher instanceof WindDispatcher) return;
		$router = WindRouterFactory::getFactory()->create();
		$router->doParser($this->request, $this->response);
		$this->dispatcher = new WindWebDispatcher($this->request, $this->response);
		$this->dispatcher->initWithRouter($router);
	}
	
	/**
	 * @return WindWebApplication
	 */
	protected function createApplication() {
		if ($this->application === null) {
			$application = C::getApplications($this->applicationType);
			$className = L::import($application[IWindConfig::APPLICATIONS_CLASS]);
			if (!class_exists($className)) {
				throw new WindException('create application failed. the class ' . $className . ' is not exist.');
			}
			$this->application = new $className();
		}
		return $this->application;
	}

}