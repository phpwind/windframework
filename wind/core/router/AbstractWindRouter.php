<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

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
abstract class AbstractWindRouter {

	protected $action = 'run';

	protected $controller = 'index';

	protected $module = 'default';

	/**
	 * Enter description here ...
	 * 
	 * @param WindHttpRequest $request
	 */
	abstract public function doParse($request);

	/**
	 * Enter description here ...
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	abstract public function getHandler($request, $response);

	/**
	 * 根据路由解析，组装URL
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 */
	abstract public function buildUrl($action = '', $controller = '', $module = '');

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

}