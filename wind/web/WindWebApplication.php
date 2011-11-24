<?php
Wind::import('WIND:http.request.WindHttpRequest');
Wind::import('WIND:http.response.WindHttpResponse');
/**
 * 应用控制器,协调处理用户请求,处理,跳转分发等工作
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
class WindWebApplication extends WindModule implements IWindApplication {
	/**
	 * @var WindHttpRequest
	 */
	protected $request;
	/**
	 * @var WindHttpResponse
	 */
	protected $response;
	/**
	 * @var WindFactory
	 */
	protected $windFactory = null;
	/**
	 * @var WindDispatcher
	 */
	protected $dispatcher = null;
	protected $token = '';
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
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WindFactory $factory
	 */
	public function __construct($request, $response, $factory) {
		$this->request = $request;
		$this->response = $response;
		$this->windFactory = $factory;
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::doDispatch()
	 */
	public function doDispatch($forward, $display = false) {
		if ($forward === null) return;
		$this->_getDispatcher()->dispatch($forward, $this->handlerAdapter, $display);
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::run()
	 */
	public function run($__state = false) {
		try {
			$this->checkProcess();
			$module = $this->getModules();
			$module = $this->setModules($this->handlerAdapter->getModule(), $module, true);
			if (!$module) {
				throw new WindActionException(
					'[web.WindWebApplication.run] Your requested \'' . $this->handlerAdapter->getModule() . '\' was not found on this server.', 
					404);
			}
			
			$handlerPath = $module['controller-path'] . '.' . ucfirst($this->handlerAdapter->getController()) . $module['controller-suffix'];
			if (WIND_DEBUG & 2) {
				Wind::getApp()->getComponent('windLogger')->info(
					'[web.WindWebApplication.run] \r\n\taction handl:' . $handlerPath, 'wind.core');
			}
			$this->windFactory->addClassDefinitions($handlerPath, 
				array(
					'path' => $handlerPath, 
					'scope' => 'prototype', 
					'config' => $this->getConfig('actionmap'), 
					'properties' => array(
						'errorMessage' => array('ref' => 'errorMessage'), 
						'forward' => array('ref' => 'forward'), 
						'urlHelper' => array('ref' => 'urlHelper'))));
			$handler = $this->windFactory->getInstance($handlerPath);
			if (!$handler) {
				throw new WindActionException(
					'[web.WindWebApplication.run] Your requested \'' . $handlerPath . '\' was not found on this server.', 
					404);
			}
			if ($__state === true && $filters = $this->getConfig('filters')) {
				$this->resolveActionMapping($filters, $handler);
				$this->_proxy->runProcess($handler);
			} else
				$this->runProcess($handler);
		} catch (WindForwardException $e) {
			$this->doDispatch($e->getForward());
		} catch (WindActionException $e) {
			$this->sendErrorMessage($e);
		} catch (WindException $e) {
			$this->sendErrorMessage($e);
		}
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		if ($components = $this->getConfig('components')) {
			if (isset($components['resource'])) {
				$components = $this->windFactory->getInstance('configParser')->parse(
					Wind::getRealPath($components['resource'], true, true));
			}
			unset($components['router']);
			$this->windFactory->loadClassDefinitions($components);
		}
		
		if ($default = $this->getModules('default')) {
			$this->defaultModule = WindUtility::mergeArray($this->defaultModule, $default);
		}
		$charset = $this->getConfig('charset', '', 'utf-8');
		$this->getResponse()->setHeader('Content-type', 'text/html;charset=' . $charset);
		$this->getResponse()->setCharset($charset);
	}

	/**
	 * 执行请求的进程
	 * 
	 * @param IWindController $handler
	 * @return void
	 * @throws WindFinalException
	 */
	public function runProcess($handler) {
		if (!$handler instanceof IWindController) throw new WindFinalException();
		$this->doDispatch($handler->doAction($this->handlerAdapter));
	}

	/**
	 * 设置全局变量
	 * 
	 * @param array|object|string $data
	 * @param string $key
	 * @return void
	 */
	public function setGlobal($data, $key = '') {
		$_G = $this->getGlobal();
		$_G = is_array($_G) ? $_G : array();
		if ($key)
			$_G[$key] = $data;
		else {
			if (is_object($data)) $data = get_object_vars($data);
			if (is_array($data)) $_G = array_merge($_G, $data);
		}
		$this->response->setData($_G, 'G');
	}

	/**
	 * 获取全局变量
	 * 
	 * @return mixed
	 */
	public function getGlobal() {
		$_args = func_get_args();
		$args = array_merge(array('G'), $_args);
		return call_user_func_array(array($this->response, 'getData'), $args);
	}

	/**
	 * 添加module配置
	 * <code>
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
	 * <template-ext>htm</template-ext></code>
	 * 
	 * @param string $name module名称
	 * @param array $config 配置数组
	 * @param boolean $replace 如果module已经存在是否覆盖他 默认值为false不进行覆盖
	 * @return array
	 */
	public function setModules($name, $config, $replace = false) {
		if ($replace || !isset($this->_config['modules'][$name])) {
			$this->_config['modules'][$name] = WindUtility::mergeArray($this->defaultModule, (array) $config);
		}
		return $this->_config['modules'][$name];
	}

	/**
	 * 获得module配置,$name为空时返回当前module配置
	 * 
	 * @param string $name module名称 默认为空
	 * @return array
	 * @throws WindActionException
	 * @throws WindException
	 */
	public function getModules($name = '') {
		if ($name === '') return $this->getConfig('modules', $this->handlerAdapter->getModule());
		return $this->getConfig('modules', $name, array());
	}

	/**
	 * 获得组件对象
	 * 
	 * @param string $componentName 组件名称呢个
	 * @return object
	 */
	public function getComponent($componentName) {
		$component = null;
		switch ($componentName) {
			case 'windCache':
				if ($this->getConfig('iscache', '', false)) {
					$component = $this->windFactory->getInstance($componentName);
				}
				break;
			default:
				$component = $this->windFactory->getInstance($componentName);
				break;
		}
		return $component;
	}

	/**
	 * 解析action过滤链的配置信息
	 * 
	 * @param array $filters
	 * @param WindSimpleController $handler
	 * @return void
	 */
	protected function resolveActionMapping($filters, $handler) {
		$this->_proxy || $this->_proxy = new WindClassProxy($this);
		/* @var $cache AbstractWindCache */
		$_filters = array();
		if ($cache = $this->getComponent('windCache')) {
			$key = md5(serialize($filters));
			$_filters = $cache->get($key);
		}
		$_token = $this->handlerAdapter->getModule() . '/' . $this->handlerAdapter->getController() . '/' . $this->handlerAdapter->getAction();
		if (!isset($_filters[$_token])) {
			foreach ($filters as $_filter) {
				if (!isset($_filter['class'])) continue;
				$_pattern = empty($_filter['pattern']) ? '' : $_filter['pattern'];
				unset($_filter['pattern']);
				if ($_pattern) {
					$_pattern = str_replace(array('*', '/'), array('\w*', '\/'), $_pattern);
					if (in_array($_pattern[0], array('~', '!'))) {
						$_pattern = substr($_pattern, 1);
						if (preg_match('/^' . $_pattern . '$/i', $_token)) continue;
					} else {
						if (!preg_match('/^' . $_pattern . '$/i', $_token)) continue;
					}
				}
				$_filters[$_token][] = $_filter;
			}
			$cache && $cache->set($key, $_filters);
		}
		if (empty($_filters[$_token])) return;
		$args = array($handler->getForward(), $handler->getErrorMessage());
		foreach ($_filters[$_token] as $value) {
			$this->_proxy->registerEventListener('runProcess', 
				$this->windFactory->createInstance(Wind::import($value['class']), 
					array($args[0], $args[1], $this->handlerAdapter, $value)));
		}
	}

	/**
	 * 处理错误请求
	 * 
	 * 根据错误请求的相关信息,将程序转向到错误处理句柄进行错误处理
	 * @param WindActionException actionException
	 * @return void
	 * @throws WindFinalException
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
		$forward->forwardAction($_errorAction, array(), false, false);
		$forward->setVars($errorMessage->getError(), 'error');
		$forward->setVars($exception->getCode(), 'errorCode');
		$this->_getDispatcher()->dispatch($forward, $this->handlerAdapter, false);
	}

	/**
	 * 检查请求的合法性
	 * 
	 * 检查请求的合法性,当判断请求不合法时,抛出一个终止异常并终止当前进程
	 * @return void
	 * @throws WindFinalException
	 */
	protected function checkProcess() {
		$token = $this->_getHandlerAdapter()->getModule() . '/' . $this->handlerAdapter->getController() . '/' . $this->handlerAdapter->getAction();
		if (strcasecmp($token, $this->token) === 0) {
			throw new WindFinalException('[WindWebApplication.checkProcess] dulplicat request \'' . $token . '\'', 
				WindException::ERROR_SYSTEM_ERROR);
		}
		$this->token = $token;
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

	/**
	 * @return WindFactory
	 */
	public function getWindFactory() {
		return $this->windFactory;
	}
}