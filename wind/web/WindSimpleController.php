<?php
/**
 * 操作控制器,管理用户的请求处理操作.
 * 
 * 该类有一个抽象方法作为默认处理用户请求的操作实现'run',用户可以通过实现该方法处理请求
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
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
	 * @var WindHttpRequest
	 */
	protected $request = null;
	/**
	 * @var WindHttpResponse
	 */
	protected $response = null;

	/**
	 * 默认的操作处理方法
	 * 
	 * @return void
	 */
	abstract public function run();
	
	/* (non-PHPdoc)
	 * @see IWindController::doAction()
	 */
	public function doAction($handlerAdapter) {
		$_vars = $this->getResponse()->getData('F');
		if ($_vars) $this->getForward()->setVars($_vars);
		
		$this->beforeAction($handlerAdapter);
		$this->setDefaultTemplateName($handlerAdapter);
		$method = $this->resolvedActionMethod($handlerAdapter);
		call_user_func_array(array($this, $method), array());
		if ($this->errorMessage !== null) $this->getErrorMessage()->sendError();
		$this->afterAction($handlerAdapter);
		return $this->forward;
	}

	/**
	 * 保存token令牌
	 * 
	 * @return　void
	 */
	protected function saveToken($tokenName = 'token') {
		/* @var $token WindSecurityToken */
		$token = Wind::getApp()->getComponent('windToken');
		return $token->saveToken($tokenName);
	}

	/**
	 * 验证令牌
	 * 
	 * @param string $tokenValue 当前获得的token值
	 * @param string $tokenName token名称
	 * @return void
	 */
	protected function validateToken($tokenValue, $tokenName = 'token') {
		/* @var $token WindSecurityToken */
		$token = Wind::getApp()->getComponent('windToken');
		return $token->validateToken($tokenValue, $tokenName);
	}

	/**
	 * action过滤链策略部署
	 * @example 
	 * <pre>
	 * $filters = array(array('expression'=>'', 'class'=>'', args=array()));
	 * </pre>
	 * 
	 * @param array $filters
	 * @return void
	 */
	protected function resolveActionFilter($filters) {
		if (!$filters) return;
		$chain = WindFactory::createInstance('WindHandlerInterceptorChain');
		$args = array($this->getForward(), $this->getErrorMessage(), null);
		foreach ((array) $filters as $value) {
			$chain->addInterceptors(
				WindFactory::createInstance(Wind::import($value['class']), 
					(empty($value['args']) ? $args : array_merge($args, array($value['args'])))));
		}
		$chain->getHandler()->handle();
	}

	/**
	 * action操作开始前调用
	 * 
	 * @param AbstractWindRouter $handlerAdapter
	 */
	protected function beforeAction($handlerAdapter) {}

	/**
	 * action操作结束后调用
	 * 
	 * @param AbstractWindRouter $handlerAdapter
	 */
	protected function afterAction($handlerAdapter) {}

	/**
	 * 重定向一个请求到另外的action
	 * 
	 * @param string $action 支持格式:module/controller/action/?args
	 * @param array $args 参数信息 默认为空数组
	 * @param boolean $isRedirect 是否是重定向请求 以url重定向方式跳转
	 * @param boolean $immediately 是否立即forward
	 * @return void
	 */
	protected function forwardAction($action, $args = array(), $isRedirect = false, $immediately = true) {
		$this->getForward()->forwardAction($action, $args, $isRedirect, $immediately);
	}

	/**
	 * 重定向一个请求到另外的URL
	 * 
	 * @param string $url
	 * @return void
	 */
	protected function forwardRedirect($url) {
		$this->getForward()->forwardRedirect($url);
	}
	
	/* 数据处理 */
	/**
	 * 设置模板数据
	 * 
	 * @param string|array|object $data
	 * @param string $key
	 * @return void
	 */
	protected function setOutput($data, $key = '') {
		$this->getForward()->setVars($data, $key);
	}

	/**
	 * 设置模板数据
	 * 
	 * @param string|array|object $data
	 * @param string $key
	 * @return void
	 */
	protected function setGlobal($data, $key = '') {
		Wind::getApp()->setGlobal($data, $key);
	}

	/**
	 * 获得输入数据
	 * 
	 * 如果输入了回调方法则返回数组:第一个值：value;第二个值：验证结果
	 * @param string $name input name
	 * @param string $type input type (GET POST COOKIE)
	 * @param string $callback validation for input
	 * @return array|string
	 */
	protected function getInput($name, $type = '', $callback = null) {
		if (is_array($name)) {
			$result = array();
			foreach ($name as $key => $value) {
				$result[$key] = $this->getInput($value, $type, $callback);
			}
			return $result;
		} else {
			$value = '';
			switch (strtolower($type)) {
				case 'get':
					$value = $this->getRequest()->getGet($name);
					break;
				case 'post':
					$value = $this->getRequest()->getPost($name);
					break;
				case 'cookie':
					$value = $this->getRequest()->getCookie($name);
					break;
				default:
					$value = $this->getRequest()->getAttribute($name);
			}
			return $callback ? array($value, call_user_func_array($callback, array($value))) : $value;
		}
	}
	
	/* 模板处理 */
	/**
	 * 设置页面模板
	 * 
	 * @param string $template
	 * @return void 
	 */
	protected function setTemplate($template) {
		$this->getForward()->getWindView()->templateName = $template;
	}

	/**
	 * 设置模板路径,模板目录地址,支持命名空间方式
	 * 
	 * @param string $templatePath
	 * @return void
	 */
	protected function setTemplatePath($templatePath) {
		$this->getForward()->getWindView()->templateDir = $templatePath;
	}

	/**
	 * 设置模板文件的扩展名
	 * 
	 * @param string $templateExt
	 * @return void
	 */
	protected function setTemplateExt($templateExt) {
		$this->getForward()->getWindView()->templateExt = $templateExt;
	}

	/**
	 * 设置当前主题信息
	 * 
	 * @param string $theme
	 * @return void
	 */
	protected function setTheme($theme) {
		$this->getForward()->getWindView()->setTheme($theme);
	}

	/**
	 * 设置主题包
	 *
	 * @param string $package
	 * @return void
	 */
	protected function setThemePackage($package) {
		$this->getForward()->getWindView()->setThemePackage($package);
	}

	/**
	 * 设置布局页面
	 * 
	 * @param string $layout
	 * @return void
	 */
	protected function setLayout($layout) {
		$this->getForward()->getWindView()->layout = $layout;
	}
	
	/* 错误处理 */
	/**
	 * 添加错误信息
	 * 
	 * @param string $message
	 * @param string $key 默认为空字符串
	 * @return void 
	 */
	protected function addMessage($message, $key = '') {
		$this->getErrorMessage()->addError($message, $key);
	}

	/**
	 * 发送一个错误请求
	 * 
	 * @param string $message 默认为空字符串
	 * @param string $key 默认为空字符串
	 * @param string $errorAction 默认为空字符串
	 * @return void
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
	 * @return void
	 */
	protected function setDefaultTemplateName($handlerAdapter) {}

	/**
	 * 解析action操作方法名称
	 * 
	 * 可以通过覆盖该方法,改变action解析规则
	 * @param WindUrlBasedRouter $handlerAdapter
	 * @return string 返回解析到的action操作处理方法,默认只返回run
	 */
	protected function resolvedActionMethod($handlerAdapter) {
		return 'run';
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
 * 应用控制器接口定义类
 * 
 * 该类定义了一个唯一需要被实现的方法'doAction',该方法接收一个路由对象作为参数,进行操作处理并返回一个forward对象
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
interface IWindController {

	/**
	 * 处理请求并返回Forward对象
	 * 
	 * @param WindRouter $handlerAdapter
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @return WindForward|null
	 */
	public function doAction($handlerAdapter);
}
?>