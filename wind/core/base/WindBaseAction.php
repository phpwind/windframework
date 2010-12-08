<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.base.IWindAction');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindBaseAction implements IWindAction {
	protected $request;
	protected $response;
	protected $forward = null;
	protected $error = null;
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function __construct($request, $response) {
		L::import('WIND:core.WindForward');
		$this->forward = new WindForward();
		$this->forward->setViewName($response->getDispatcher()->getController() . '_' . $response->getDispatcher()->getAction());
		$this->error = WindErrorMessage::getInstance();
		$this->request = $request;
		$this->response = $response;
	}
	
	public function beforeAction() {}
	abstract public function run();
	public function afterAction() {}
	
	/**
	 * 请求另一个操作处理
	 * 
	 * @param string $actionHandle
	 * @param string $path
	 */
	public function forwardAction($actionHandle = '', $path = '') {
		$this->forward->setAction($actionHandle, $path);
	}
	
	/* 数据处理 */
	
	/**
	 * 设置模板数据
	 * @param string|array|object $data
	 * @param string $key
	 */
	public function setOutput($data, $key = '') {
		$this->response->setData($data, $key);
	}
	
	/**
	 * 获得输入数据
	 * 如果输入了回调方法则返回数组:
	 * 第一个值：value
	 * 第二个值：验证结果
	 * @param string $name input name
	 * @param string $type input type (GET POST COOKIE)
	 * @param string $callback | validation for input
	 * @return array | string
	 */
	public function getInput($name, $type = '', $callback = null) {
		if (is_array($name))
			return $this->getInputWithArray($name, $type);
		else
			return $this->getInputWithString($name, $type, $callback);
	}
	
	private function getInputWithString($name, $type = '', $callback = null) {
		$value = '';
		switch ($type) {
			case IWindRequest::INPUT_TYPE_GET:
				$value = $this->request->getGet($name);
			case IWindRequest::INPUT_TYPE_POST:
				$value = $this->request->getPost($name);
			case IWindRequest::INPUT_TYPE_COOKIE:
				$value = $this->request->getCookie($name);
			default:
				$value = $this->request->getAttribute($name);
		}
		return $callback ? array($value, call_user_func_array($callback, $value)) : $value;
	}
	
	private function getInputWithArray($name, $type = '') {
		$result = array();
		foreach ($name as $key => $value) {
			$result[(is_array($value) ? $key : $value)] = $this->getInput($value, $type);
		}
		return $result;
	}
	
	/* 错误处理 */
	
	/**
	 * 添加错误信息
	 * 
	 * @param string $message
	 * @param string $key
	 */
	public function addError($message, $key = '') {
		$this->error->addError($message, $key);
	}
	
	/**
	 * @param string $message
	 * @param string $key
	 */
	public function sendError($message = '', $key = '', $errorAction = '') {
		$this->addError($message, $key);
		$this->error->setErrorAction($errorAction);
		$this->error->sendError();
	}
	
	/* 模板处理 */
	
	/**
	 * 设置页面模板
	 * @param string $template
	 */
	public function setTemplate($template = '') {
		if ($template) $this->forward->setViewName($template);
	}
	
	/**
	 * 设置页面布局
	 * @param WindLayout $layout
	 */
	public function setLayout($layout = '') {
		if ($layout instanceof WindLayout) {
			$this->forward->setLayout($layout);
		}
	}
	
	/**
	 * @return WindModelAndView $mav
	 */
	public function forward() {
//		$this->sendError();
		return $this->forward;
	}
}