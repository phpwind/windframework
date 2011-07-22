<?php
/**
 * 抽象的前端控制器接口，通过集成该接口可以实现以下职责
 * 职责定义：
 * 接受客户请求
 * 处理请求
 * 向客户端发送响应
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindFrontController {
	/**
	 * 全局配置对象类型定义，建议WindSystemConfig配置类作为全局配置容器
	 */
	const WIND_SYSTEM_CONFIG = 'WIND:core.config.WindSystemConfig';
	/**
	 * 全局工厂类型定义，建议WindComponentFactory工厂类作为全局工厂
	 */
	const WIND_SYSTEM_FACTORY = 'WIND:core.factory.WindFactory';
	/**
	 * 框架系统配置信息资源地址，只接受php格式配置
	 */
	const WIND_COMPONENT_CONFIG_RESOURCE = 'WIND:components_config';
	/**
	 * @var WindHttpRequest
	 */
	private $request;
	/**
	 * @var WindHttpResponse
	 */
	private $response;
	/**
	 * @var WindSystemConfig
	 */
	protected $windSystemConfig = null;
	/**
	 * @var WindFactory
	 */
	protected $windFactory = null;

	/**
	 * @param WindConfig $windConfig
	 * @param WindFactory $windFactory
	 */
	public function __construct($appName, $config = '') {
		try {
			$this->request = new WindHttpRequest();
			$this->response = $this->request->getResponse();
			$this->initWindConfig($appName, $config);
			$this->initWindFactory();
		} catch (Exception $exception) {
			throw new Exception('System failed to initialize.' . $exception->getMessage());
		}
	}

	/**
	 * 执行操作
	 * @throws Exception
	 */
	public function run() {
		$this->beforeProcess();
		$appName = $this->windSystemConfig->getAppClass();
		/* @var $application WindModule */
		$application = $this->windFactory->getInstance($appName);
		if ($application === null) {
			throw new WindException($appName . '[core.web.WindFrontController.process]', 
				WindException::ERROR_CLASS_NOT_EXIST);
		}
		$routerAlias = $this->windSystemConfig->getRouterClass();
		$application->setDelayAttributes(array('handlerAdapter' => array('ref' => $routerAlias)));
		
		if (null !== ($filterChain = $this->getFilterChain())) {
			$filterChain->setCallBack(array($application, 'processRequest'), array());
			$filterChain->getHandler()->handle($this->request, $this->response);
		} else
			$application->processRequest($this->request, $this->response);
		$this->afterProcess();
	}

	/**
	 * @return WindFilterChain
	 */
	protected function getFilterChain() {
		$filterChainPath = $this->windSystemConfig->getFilterClass();
		$filters = $this->windSystemConfig->getFilters();
		if (empty($filters) || empty($filterChainPath)) return null;
		if (IS_DEBUG && IS_DEBUG <= WindLogger::LEVEL_DEBUG) {
			Wind::log(
				"[core.WindFrontController.getFilterChain] an filter chain defined.(" . $filterChainPath . "," .
					 count($filters) . ")", WindLogger::LEVEL_DEBUG, 'wind.core');
		}
		return $this->windFactory->createInstance($filterChainPath, array($filters));
	}

	/**
	 * 初始全局工厂类
	 * @return
	 */
	protected function initWindFactory() {
		$configPath = Wind::getRealPath(self::WIND_COMPONENT_CONFIG_RESOURCE);
		$factoryClass = Wind::import(self::WIND_SYSTEM_FACTORY);
		$this->windFactory = new $factoryClass(@include ($configPath));
		if (IS_DEBUG && IS_DEBUG <= WindLogger::LEVEL_DEBUG) {
			Wind::log('[core.web.WindFrontController.initWindFactory] system factory:' . self::WIND_SYSTEM_FACTORY, 
				WindLogger::LEVEL_DEBUG, 'wind.core');
		}
	}

	/**
	 * 初始化系统配置
	 * @param string $appName
	 * @param string $config
	 */
	protected function initWindConfig($appName, $config) {
		$this->windSystemConfig = new WindSystemConfig($config, $appName);
		Wind::register($this->windSystemConfig->getRootPath(), $appName, true);
		if (IS_DEBUG && IS_DEBUG <= WindLogger::LEVEL_DEBUG) {
			Wind::log(
				'[core.web.WindFrontController.initWindConfig] rootPath:' . $this->windSystemConfig->getRootPath() .
					 ' appName:' . $this->windSystemConfig->getAppName(), WindLogger::LEVEL_DEBUG, 'wind.core');
		}
	}

	/* (non-PHPdoc)
	 * @see AbstractWindServer::beforeProcess()
	 */
	protected function beforeProcess() {}

	/* (non-PHPdoc)
	 * @see AbstractWindServer::afterProcess()
	 */
	protected function afterProcess() {
		$this->response->sendResponse();
	}

	/**
	 * @return WindsystemConfig
	 */
	public function getWindSystemConfig() {
		return $this->windSystemConfig;
	}

	/**
	 * @return WindComponentFactory
	 */
	public function getWindFactory() {
		return $this->windFactory;
	}

	/**
	 * @return WindHttpRequest
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @return WindHttpResponse
	 */
	public function getResponse() {
		return $this->response;
	}
}