<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.WindComponentModule');
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
abstract class AbstractWindRouter extends WindComponentModule {

	protected $errorHandle = 'WIND:core.web.WindErrorHandler';

	protected $action = 'run';

	protected $controller = 'index';

	protected $module = 'default';

	protected $modulePath = '';

	protected $appName = '';

	protected $reParse = true;

	protected $defaultControllerSuffix = 'Controller';

	protected $defaultControllerPath = 'controller';

	/**
	 * Enter description here ...
	 */
	abstract public function parse();

	/**
	 * Enter description here ...
	 */
	abstract public function getHandler();

	/**
	 * Enter description here ...
	 */
	abstract public function buildUrl();

	/**
	 * Enter description here ...
	 */
	public function doParse() {
		if ($this->reParse) {
			$this->parse();
			$this->reParse = false;
		}
		$this->destroy();
	}

	/**
	 * 返回Error处理句柄
	 * @return Ambigous <string, multitype:, unknown>
	 */
	protected function getErrorHandler() {
		$moduleConfig = $this->windSystemConfig->getModules($this->getModule());
		if ($errorHandler = $this->windSystemConfig->getConfig(self::ERROR_HANDLER, WIND_CONFIG_CLASS, $moduleConfig))
			$controllerPath = $errorHandler;
		else
			$controllerPath = $this->errorHandle;
		return $controllerPath;
	}

	/**
	 * 重新进行解析
	 */
	public function reParse() {
		$this->reParse = true;
	}

	/**
	 * 获得业务操作
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * 获得业务对象
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * 返回一组应用入口
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * @param string $action
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * @param string $controller
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * @param string $module
	 */
	public function setModule($module) {
		if (false !== $pos = strpos($module, ':')) {
			$this->appName = substr($module, 0, $pos);
			$this->modulePath = substr($module, $pos + 1);
			if (false === strpos($this->modulePath, '.')) {
				$this->module = $this->modulePath;
				$this->modulePath = '';
			}
		} elseif (false !== strpos($module, '.')) {
			$this->modulePath = $module;
		} else {
			$this->module = $module;
			$this->modulePath = '';
			$this->appName = '';
		}
	}

	/**
	 * 销毁缓存
	 */
	protected function destroy() {
		$this->modulePath = '';
		$this->appName = '';
	}

}