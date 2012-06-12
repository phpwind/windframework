<?php
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
	public function __construct($request, $factory) {
		$this->response = $request->getResponse();
		$this->request = $request;
		$this->windFactory = $factory;
	}

	/**
	 * 请求处理完毕后，进一步分发
	 *
	 * @param WindForward $forward
	 * @param boolean $display
	 */
	public function doDispatch($forward, $display = false) {
		if ($forward === null) return;
		$this->_getDispatcher()->dispatch($forward, $this->handlerAdapter, $display);
	}
	
	/* (non-PHPdoc)
	 * @see IWindApplication::run()
	 */
	public function run($filters = false) {
		$module = $this->getModules($this->_getHandlerAdapter()->getModule());
		$handlerPath = $module['controller-path'] . '.' . ucfirst($this->handlerAdapter->getController()) . $module['controller-suffix'];
		$className = Wind::import($handlerPath);
		if (!class_exists($className)) throw new WindException(
			'Your requested \'' . $handlerPath . '\' was not found on this server.', 404);
		$handler = new $className();
		$handler->setDelayAttributes(
			array('errorMessage' => array('ref' => 'errorMessage'), 'forward' => array('ref' => 'forward')));
		$filters && $this->resolveActionFilters($handler);
		
		try {
			$forward = $handler->doAction($this->handlerAdapter);
			$this->doDispatch($forward);
		} catch (WindForwardException $e) {
			$this->doDispatch($e->getForward());
		} catch (WindActionException $e) {
			$this->sendErrorMessage(($e->getError() ? $e->getError() : $e->getMessage()), $e->getCode());
		} catch (WindException $e) {
			$this->sendErrorMessage($e->getMessage(), $e->getCode());
		}
	}
	
	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		if ($default = $this->getConfig('modules', 'default', array())) {
			$this->defaultModule = WindUtility::mergeArray($this->defaultModule, $default);
		}
		$charset = $this->getConfig('charset', '', 'utf-8');
		$this->getResponse()->setHeader('Content-type', 'text/html;charset=' . $charset);
		$this->getResponse()->setCharset($charset);
	}

	/**
	 * 设置全局变量
	 *
	 * @param array|object|string $data        	
	 * @param string $key        	
	 * @return void
	 */
	public function setGlobal($data, $key = '') {
		if ($key)
			$_G[$key] = $data;
		else {
			if (is_object($data)) $data = get_object_vars($data);
			$_G = $data;
		}
		$this->response->setData($_G, 'G', true);
	}

	/**
	 * 获取全局变量
	 *
	 * @return mixed
	 */
	public function getGlobal() {
		$_args = func_get_args();
		array_unshift($_args, 'G');
		return call_user_func_array(array($this->response, 'getData'), $_args);
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
			$this->_config['modules'][$name] = (array) $config;
		}
		return $this->_config['modules'][$name];
	}

	/**
	 * 获得module配置,$name为空时返回当前module配置
	 *
	 * @param string $name module名称 默认为空
	 * @param boolean $merge 合并默认值
	 * @return array
	 * @throws WindActionException
	 * @throws WindException
	 */
	public function getModules($name = '') {
		if ($name === '') $name = $this->handlerAdapter->getModule();
		$_module = $this->getConfig('modules', $name, array());
		if (!isset($_module['_verified']) || $_module['_verified'] !== true) {
			if (empty($_module)) {
				$_module = $this->getConfig('modules', 'pattern', array());
				$_pattern = !empty($_module);
			}
			$_module = WindUtility::mergeArray($this->defaultModule, $_module);
			if (isset($_pattern) && $_pattern) {
				$_keys = array_keys($_module);
				$_replace = array(
					'{' . $this->handlerAdapter->getModuleKey() . '}' => $this->handlerAdapter->getModule(), 
					'{' . $this->handlerAdapter->getControllerKey() . '}' => $this->handlerAdapter->getController(), 
					'{' . $this->handlerAdapter->getActionKey() . '}' => $this->handlerAdapter->getAction());
				foreach ($_keys as $_key) {
					if (strrchr($_key, '-') !== '-path') continue;
					$_module[$_key] = strtr($_module[$_key], $_replace);
				}
			}
			$_module['_verified'] = true;
			$this->_config['modules'][$name] = $_module;
		}
		return $_module;
	}

	/**
	 * 获得组件对象
	 *
	 * @param string $componentName 组件名称呢个
	 * @return object
	 */
	public function getComponent($componentName, $args = array()) {
		return $this->windFactory->getInstance($componentName, $args);
	}

	/**
	 * 手动注册actionFilter
	 *
	 * 参数为数组格式：
	 * @param array $filters
	 */
	public function registeActionFilter($filters) {
		if (!$filters) return;
		if (empty($this->_config['filters']))
			$this->_config['filters'] = $filters;
		else
			$this->_config['filters'] += $filters;
	}

	/**
	 * 解析action过滤链的配置信息
	 *
	 * @param WindSimpleController $handler        	
	 * @return void
	 */
	protected function resolveActionFilters(&$handler) {
		if (!$filters = $this->getConfig('filters')) return;
		/* @var $cache AbstractWindCache */
		$_filters = array();
		if ($cache = $this->getComponent('windCache')) {
			$_filters = $cache->get('filters');
		}
		$_token = $this->handlerAdapter->getModule() . '/' . $this->handlerAdapter->getController() . '/' . $this->handlerAdapter->getAction();
		if (!isset($_filters[$_token])) {
			foreach ($filters as $_filter) {
				if (empty($_filter['class'])) continue;
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
			$cache && $cache->set('filters', $_filters);
		}
		if (empty($_filters[$_token])) return;
		/* @var $proxy WindClassProxy */
		$proxy = WindFactory::createInstance(Wind::import('WIND:factory.WindClassProxy'));
		$proxy->registerTargetObject($handler);
		foreach ($_filters[$_token] as $value) {
			$proxy->registerEventListener('doAction', 
				$this->windFactory->createInstance(Wind::import($value['class']), 
					array($handler->getForward(), $handler->getErrorMessage(), $this->handlerAdapter, $value)));
		}
		$handler = $proxy;
	}

	/**
	 * 处理错误请求
	 *
	 * 根据错误请求的相关信息,将程序转向到错误处理句柄进行错误处理
	 * @param WindErrorMessage $errorMessage
	 * @param int $errorcode
	 * @return void
	 */
	protected function sendErrorMessage($errorMessage, $errorcode) {
		if (is_string($errorMessage)) {
			$_tmp = $errorMessage;
			/* @var $errorMessage WindErrorMessage */
			$errorMessage = $this->getComponent('errorMessage');
			$errorMessage->addError($_tmp);
		}
		/* @var $router WindRouter */
		$moduleName = $this->handlerAdapter->getModule();
		if ($moduleName === 'error') throw new WindFinalException($errorMessage->getError(0));
		
		if (!$_errorAction = $errorMessage->getErrorAction()) {
			$module = $this->getModules($moduleName);
			$_errorClass = Wind::import(@$module['error-handler']);
			$_errorAction = 'error/' . $_errorClass . '/run/';
			$this->setModules('error', 
				array(
					'controller-path' => array_search($_errorClass, Wind::$_imports), 
					'controller-suffix' => '', 
					'error-handler' => ''));
		}
		/* @var $forward WindForward */
		$forward = $this->getComponent('forward');
		$error = array('message' => $errorMessage->getError(), 'code' => $errorcode);
		$forward->forwardAction($_errorAction, array('__error' => $error), false, false);
		$this->doDispatch($forward);
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