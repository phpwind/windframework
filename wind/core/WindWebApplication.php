<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.base.WindApplication');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebApplication extends WindApplication {
	public $dispatcher = null;
	protected $process = '';
	
	/**
	 * 初始化配置信息
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function init($dispatcher) {
		$this->dispatcher = $dispatcher;
		$this->dispatcher->setApplication($this);
	}
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WSystemConfig $configObj
	 */
	public function processRequest($request, $response) {
		list($className, $method) = $this->getActionHandle($request, $response);
		$action = new $className($request, $response);
		if (!($action instanceof WindAction)) throw new WindException('the type of action is error.');
		$action->beforeAction();
		$action->$method();
		$action->afterAction();
		$this->processDispatch($request, $response, $action->forward());
	}
	
	/**
	 * 返回action类
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @return array(WindAction,string)
	 */
	protected function getActionHandle() {
		list($className, $method) = $this->dispatcher->getActionHandle();
		if ($className === null || $method === null) {
			throw new WindException('can\'t create action handle.');
		}
		$this->checkReprocess($className . '_' . $method);
		return array(
			$className, 
			$method);
	}
	
	/**
	 * 获得表单处理句柄
	 */
	protected function buildFormObject() {
		$form = new stdClass();
		
	}
	
	/**
	 * 判断是否是重复提交，再一次请求中，不允许连续重复请求两次获两次以上某个操作
	 * @param string $key
	 */
	protected function checkReprocess($key = '') {
		if ($this->process && $this->process === $key) {
			throw new WindException('Duplicate request \'' . $key . '\'');
		}
		$this->process = $key;
	}
	
	/**
	 * 处理页面输出与重定向
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WindModelAndView $forward
	 */
	protected function processDispatch($request, $response, $forward) {
		$this->dispatcher->setForward($forward)->dispatch();
	}
	
	public function destory() {}
}