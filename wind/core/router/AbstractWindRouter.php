<?php
/**
 * 路由解析器接口
 * 职责: 路由解析, 返回路由对象
 * 实现路由解析器必须实现该接口的doParser()方法
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class AbstractWindRouter extends WindModule {
	const DEFAULT_ERROR_HANDLER = 'WIND:core.web.WindErrorHandler';
	const CONTROLLER_DEFAULT_PATH = 'controller';
	const CONTROLLER_DEFAULT_SUFFIX = 'Controller';
	/**
	 * 默认的处理方法‘run’
	 * 
	 * @var string
	 */
	private $action = 'run';
	/**
	 * 默认的控制器‘index’
	 *
	 * @var string
	 */
	private $controller = 'index';
	/**
	 * 默认的系统应用模块名为‘default’
	 *
	 * @var string
	 */
	private $module = 'default';
	/**
	 * 系统应用模块寻址路径
	 * 
	 * @var string
	 */
	protected $modulePath = '';
	
	private $reParse = true;

	/**
	 * 该方法定义了路由解析策略
	 * @return string | actionHandler
	 */
	abstract public function parse();

	/**
	 * 构建Url并返回
	 * @return string
	 */
	abstract public function buildUrl();

	/**
	 * 通过调用该方法返回，解析请求参数，并返回路由结果
	 * 
	 * @return
	 */
	public function doParse() {
		if ($this->reParse) {
			$this->parse();
			$this->reParse = false;
		}
		$_moduleName = $this->getModule();
		if (!strcasecmp($this->getController(), WIND_M_ERROR)) {
			if (IS_DEBUG && IS_DEBUG <= WindLogger::LEVEL_DEBUG) {
				Wind::log(
					'[core.roter.AbstractWindRouter.doParse] action hander: default error action :' .
						 self::DEFAULT_ERROR_HANDLER, WindLogger::LEVEL_DEBUG, 'wind.core');
			}
			return $this->getSystemConfig()->getModuleErrorHandlerByModuleName($_moduleName, 
				self::DEFAULT_ERROR_HANDLER);
		}
		$_suffix = $this->getSystemConfig()->getModuleControllerSuffixByModuleName($_moduleName, 
			self::CONTROLLER_DEFAULT_SUFFIX);
		if ($this->modulePath)
			$_path = $this->modulePath;
		else {
			$_path = $this->getSystemConfig()->getModuleControllerPathByModuleName($_moduleName, 
				self::CONTROLLER_DEFAULT_PATH);
		}
		$_path .= '.' . ucfirst($this->controller) . $_suffix;
		if (IS_DEBUG && IS_DEBUG <= WindLogger::LEVEL_DEBUG) {
			Wind::log('[core.router.AbstractWindRouter.doParse] action handler: ' . $_path, WindLogger::LEVEL_DEBUG, 
				'wind.core');
		}
		$this->destroy();
		return $_path;
	}

	/**
	 * @return
	 */
	protected function destroy() {
		$this->modulePath = '';
	}

	/**
	 * 设置module信息, 支持格式：
	 * 'moduleName';
	 * 'namespace:modulePath'
	 * 
	 * @param string $module
	 * @return
	 */
	public function setModule($module) {
		if (false !== ($pos = strpos($module, ':'))) {
			$this->modulePath = $module;
		} else {
			$this->module = $module;
			$this->modulePath = '';
		}
	}

	/**
	 * 获得业务操作
	 * 
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * 获得业务对象
	 * 
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * 返回一组应用入口
	 * 
	 * @return string
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * 设置action信息
	 * 
	 * @param string $action
	 * @return
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * 设置controller信息
	 * 
	 * @param string $controller
	 * @return
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * @param boolean $reParse
	 * @return 
	 */
	public function reParse() {
		$this->reParse = true;
	}

}