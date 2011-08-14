<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebApplication extends WindModule implements IWindApplication {
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
	 * @var WindDispatcher
	 */
	protected $dispatcher = null;
	/**
	 * @var WindUrlBasedRouter
	 */
	protected $handlerAdapter = null;

	/**
	 * @param WindSystemConfig $config
	 * @param WindFactory $factory
	 */
	public function __construct($config, $factory) {
		$this->windSystemConfig = $config;
		$this->windFactory = $factory;
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::run()
	 */
	public function run() {
		ob_start();
		$this->request = new WindHttpRequest();
		$this->response = new WindHttpResponse();
		$this->_getHandlerAdapter()->route();
		if (null !== ($filterChain = $this->getFilterChain())) {
			$filterChain->setCallBack(array($this, 'processRequest'));
			$filterChain->getHandler()->handle($this->request, $this->response);
		} else
			$this->processRequest();
		ob_end_flush();
		$this->response->sendResponse();
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::doDispatch()
	 */
	public function doDispatch($forward) {
		if ($forward === null) {
			Wind::log('[core.web.WindWebApplication.doDispatch] Forward is null, dispatch abort.', 
				WindLogger::LEVEL_DEBUG, 'wind.core');
			return;
		}
		$this->_getDispatcher()->dispatch($this, $forward, $this->handlerAdapter);
	}

	/**
	 * @return
	 */
	protected function processRequest() {
		try {
			$moduleName = $this->handlerAdapter->getModule();
			if (!$moduleName)
				$moduleName = 'default';
			if ($moduleName === 'default' && !$this->windSystemConfig->getModules($moduleName))
				$this->windSystemConfig->setModules($moduleName);
			$handlerPath = $this->windSystemConfig->getModuleControllerPath($moduleName) . '.' . ucfirst(
				$this->handlerAdapter->getController()) . $this->windSystemConfig->getModuleControllerSuffix(
				$moduleName);
			$handlerPath = trim($handlerPath, '.');
			if (!$handlerPath)
				throw new WindFinalException(
					'[core.web.WindWebApplication.processRequest] handler path \'' . $handlerPath . '\' is not exist.');
			
			if (strpos($handlerPath, ':') === false)
				$handlerPath = Wind::getAppName() . ':' . $handlerPath;
			if (!$this->getSystemFactory()->checkAlias($handlerPath)) {
				$this->getSystemFactory()->addClassDefinitions($handlerPath, 
					array('path' => $handlerPath, 'scope' => 'singleton', 'proxy' => true, 
						'properties' => array('errorMessage' => array('ref' => 'errorMessage'), 
							'forward' => array('ref' => 'forward'), 
							'urlHelper' => array('ref' => 'urlHelper'))));
			}
			$handler = $this->windFactory->getInstance($handlerPath);
			if ($handler === null)
				throw new WindFinalException(
					'[core.web.WindWebApplication.processRequest] action handler \'' . $handlerPath . '\' is not exist.');
			call_user_func_array(array($handler, 'preAction'), array($this->handlerAdapter));
			$forward = call_user_func_array(array($handler, 'doAction'), 
				array($this->handlerAdapter));
			call_user_func_array(array($handler, 'postAction'), array($this->handlerAdapter));
			
			$this->doDispatch($forward);
		} catch (WindActionException $actionException) {
			$this->sendErrorMessage($actionException);
		}
	}

	/**
	 * 异常处理请求
	 * 
	 * @param WindActionException|string actionException
	 * @return
	 */
	protected function sendErrorMessage($actionException) {
		$_tmp = is_object($actionException) ? $actionException->getError() : $actionException;
		if (is_string($_tmp))
			$_tmp = new WindErrorMessage($_tmp);
		$forward = $this->getSystemFactory()->getInstance(COMPONENT_FORWARD);
		$forward->forwardAnotherAction($_tmp->getErrorAction(), $_tmp->getErrorController());
		$this->getRequest()->setAttribute($_tmp->getError(), 'error');
		$this->doDispatch($forward);
	}

	/**
	 * @return WindFilterChain
	 */
	protected function getFilterChain() {
		if (!($filters = $this->getWindSystemConfig()->getFilters()))
			return null;
		if (!($filterChainPath = $this->getWindSystemConfig()->getFilterClass()))
			return null;
		return $this->getWindFactory()->createInstance($filterChainPath, array($filters));
	}

	/**
	 * @return the $request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @return the $response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @return the $windSystemConfig
	 */
	public function getWindSystemConfig() {
		return $this->windSystemConfig;
	}

	/**
	 * @return the $windFactory
	 */
	public function getWindFactory() {
		return $this->windFactory;
	}
}