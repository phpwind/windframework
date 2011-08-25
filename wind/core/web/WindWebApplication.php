<?php
Wind::import('COM:http.request.WindHttpRequest');
Wind::import('COM:http.response.WindHttpResponse');
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
	protected $filterChain = 'WIND:filter.WindFilterChain';

	/**
	 * 应用初始化操作
	 * 
	 * @param array|string $config
	 * @param WindFactory $factory
	 * @param string $runCallBack
	 */
	public function __construct($config, $factory) {
		$this->request = new WindHttpRequest();
		$this->response = $this->request->getResponse(@$config['charset']);
		$this->windFactory = $factory;
		$this->setConfig($config);
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::run()
	 */
	public function run() {
		set_error_handler('WindHelper::errorHandle');
		set_exception_handler('WindHelper::exceptionHandle');
		$this->windFactory->loadClassDefinitions($this->getConfig('components'));
		$this->_getHandlerAdapter()->route();
		$this->processRequest();
		restore_error_handler();
		restore_exception_handler();
		$this->response->sendResponse();
		Wind::resetApp();
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::doDispatch()
	 */
	public function doDispatch($forward, $display = false) {
		if ($forward === null)
			return;
		$moduleName = $this->handlerAdapter->getModule();
		if (!($module = $this->getModules($moduleName)))
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
				$module = $this->setModules($moduleName);
			} else {
				if (!($module = $this->getModules($moduleName)))
					throw new WindActionException(
						'[core.web.WindWebApplication.processRequest] Your requested \'' . $moduleName . '\' was not found on this server.', 
						404);
				$module = $this->setModules($moduleName, $module);
			}
			$handlerPath = @$module['controller-path'] . '.' . ucfirst(
				$this->handlerAdapter->getController()) . @$module['controller-suffix'];
			$handlerPath = trim($handlerPath, '.');
			if (!$handlerPath)
				throw new WindActionException(
					'[core.web.WindWebApplication.processRequest] Your requested \'' . $handlerPath . '\' was not found on this server.', 
					404);
			
			strpos($handlerPath, ':') === false && $handlerPath = Wind::getAppName() . ':' . $handlerPath;
			$this->getSystemFactory()->addClassDefinitions($handlerPath, 
				array('path' => $handlerPath, 'scope' => 'singleton', 'proxy' => true, 
					'config' => $this->getConfig('actionmap'), 
					'properties' => array('errorMessage' => array('ref' => 'errorMessage'), 
						'forward' => array('ref' => 'forward'), 
						'urlHelper' => array('ref' => 'urlHelper'))));
			$handler = $this->windFactory->getInstance($handlerPath);
			if (!$handler)
				throw new WindActionException(
					'[core.web.WindWebApplication.processRequest] Your requested \'' . $handlerPath . '\' was not found on this server.', 
					404);
			$this->resolveActionChain($handler);
			$this->doDispatch($handler->doAction($this->handlerAdapter));
		} catch (WindActionException $e) {
			$this->sendErrorMessage($e);
		} catch (WindException $e) {
			$this->sendErrorMessage($e);
		}
	}

	/**
	 * @param WindClassProxy $handler
	 * @throws WindActionException
	 */
	protected function resolveActionChain($handler) {
		$_alias = $handler->_getClassName() . '.' . $this->handlerAdapter->getAction();
		if ($formClassPath = $handler->getConfig($_alias, 'form')) {
			$handler->registerEventListener('doAction', 
				new WindFormListener($this->getRequest(), $formClassPath, 
					$this->getComponent('errorMessage')));
		}
		if ($_items = $handler->getConfig($_alias, 'item')) {
			!isset($_items[0]) && $_items = array($_items);
			foreach ((array) $_items as $item) {
				if (@$item['type'] === 'interceptor' && @$item['item']) {
					/*$className = @$item['class'] ? Wind::import($item['class']) : 'WindActionInterceptorListener';
					$handler->registerEventListener('doAction', 
						new $className($this->getRequest(), $this->getResponse(), $item['item']));*/
				} elseif (@$item['type'] === 'filter' && @$item['class']) {
					$className = Wind::import($item['class']);
					$handler->registerEventListener('doAction', 
						new $className($this->getRequest(), $this->getResponse()));
				}
			}
		}
	}

	/**
	 * 异常处理请求
	 * @param WindActionException actionException
	 * @return
	 */
	protected function sendErrorMessage($exception) {
		$moduleName = $this->handlerAdapter->getModule();
		if ($moduleName === 'error')
			throw new WindFinalException($exception->getMessage());
		
		$errorMessage = null;
		if ($exception instanceof WindActionException)
			$errorMessage = $exception->getError();
		if (!$errorMessage) {
			$errorMessage = $this->windFactory->getInstance('errorMessage');
			$errorMessage->addError($exception->getMessage());
		}
		if (!$_errorAction = $errorMessage->getErrorAction()) {
			$module = $this->getModules($moduleName);
			if (empty($module))
				$module = $this->setModules('default');
			preg_match("/([a-zA-Z]*)$/", @$module['error-handler'], $matchs);
			$_errorHandler = trim(substr(@$module['error-handler'], 0, -(strlen(@$matchs[0]))));
			$_errorAction = 'error/' . @$matchs[0] . '/run/';
			$this->setModules('error', 
				array('controller-path' => $_errorHandler, 'controller-suffix' => '', 
					'error-handler' => ''));
		}
		$forward = $this->getSystemFactory()->getInstance('forward');
		$forward->forwardAction($_errorAction);
		$this->getRequest()->setAttribute($errorMessage->getError(), 'error');
		$this->getRequest()->setAttribute($exception->getCode(), 'errorCode');
		$this->_getDispatcher()->dispatch($forward, $this->handlerAdapter, false);
	}

	/**
	 * 添加module
	 * <controller-path>controller</controller-path>
	 * <!-- 指定该模块下的controller的后缀格式 -->
	 * <controller-suffix>Controller</controller-suffix>
	 * <!-- 配置该模块的error处理的action controller类 -->
	 * <error-handler>WIND:core.web.WindErrorHandler</error-handler>
	 * <!-- 试图相关配置，config中配置可以根据自己的需要进行配置或是使用缺省 -->
	 * <!-- 可以在这里进行view的配置，该配置只会影响该module下的view行为，该配置可以设置也可以不设置 -->
	 * <!-- 指定模板路径 -->
	 * <template-dir>template</template-dir>
	 * <!-- 指定模板后缀 -->
	 * <template-ext>htm</template-ext>
	 * 
	 * @param string $name
	 * @param array $config
	 * @return array
	 */
	public function setModules($name, $config = array()) {
		if (isset($this->_config['modules']['default']))
			$_default = $this->_config['modules']['default'];
		else {
			$_default = array('controller-path' => 'controller', 
				'controller-suffix' => 'Controller', 
				'error-handler' => 'WIND:core.web.WindErrorHandler');
			$this->_config['modules']['default'] = $_default;
		}
		if (!$config)
			$this->_config['modules'][$name] = $_default;
		else
			$this->_config['modules'][$name] = WindUtility::mergeArray($_default, $config);
		return $this->_config['modules'][$name];
	}

	/**
	 * @param string $name
	 * @throws WindActionException
	 * @throws WindException
	 * @return array
	 */
	public function getModules($name = '') {
		return $this->getConfig('modules', $name, array());
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		if (!$config)
			return;
		$config = @$config[Wind::getAppName()] ? $config[Wind::getAppName()] : $config;
		$this->_config = $config;
	}

	/**
	 * @param object $componentInstance
	 * @param string $componentName
	 */
	public function registeComponent($componentName, $componentInstance, $scope) {
		return $this->windFactory->registInstance($componentInstance, $componentName);
	}

	/**
	 * @param string $componentName
	 * @return object
	 */
	public function getComponent($componentName) {
		$component = null;
		switch ($componentName) {
			case 'windCache':
				if ($this->getConfig('iscache', '', true))
					$component = $this->windFactory->getInstance($componentName);
				break;
			default:
				$component = $this->windFactory->getInstance($componentName);
				break;
		}
		return $component;
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
	 * @return WindFactory $windFactory
	 */
	public function getWindFactory() {
		return $this->windFactory;
	}
}