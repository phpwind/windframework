<?php
/**
 * 简单应用控制器
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class WindSimpleController extends WindModule implements IWindController {
	/**
	 * @var WindForward
	 */
	protected $forward = null;
	/**
	 * @var WindErrorMessage
	 */
	protected $errorMessage = null;

	/**
	 * 默认的操作处理方法
	 */
	abstract public function run();

	/**
	 * @param WindUrlBasedRouter $handlerAdapter
	 */
	protected function beforeAction($handlerAdapter) {
		$this->urlHelper = null;
		$this->errorMessage = null;
		$this->forward = null;
	}

	/**
	 * @param WindUrlBasedRouter $handlerAdapter
	 */
	protected function afterAction($handlerAdapter) {}

	/* (non-PHPdoc)
	 * @see IWindController::doAction()
	 */
	public function doAction($handlerAdapter) {
		$this->beforeAction($handlerAdapter);
		$this->setDefaultTemplateName($handlerAdapter);
		$method = $this->resolvedActionMethod($handlerAdapter);
		call_user_func_array(array($this, $method), array());
		if ($this->errorMessage !== null)
			$this->getErrorMessage()->sendError();
		$this->afterAction($handlerAdapter);
		return $this->forward;
	}

	/**
	 * 重定向一个请求到另外的Action
	 * 
	 * @param string $action
	 * @param string $controller
	 * @param array $args
	 * @param boolean $isRedirect
	 * @return 
	 */
	protected function forwardAction($action = 'run', $controller = '', $args = array(), $isRedirect = false) {
		$this->getForward()->forwardAnotherAction($action, $controller, $args, $isRedirect);
	}

	/**
	 * 重定向一个请求到另外的URL
	 * 
	 * @param string $url
	 * @return 
	 */
	protected function forwardRedirect($url) {
		$this->getForward()->setIsRedirect(true);
		$this->getForward()->setUrl($url);
	}

	/* 数据处理 */
	/**
	 * 设置模板数据
	 * 
	 * @param string|array|object $data
	 * @param string $key
	 * @return
	 */
	protected function setOutput($data, $key = '') {
		$this->getForward()->setVars($data, $key);
	}

	/**
	 * 设置模板数据
	 * 
	 * @param string|array|object $data
	 * @param string $key
	 * @return
	 */
	protected function setGlobal($data, $key = '') {
		$this->getResponse()->setData($data, $key, true);
	}

	/**
	 * 获得输入数据
	 * 如果输入了回调方法则返回数组:
	 * 第一个值：value
	 * 第二个值：验证结果
	 * 
	 * @param string $name input name
	 * @param string $type input type (GET POST COOKIE)
	 * @param string $callback | validation for input
	 * @return array | string
	 */
	protected function getInput($name, $type = '', $callback = null) {
		if (is_array($name))
			return $this->getInputWithArray($name, $type);
		else
			return $this->getInputWithString($name, $type, $callback);
	}

	/* 模板处理 */
	/**
	 * 设置页面模板
	 * 
	 * @param string $template
	 * @return 
	 */
	protected function setTemplate($template) {
		$this->getForward()->setTemplateName($template);
	}

	/**
	 * 设置模板路径
	 * 
	 * @param string $templatePath
	 * @return 
	 */
	protected function setTemplatePath($templatePath) {
		$this->getForward()->setTemplatePath($templatePath);
	}

	/**
	 * 设置模板文件的扩展名
	 * 
	 * @param string $templateExt
	 * @return
	 */
	protected function setTemplateExt($templateExt) {
		$this->getForward()->setTemplateExt($templateExt);
	}

	/**
	 * 设置页面布局
	 * 可以是一个布局对象或者一个布局文件
	 * 
	 * @param WindLayout|string $layout
	 * @return 
	 */
	protected function setLayout($layout) {
		$this->getForward()->setLayout($layout);
	}

	/* 错误处理 */
	/**
	 * 添加错误信息
	 * 
	 * @param string $message
	 * @param string $key
	 * @return 
	 */
	protected function addMessage($message, $key = '') {
		$this->getErrorMessage()->addError($message, $key);
	}

	/**
	 * 发送一个错误
	 * 
	 * @param string $message
	 * @param string $key
	 * @param string $errorAction
	 * @return 
	 */
	protected function showMessage($message = '', $key = '', $errorAction = '') {
		$this->addMessage($message, $key);
		$this->getErrorMessage()->setErrorAction($errorAction);
		$this->getErrorMessage()->sendError();
	}

	/**
	 * 设置默认的模板名称
	 * 
	 * @param WindUrlBasedRouter $handlerAdapter
	 * @return 
	 */
	protected function setDefaultTemplateName($handlerAdapter) {}

	/**
	 * 定义了一种解析策略，使其通过解析请求信息来获得调用的方法。
	 * 
	 * @param WindUrlBasedRouter $handlerAdapter
	 * @return
	 */
	protected function resolvedActionMethod($handlerAdapter) {
		return 'run';
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @param array $callback
	 * @return Ambigous <multitype:unknown mixed , string, unknown, multitype:>
	 */
	private function getInputWithString($name, $type = '', $callback = array()) {
		$value = '';
		switch (strtolower($type)) {
			case 'form':
				$value = $this->response->getData($name);
				break;
			case IWindRequest::INPUT_TYPE_GET:
				$value = $this->request->getGet($name);
				break;
			case IWindRequest::INPUT_TYPE_POST:
				$value = $this->request->getPost($name);
				break;
			case IWindRequest::INPUT_TYPE_COOKIE:
				$value = $this->request->getCookie($name);
				break;
			default:
				$value = $this->request->getAttribute($name);
		}
		return $callback ? array($value, call_user_func_array($callback, array($value))) : $value;
	}

	/**
	 * @param array $name
	 * @param string $type
	 * @return array
	 */
	private function getInputWithArray($name, $type = '') {
		$result = array();
		foreach ($name as $key => $value) {
			$result[(is_array($value) ? $key : $value)] = $this->getInput($value, $type);
		}
		return $result;
	}

	/**
	 * @return WindForward
	 */
	protected function getForward() {
		return $this->_getForward();
	}

	/**
	 * @return WindErrorMessage
	 */
	protected function getErrorMessage() {
		return $this->_getErrorMessage();
	}

}

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
interface IWindController {

	/**
	 * 处理请求并返回Forward对象
	 * @param WindUrlBasedRouter $handlerAdapter
	 * @return WindForward
	 */
	public function doAction($handlerAdapter);
}
?>