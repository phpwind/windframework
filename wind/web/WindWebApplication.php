<?php
Wind::import('WIND:http.request.WindHttpRequest');
Wind::import('WIND:http.response.WindHttpResponse');
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
	protected $defaultModule = array(
		'controller-path' => 'controller', 
		'controller-suffix' => 'Controller', 
		'error-handler' => 'WIND:web.WindErrorHandler');

	/**
	 * 应用初始化操作
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
		$this->setModules('default', $this->defaultModule);
		$this->_getHandlerAdapter()->route();
		$this->processRequest();
		restore_error_handler();
		restore_exception_handler();
		$this->response->sendResponse();
		$this->windFactory->executeDestroyMethod();
		Wind::resetApp();
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::doDispatch()
	 */
	public function doDispatch($forward, $display = false) {
		if ($forward === null) return;
		$this->_getDispatcher()->dispatch($forward, $this->handlerAdapter, $display);
	}

	/**
	 * 请求处理
	 * @return
	 */
	public function processRequest() {
		try {
			if (!$this->handlerAdapter->getModule()) $this->handlerAdapter->setModule('default');
			if (!($module = $this->getModules())) throw new WindActionException(
				'[web.WindWebApplication.processRequest] Your requested \'' . $this->handlerAdapter->getModule() .
					 '\' was not found on this server.', 404);
			$module = WindUtility::mergeArray($this->getModules('default'), $module);
			$this->setModules($this->handlerAdapter->getModule(), $module, true);
			$handlerPath = $module['controller-path'] . '.' . ucfirst($this->handlerAdapter->getController()) .
				 $module['controller-suffix'];
				if (WIND_DEBUG & 2) Wind::getApp()->getComponent('windLogger')->info(
					'[web.WindWebApplication.processRequest] \r\n\taction handl:' . $handlerPath, 'wind.core');
				
				$this->windFactory->addClassDefinitions($handlerPath, 
					array(
						'path' => $handlerPath, 
						'scope' => 'prototype', 
						'proxy' => true, 
						'config' => $this->getConfig('actionmap'), 
						'properties' => array(
							'errorMessage' => array('ref' => 'errorMessage'), 
							'forward' => array('ref' => 'forward'), 
							'urlHelper' => array('ref' => 'urlHelper'))));
				$handler = $this->windFactory->getInstance($handlerPath);
				if (!$handler) throw new WindActionException(
					'[web.WindWebApplication.processRequest] Your requested \'' . $handlerPath .
						 '\' was not found on this server.', 404);
				if ($filters = $this->getConfig('filters')) $this->resolveActionMapping($filters, $handler);
				$this->doDispatch($handler->doAction($this->handlerAdapter));
			} catch (WindActionException $e) {
				$this->sendErrorMessage($e);
			} catch (WindException $e) {
				$this->sendErrorMessage($e);
			}
		}

		/**
		 * 设置全局变量
		 * @param array|object|string $data
		 * @param string $key
		 * @return 
		 */
		public function setGlobal($data, $key = '') {
			$_G = $this->getGlobal();
			if ($key)
				$_G[$key] = $data;
			else {
				if (is_object($data)) $data = get_object_vars($data);
				if (is_array($data)) $_G += $data;
			}
			$this->response->setData($_G, 'G');
		}

		/**
		 * 获取全局变量
		 * @return string|object|array
		 */
		public function getGlobal() {
			$args = array('G') + func_get_args();
			return call_user_func_array(array($this->response, 'getData'), $args);
		}

		/**
		 * 添加module
		 * <controller-path>controller</controller-path>
		 * <!-- 指定该模块下的controller的后缀格式 -->
		 * <controller-suffix>Controller</controller-suffix>
		 * <!-- 配置该模块的error处理的action controller类 -->
		 * <error-handler>WIND:web.WindErrorHandler</error-handler>
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
		public function setModules($name, $config = array(), $replace = false) {
			if ($replace || !isset($this->_config['modules'][$name])) $this->_config['modules'][$name] = (array) $config;
		}

		/**
		 * @param string $name
		 * @throws WindActionException
		 * @throws WindException
		 * @return array
		 */
		public function getModules($name = '') {
			if ($name === '') return $this->getConfig('modules', $this->handlerAdapter->getModule());
			return $this->getConfig('modules', $name, array());
		}

		/**
		 * @param object $componentInstance
		 * @param string $componentName
		 */
		public function registeComponent($componentName, $componentInstance, $scope) {
			return $this->windFactory->registInstance($componentInstance, $componentName, $scope);
		}

		/**
		 * @param string $componentName
		 * @return object
		 */
		public function getComponent($componentName) {
			$component = null;
			switch ($componentName) {
				case 'windCache':
					if ($this->getConfig('iscache', '', true)) $component = $this->windFactory->getInstance(
						$componentName);
					break;
				default:
					$component = $this->windFactory->getInstance($componentName);
					break;
			}
			return $component;
		}

		/**
		 * @param array $filters
		 * @param WindClassProxy $handler
		 * @return
		 */
		protected function resolveActionMapping($filters, $handler) {
			/* @var $cache AbstractWindCache */
			$_filters = array();
			if ($cache = $this->getComponent('windCache')) {
				$key = md5(serialize($filters));
				$_filters = $cache->get($key);
			}
			$_token = $this->handlerAdapter->getModule() . '_' . $this->handlerAdapter->getController() . '_' .
				 $this->handlerAdapter->getAction();
				if (!isset($_filters[$_token])) {
					foreach ($filters as $_filter) {
						if (isset($_filter['class'])) if (!$_filter['pattern'] || preg_match(
							'/^' . str_replace('*', '\w*', $_filter['pattern']) . '$/i', $_token)) {
							$_filters[$_token][] = $_filter['class'];
						}
					}
					$cache && $cache->set($key, $_filters);
				}
				if (empty($_filters[$_token])) {
					return;
				}
				foreach ($_filters[$_token] as $key => $value) {
					$handler->registerEventListener('doAction', 
						$this->windFactory->createInstance(Wind::import($value)));
				}
			}

			/**
			 * 异常处理请求
			 * @param WindActionException actionException
			 * @return
			 */
			protected function sendErrorMessage($exception) {
				$moduleName = $this->handlerAdapter->getModule();
				if ($moduleName === 'error') throw new WindFinalException($exception->getMessage());
				
				$errorMessage = null;
				if ($exception instanceof WindActionException) $errorMessage = $exception->getError();
				if (!$errorMessage) {
					$errorMessage = $this->windFactory->getInstance('errorMessage');
					$errorMessage->addError($exception->getMessage());
				}
				if (!$_errorAction = $errorMessage->getErrorAction()) {
					$module = $this->getModules($moduleName);
					if (empty($module)) $module = $this->getModules('default');
					preg_match("/([a-zA-Z]*)$/", @$module['error-handler'], $matchs);
					$_errorHandler = trim(substr(@$module['error-handler'], 0, -(strlen(@$matchs[0]) + 1)));
					$_errorAction = 'error/' . @$matchs[0] . '/run/';
					$this->setModules('error', 
						array('controller-path' => $_errorHandler, 'controller-suffix' => '', 'error-handler' => ''));
				}
				/* @var $forward WindForward */
				$forward = $this->getSystemFactory()->getInstance('forward');
				$forward->forwardAction($_errorAction);
				$forward->setVars($errorMessage->getError(), 'error');
				$forward->setVars($exception->getCode(), 'errorCode');
				$this->_getDispatcher()->dispatch($forward, $this->handlerAdapter, false);
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