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
class WindFrontController implements IWindFrontController {
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
	 * @return WindWebApplication
	 */
	public function getApplication($config) {
		$factory = new WindFactory(@include (Wind::getRealPath('WIND:components_config')));
		$config = new WindSystemConfig($config, Wind::getAppName(), $factory);
		if ($components = $config->getComponents()) $factory->loadClassDefinitions($components);
		$application = $this->windFactory->getInstance($config->getAppClass('windWebApp'));
		if ($application === null) {
			throw new WindException(
				'[core.web.WindFrontController.getApplication] ' . $config->getAppClass(
					'windWebApp'), WindException::ERROR_CLASS_NOT_EXIST);
		}
		return $application;
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
		if (!($filters = $this->windSystemConfig->getFilters())) return null;
		if (!($filterChainPath = $this->windSystemConfig->getFilterClass())) return null;
		return $this->getWindFactory()->createInstance($filterChainPath, array($filters));
	}

	/**
	 * 预处理Process方法
	 */
	protected function beforeProcess() {
		try {
			ob_start();
			$configPath = Wind::getRealPath(self::WIND_COMPONENT_CONFIG_RESOURCE);
			$this->windFactory = new WindFactory(@include ($configPath));
			$this->windSystemConfig = new WindSystemConfig($this->config, Wind::getAppName(), 
				$this->windFactory);
		} catch (Exception $e) {
			Wind::log('System failed to initialize. (' . $e->getMessage() . ')', 
				WindLogger::LEVEL_INFO, 'wind.core');
			throw new WindException('System failed to initialize.' . $e->getMessage());
		}
	}

	/**
	 * 后处理Process方法
	 */
	protected function afterProcess() {
		ob_end_flush();
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