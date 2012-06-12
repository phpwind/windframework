<?php
/**
 * 命令行应用控制器
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package command
 */
class WindCommandApplication extends WindModule implements IWindApplication {
	
	/**
	 * @var WindCommandView
	 */
	protected $windView = null;
	
	/**
	 * @var WindCommandRouter
	 */
	protected $handlerAdapter = null;
	/**
	 * @var WindCommandRequest
	 */
	protected $request;
	/**
	 * @var WindCommandResponse
	 */
	protected $response;
	/**
	 * @var WindFactory
	 */
	protected $windFactory = null;
	protected $defaultModule = array(
		'controller-path' => 'controller', 
		'controller-suffix' => 'Controller', 
		'error-handler' => 'WIND:command.WindCommandErrorHandler');

	/**
	 * 应用初始化操作
	 * 
	 * @param WindCommandRequest $request
	 * @param WindCommandResponse $response
	 * @param WindFactory $factory
	 */
	public function __construct($request, $factory) {
		$this->request = $request;
		$this->response = $request->getResponse();
		$this->windFactory = $factory;
	}
	
	/* (non-PHPdoc)
	 * @see IWindApplication::run()
	 */
	public function run() {
		$module = $this->getModules($this->_getHandlerAdapter()->getModule());
		$handlerPath = $module['controller-path'] . '.' . ucfirst($this->handlerAdapter->getController()) . $module['controller-suffix'];
		$className = Wind::import($handlerPath);
		if ($this->handlerAdapter->isHelp()) {
			$this->help($className);
		}
		if (!class_exists($className)) throw new WindException(
			"Your requested '$handlerPath' was not found on this server.");
		
		try {
			$handler = new $className();
			$handler->setDelayAttributes(
				array('errorMessage' => array('ref' => 'errorMessage'), 'forward' => array('ref' => 'forward')));
			$handler->doAction($this->handlerAdapter);
		} catch (WindException $e) {
			$this->help($className, $e);
		}
	}

	/**
	 * 显示帮助信息
	 * 
	 * @param string $className
	 * @param WindException $e
	 */
	protected function help($className, $e = null) {
		$helps = array();
		$helps[10] = 'usage: command [options] [args]';
		$helps[11] = 'Valid options:';
		$helps[12] = $this->handlerAdapter->getModuleKey() . ' 		routing information,the name of application module';
		$helps[13] = $this->handlerAdapter->getControllerKey() . ' 	routing information,the name of controller';
		$helps[14] = $this->handlerAdapter->getActionKey() . ' 		routing information,the name of action';
		$helps[15] = $this->handlerAdapter->getParamKey() . '		the parameters of the method [action]';
		if (class_exists($className)) {
			/*@var $handler WindCommandController */
			$handler = new $className();
			$action = $this->handlerAdapter->getAction();
			if ($action !== 'run') $action = $handler->resolvedActionName($this->handlerAdapter->getAction());
			if (!method_exists($handler, $action)) return;
			$method = new ReflectionMethod($handler, $action);
			$helps[20] = "\r\nlist -p [paraments] of '$className::$action' \r\n";
			$method = $method->getParameters();
			$i = 21;
			foreach ($method as $value) {
				$helps[$i++] = $value;
			}
		}
		if ($e !== null) $helps[0] = $e->getMessage() . "\r\n";
		exit(implode("\r\n", $helps));
	}
	
	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		if ($default = $this->getModules('default')) {
			$this->defaultModule = WindUtility::mergeArray($this->defaultModule, $default);
		}
	}

	/**
	 * 添加module配置
	 * <code>
	 * <controller-path>controller</controller-path>
	 * <!-- 指定该模块下的controller的后缀格式 -->
	 * <controller-suffix>Controller</controller-suffix>
	 * <!-- 配置该模块的error处理的action controller类 -->
	 * <error-handler>WIND:command.WindCommandErrorHandler</error-handler>
	 * </code>
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
		if ($name === '') $name = $this->handlerAdapter->getModule();
		$_module = $this->getConfig('modules', $name, array());
		if (!isset($_module['_verified'])) {
			$_module = WindUtility::mergeArray($this->defaultModule, $_module);
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
			$errorMessage = $this->getComponent('errorMessage');
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
		$this->handlerAdapter->setModule('error');
		$this->handlerAdapter->setController($matchs[0]);
		Wind::getApp()->getRequest()->setAttribute(array($errorMessage->getError(), $exception->getCode()), 'argv');
		Wind::getApp()->run();
	}

	/**
	 * @return WindFactory
	 */
	public function getWindFactory() {
		return $this->windFactory;
	}

	/**
	 * 返回WindCommandRequest
	 * 
	 * @return WindCommandRequest $request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * 返回WindCommandResponse
	 * 
	 * @return WindCommandResponse $response
	 */
	public function getResponse() {
		return $this->response;
	}
}

?>