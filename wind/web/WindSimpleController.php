<?php
/**
 * 简单应用控制器
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
abstract class WindSimpleController extends WindModule implements IWindController {
	protected $_vars = array();
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

	/* (non-PHPdoc)
	 * @see IWindController::doAction()
	 */
	public function doAction($handlerAdapter) {
		if ($this->forward !== null)
			$this->_vars = $this->forward->getVars();
		$this->beforeAction($handlerAdapter);
		$this->setDefaultTemplateName($handlerAdapter);
		$method = $this->resolvedActionMethod($handlerAdapter);
		call_user_func_array(array($this, $method), array());
		if ($this->errorMessage !== null)
			$this->getErrorMessage()->sendError();
		$this->afterAction($handlerAdapter);
		return $this->forward;
	}

	/* (non-PHPdoc)
	 * @see IWindController::resolveActionFilter($action)
	 */
	protected function resolveActionFilter($__filters) {
		@extract(@$this->getRequest()->getRequest(), EXTR_REFS);
		$chain = WindFactory::createInstance('WindHandlerInterceptorChain');
		foreach ((array) $__filters as $__filter) {
			if (isset($__filter['expression']) && !empty($__filter['expression'])) {
				if (!@eval('return ' . $__filter['expression'] . ';'))
					continue;
				/*list($p, $v) = explode('=', $__filter['expression'] . '=');
				if ($this->getRequest()->getRequest($p) != $v)
					continue;*/
			}
			$__args = array($this->getForward(), $this->getErrorMessage());
			if (isset($__filter['args']))
				$__args = $__args + (array) $__filter['args'];
			$chain->addInterceptors(WindFactory::createInstance(Wind::import(@$__filter['class']), $__args));
		}
		$chain->getHandler()->handle();
	}

	/**
	 * @param AbstractWindRouter $handlerAdapter
	 */
	protected function beforeAction($handlerAdapter) {}

	/**
	 * @param AbstractWindRouter $handlerAdapter
	 */
	protected function afterAction($handlerAdapter) {}

	/**
	 * 重定向一个请求到另外的Action
	 * @param string $action
	 * @param array $args
	 * @param boolean $isRedirect
	 * @return 
	 */
	protected function forwardAction($action = 'run', $args = array(), $isRedirect = false) {
		//$this->getForward()->forwardAnotherAction($action, $controller, $args, $isRedirect);
		$this->getForward()->forwardAction($action, $args, $isRedirect);
	}

	/**
	 * 重定向一个请求到另外的URL
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
	 * @param string|array|object $data
	 * @param string $key
	 * @return
	 */
	protected function setOutput($data, $key = '') {
		$this->getForward()->setVars($data, $key);
	}

	/**
	 * 设置模板数据
	 * @param string|array|object $data
	 * @param string $key
	 * @return
	 */
	protected function setGlobal($data, $key = '') {
		$this->getForward()->setVars($data, $key, true);
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
	protected function getInput($name, $type = '', $callback = null) {
		if (is_array($name))
			return $this->getInputWithArray($name, $type);
		else
			return $this->getInputWithString($name, $type, $callback);
	}

	/* 模板处理 */
	/**
	 * 设置页面模板
	 * @param string $template
	 * @return 
	 */
	protected function setTemplate($template) {
		$this->getForward()->getWindView()->templateName = $template;
	}

	/**
	 * 设置模板路径
	 * @param string $templatePath
	 * @return 
	 */
	protected function setTemplatePath($templatePath) {
		$this->getForward()->getWindView()->templateDir = $templatePath;
	}

	/**
	 * 设置模板文件的扩展名
	 * @param string $templateExt
	 * @return
	 */
	protected function setTemplateExt($templateExt) {
		$this->getForward()->getWindView()->templateExt = $templateExt;
	}

	/**
	 * 设置主题包位置
	 * @param string $theme
	 * @return
	 */
	protected function setTheme($theme) {
		$this->getForward()->getWindView()->thems = $theme;
	}

	/**
	 * 设置页面布局
	 * @param string $layout
	 * @return 
	 */
	protected function setLayout($layout) {
		$this->getForward()->getWindView()->layout = $layout;
	}

	/* 错误处理 */
	/**
	 * 添加错误信息
	 * @param string $message
	 * @param string $key
	 * @return 
	 */
	protected function addMessage($message, $key = '') {
		$this->getErrorMessage()->addError($message, $key);
	}

	/**
	 * 发送一个错误
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
				$value = $this->getRequest()->getData($name);
				break;
			case IWindRequest::INPUT_TYPE_GET:
				$value = $this->getRequest()->getGet($name);
				break;
			case IWindRequest::INPUT_TYPE_POST:
				$value = $this->getRequest()->getPost($name);
				break;
			case IWindRequest::INPUT_TYPE_COOKIE:
				$value = $this->getRequest()->getCookie($name);
				break;
			default:
				$value = $this->getRequest()->getAttribute($name);
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
	public function getForward() {
		return $this->_getForward();
	}

	/**
	 * @return WindErrorMessage
	 */
	public function getErrorMessage() {
		return $this->_getErrorMessage();
	}

	/**
	 * @param WindForward $forward
	 */
	public function setForward($forward) {
		$this->forward = $forward;
	}

	/**
	 * @param WindErrorMessage $errorMessage
	 */
	public function setErrorMessage($errorMessage) {
		$this->errorMessage = $errorMessage;
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