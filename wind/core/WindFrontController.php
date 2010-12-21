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
	private $application = null;
	private $systemConfig = null;
	private $applicationType = 'web';
	
	public function __construct($currentName = '', $config = array()) {
		parent::__construct();
		$this->setSystemConfig($currentName, $config);
	}
	
	public function process() {
		if ($this->excuteFilterChain()) return;
		$this->application->init($this->response->getDispatcher());
		$this->application->processRequest($this->request, $this->response);
	}
	
	/**
	 * 初始化Dispatcher
	 */
	protected function initDispatcher() {
		$router = WindRouterFactory::getFactory()->create($this->request, $this->response);
		$router->doParser($this->request, $this->response);
		$dispatcher = new WindWebDispatcher($this->request, $this->response, $this);
		$this->response->setDispatcher($dispatcher->initWithRouter($router));
	}
	
	/**
	 * 初始化过滤器，并将程序执行句柄指向一个过滤器入口
	 */
	private function excuteFilterChain() {
		$filters = $this->systemConfig->getConfig(IWindConfig::FILTERS);
		if (empty($filters)) return false;
		WindFilterFactory::getFactory()->setExecute(array($this, 'process'));
		$filter = WindFilterFactory::getFactory()->create($this->systemConfig->getFilters());
		if ($filter instanceof WindFilter) {
			$filter->doFilter($this->request, $this->response);
			return false;
		}
		return true;
	}
	
	/**
	 * 初始化应用配置
	 * @param string $appName
	 * @param string $config
	 */
	private function setSystemConfig($appName, $config) {
		if (!is_array($config)) {
			L::import('WIND:component.config.WindConfigParser');
			$configParser = new WindConfigParser();
			$config = $configParser->parse($appName, $config, true);
		}
		$this->systemConfig = new WindSystemConfig($config);
		$this->response->setData($this->systemConfig, 'WindSystemConfig');
		L::register($this->systemConfig->getRootPath(), $appName);
	}
	
	/**
	 * @return WindWebApplication
	 */
	protected function createApplication() {
		if ($this->application !== null) return;
		$application = $this->systemConfig->getApplications($this->applicationType);
		$className = L::import($application[IWindConfig::APPLICATIONS_CLASS]);
		if (!class_exists($className)) {
			throw new WindException('create application failed. the class ' . $className . ' is not exist.');
		}
		$this->application = new $className();
	}
	
	protected function beforeProcess() {
		$this->initDispatcher();
		$this->createApplication();
	}
	
	protected function afterProcess() {
		$this->application->destory();
		restore_exception_handler();
	}
	
	protected function doPost($request, $response) {
		$this->process($request, $response);
	}
	
	protected function doGet($request, $response) {
		$this->process($request, $response);
	}

}