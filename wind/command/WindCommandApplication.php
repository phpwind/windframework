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
class WindCommandApplication extends WindModule implements IWindApplication{
	
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
		try {
			$module = $this->getModules();
			$module = $this->setModules($this->handlerAdapter->getModule(), $module, true);
			if (!$module) {
				throw new WindActionException(
					'[command.WindCommandApplication.run] Your requested \'' . $this->handlerAdapter->getModule() . '\' was not found on this server.', 
					404);
			}
			
			$handlerPath = $module['controller-path'] . '.' . ucfirst($this->handlerAdapter->getController()) . $module['controller-suffix'];
			if (WIND_DEBUG & 2) {
				Wind::getApp()->getComponent('windLogger')->info(
					'[command.WindCommandApplication.run] \r\n\taction handler:' . $handlerPath, 'wind.command');
			}
			$this->windFactory->addClassDefinitions($handlerPath, 
				array(
					'path' => $handlerPath, 
					'scope' => 'prototype', 
					'properties' => array(
						'errorMessage' => array('ref' => 'errorMessage'))));
			$handler = $this->windFactory->getInstance($handlerPath);
			if (!$handler) {
				throw new WindActionException(
					'[command.WindCommandApplication.run] Your requested \'' . $handlerPath . '\' was not found on this server.', 
					404);
			}
			$output = $handler->doAction($this->handlerAdapter);
			$this->_getWindView($output)->render();
		}  catch (WindActionException $e) {
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
		if ($name === '') return $this->getConfig('modules', $this->_getHandlerAdapter()->getModule());
		return $this->getConfig('modules', $name, array());
	}

	/**
	 * 获得组件对象
	 * 
	 * @param string $componentName 组件名称呢个
	 * @return object
	 */
	public function getComponent($componentName) {
		return $this->windFactory->getInstance($componentName);
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
				array(
					'controller-path' => $_errorHandler, 
					'controller-suffix' => '', 
					'error-handler' => ''));
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