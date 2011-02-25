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

	protected $action = 'run';

	protected $controller = 'index';

	protected $module = 'default';

	protected $reParse = true;

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
	 * 获得一组应用入口
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * @param field_type $action
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * @param field_type $controller
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * @param field_type $module
	 */
	public function setModule($module) {
		$this->module = $module;
	}

}