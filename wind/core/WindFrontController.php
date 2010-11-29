<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

L::import('WIND:core.base.WindServlet');
L::import('WIND:component.exception.WindException');
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
	private $applications = array();
	private static $instance = null;
	
	protected function __construct() {
		parent::__construct();
		$this->initConfig();
	}
	
	public function run() {
		$this->beforProcess();
		$filters = C::getConfig(IWindConfig::FILTERS);
		if (!empty($filters)) {
			$this->initFilter();
		} else
			parent::run();
		$this->afterProcess();
	}
	
	protected function beforProcess() {

	}
	
	public function process($request, $response) {
		$this->initDispatch($request, $response);
		$applicationController = $this->getApplicationHandle();
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
	private function initFilter() {
		L::import('WIND:component.filter.WindFilterFactory');
		WindFilterFactory::getFactory()->setExecute(array(get_class($this), 'process'), $this->request, $this->response);
		$filter = WindFilterFactory::getFactory()->create();
		if (is_object($filter)) $filter->doFilter($this->request, $this->response);
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
	 * 初始化页面分发器
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	protected function initDispatch($request, $response) {
		if ($response->getDispatcher() && $response->getDispatcher()->getAction()) return;
		$router = WindRouterFactory::getFactory()->create();
		$router->doParser($request, $response);
		$response->setDispatcher(WindDispatcher::getInstance()->setRouter($router));
	}
	
	/**
	 * @param string $key
	 * @return WindWebApplication
	 */
	public function &getApplicationHandle($key = 'default') {
		if (!isset($this->applications[$key])) {
			$application = C::getApplications($key);
			list(, $className, , $realpath) = L::getRealPath($application[IWindConfig::APPLICATIONS_CLASS], true);
			L::import($realpath);
			$this->applications[$key] = &new $className();
		}
		return $this->applications[$key];
	}
	
	/**
	 * @return WindFrontController
	 */
	static public function &getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
}