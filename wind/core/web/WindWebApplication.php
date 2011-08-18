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
	 * @var WindRouter
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
		set_error_handler('WindHelper::errorHandle');
		set_exception_handler('WindHelper::exceptionHandle');
		$this->request = new WindHttpRequest();
		$this->response = $this->request->getResponse();
		$this->_getHandlerAdapter()->route();
		if (null == ($filterChain = $this->getFilterChain()))
			$this->processRequest();
		else {
			$filterChain->setCallBack(array($this, 'processRequest'));
			$filterChain->getHandler()->handle($this->request, $this->response);
		}
		restore_error_handler();
		restore_exception_handler();
		$this->response->sendResponse();
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::doDispatch()
	 */
	public function doDispatch($forward, $display = false) {
		if ($forward === null)
			return;
		$moduleName = $this->handlerAdapter->getModule();
		if (!($module = $this->windSystemConfig->getModules($moduleName)))
			throw new WindActionException(
				'[core.web.WindWebApplication.doDispatch] Your requested \'' . $moduleName . '\' was not found on this server.', 
				404);
			
		/* @var $forward WindForward */
		if ($forward->getTemplateExt() === null && isset($module['template-ext']))
			$forward->setTemplateExt($module['template-ext']);
		if ($forward->getTemplatePath() === null && isset($module['template-dir']))
			$forward->setTemplatePath($module['template-dir']);
		$this->_getDispatcher()->dispatch($forward, $this->handlerAdapter, $display);
	}

	/**
	 * 请求处理
	 * @return
	 */
	public function processRequest() {
		try {
			$moduleName = $this->handlerAdapter->getModule();
			if (!$moduleName) {
				$moduleName = 'default';
				$this->handlerAdapter->setModule($moduleName);
				$module = $this->windSystemConfig->setModules($moduleName);
			} else {
				if (!($module = $this->windSystemConfig->getModules($moduleName)))
					throw new WindActionException(
						'[core.web.WindWebApplication.processRequest] Your requested \'' . $moduleName . '\' was not found on this server.', 
						404);
				$module = $this->windSystemConfig->setModules($moduleName, $module);
			}
			$handlerPath = @$module['controller-path'] . '.' . ucfirst(
				$this->handlerAdapter->getController()) . @$module['controller-suffix'];
			$handlerPath = trim($handlerPath, '.');
			if (!$handlerPath)
				throw new WindActionException(
					'[core.web.WindWebApplication.processRequest] Your requested \'' . $handlerPath . '\' was not found on this server.', 
					404);
			
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
			
			if (!$handler)
				throw new WindActionException(
					'[core.web.WindWebApplication.processRequest] Your requested \'' . $handlerPath . '\' was not found on this server.', 
					404);
			call_user_func_array(array($handler, 'preAction'), array($this->handlerAdapter));
			$forward = call_user_func_array(array($handler, 'doAction'), 
				array($this->handlerAdapter));
			call_user_func_array(array($handler, 'postAction'), array($this->handlerAdapter));
			$this->doDispatch($forward);
		} catch (WindActionException $e) {
			$this->sendErrorMessage($e);
		} catch (WindViewException $e) {
			$this->sendErrorMessage($e);
		}
	}

	/**
	 * 异常处理请求
	 * @param WindActionException actionException
	 * @return
	 */
	protected function sendErrorMessage($exception) {
		$moduleName = $this->handlerAdapter->getModule();
		if ($moduleName === 'error' || !($module = $this->windSystemConfig->getModules($moduleName)))
			throw new WindException(
				'[core.web.WindWebApplication.sendErrorMessage] ' . $exception->getMessage());
		
		if ($exception instanceof WindActionException)
			$errorMessage = $exception->getError();
		if (!$errorMessage) {
			$errorMessage = $this->windFactory->getInstance('errorMessage');
			$errorMessage->addError($exception->getMessage());
		}
		if (!$_errorAction = $errorMessage->getErrorAction()) {
			preg_match("/([a-zA-Z]*)$/", @$module['error-handler'], $matchs);
			$_errorHandler = trim(substr(@$module['error-handler'], 0, -(strlen(@$matchs[0]))));
			$_errorAction = 'error/' . @$matchs[0] . '/run/';
			$this->windSystemConfig->setModules('error', 
				array('controller-path' => $_errorHandler, 'controller-suffix' => '', 
					'error-handler' => ''));
		}
		$forward = $this->getSystemFactory()->getInstance('forward');
		$forward->forwardAction($_errorAction);
		$this->getRequest()->setAttribute($errorMessage->getError(), 'error');
		$this->getRequest()->setAttribute($exception->getCode(), 'errorCode');
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
	 * @param string $componentName
	 * @param object $componentInstance
	 */
	public function registeComponent($componentName, $componentInstance) {}

	/**
	 * @param string $componentName
	 */
	public function getComponent($componentName) {
		return $this->windFactory->getInstance($componentName);
	}

	/**
	 * @return WindHttpRequest $request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @return WindHttpResponse $response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @return WindSystemConfig $windSystemConfig
	 */
	public function getWindSystemConfig() {
		return $this->windSystemConfig;
	}

	/**
	 * @return WindFactory $windFactory
	 */
	public function getWindFactory() {
		return $this->windFactory;
	}
}