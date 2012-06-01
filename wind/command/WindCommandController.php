<?php
/**
 * 命令行操作控制器
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package command
 */
class WindCommandController extends WindModule {
	
	protected $_output = array();
	
	/**
	 * @var WindErrorMessage
	 */
	protected $errorMessage = null;

	/**
	 * 默认的操作处理方法
	 * 
	 * @return void
	 */
	public function run() {}

	/* (non-PHPdoc)
	 * @see IWindController::doAction()
	 */
	public function doAction($handlerAdapter) {
		$this->beforeAction($handlerAdapter);
		list($method, $args) = $this->resolvedActionMethod($handlerAdapter);
		call_user_func_array(array($this, $method), $args);
		if ($this->errorMessage !== null) $this->getErrorMessage()->sendError();
		$this->afterAction($handlerAdapter);
		return $this->_output;
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
	 * 默认帮助action
	 *
	 * @param array $params
	 * @param WindCommandRouter $router
	 */
	public function help($params, $router) {
		if (empty($params)) {
			$helps = '[-m module] [-c controller] [-a action] [-p arg1 arg2 ]';
		} else if (!isset($params[$router->getActionKey()])) {
			$class = new ReflectionClass(get_class($this));
			$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
			$helps = array();
			foreach ($methods as $method) {
				/* @var $method ReflectionMethod */
				$name = $method->getName();
				if ($name === 'doAction') continue;
				if ($name === 'run' || strpos($name, 'Action') > 0) {
					$paramHelp = ' ';
					foreach ($method->getParameters() as $param) {
						$paramHelp .= ($param->isDefaultValueAvailable() ? '[' . $param->getName() . ']' : $param->getName()) . ' ';
					}
					$help = '-' . $router->getModuleKey() . ' ' . $router->getModule() . ' -' . $router->getControllerKey() . ' ' . $router->getController() . ' -' . $router->getActionKey() . ' ' . ($name === 'run' ? 'run' : substr(
						$name, 0, -6));
					$paramHelp && $help .= ' -' . $router->getParamKey() . $paramHelp;
					$helps[] = $help;
				}
			}
		} else {
			$action = $params[$router->getActionKey()] === 'run' ? 'run' : $this->resolvedActionName(
				$params[$router->getActionKey()]);
			$method = new ReflectionMethod($this, $action);
			if (!$method->isPublic()) {
				$helps = array();
				$helps[] = 'undefined action!';
				$helps[] = '[-m module] [-c controller] [-a action] [-p arg1 arg2 ]';
			} else {
				$paramHelp = ' ';
				foreach ($method->getParameters() as $param) {
					$paramHelp .= ($param->isDefaultValueAvailable() ? '[' . $param->getName() . ']' : $param->getName()) . ' ';
				}
				$helps = '-' . $router->getModuleKey() . ' ' . $router->getModule() . ' -' . $router->getControllerKey() . ' ' . $router->getController() . ' -' . $router->getActionKey() . ' ' . $params[$router->getActionKey()];
				$paramHelp && $helps .= ' -' . $router->getParamKey() . $paramHelp;
			}
		}
		
		$this->setOutput($helps);
	}

	/**
	 * 设置模板数据
	 * 
	 * @return void
	 */
	protected function setOutput($data) {
		$this->_output[] = $data;
	}

	/**
	 * 读取输入行
	 *
	 * @return string
	 */
	protected function getLine($message) {
		echo $message;
		return trim(fgets(STDIN));
	}

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
	 * 解析action操作方法名称
	 * 
	 * 默认解析规则,在请求的action名称后加'Action'后缀<code>
	 * 请求的action为 'add',则对应的处理方法名为 'addAction',可以通过覆盖本方法,修改解析规则</code>
	 * @param string $action
	 * @return void
	 */
	protected function resolvedActionName($action) {
		return $action . 'Action';
	}

	/* (non-PHPdoc)
	 * @see WindAction::resolvedActionMethod()
	 */
	protected function resolvedActionMethod($handlerAdapter) {
		$action = $handlerAdapter->getAction();
		if ($action !== 'run' && $action !== 'help') $action = $this->resolvedActionName($action);
		if (in_array($action, array('doAction', 'beforeAction', 'afterAction'))) throw new WindException(
			'[resolvedActionMethod].ERROR_CLASS_METHOD_NOT_EXIST');
		$method = new ReflectionMethod($this, $action);
		if ($method->isAbstract() || !$method->isPublic()) throw new WindException(
			'[resolvedActionMethod].ERROR_CLASS_METHOD_NOT_EXIST');
		
		$args = $this->getRequest()->getAttribute('argv', array());
		if ($action === 'help') $args[] = $handlerAdapter;
		$this->resolvedMethodParams($method, $args);
		
		return array($action, $args);
	}

	/**
	 * 解析方法的参数
	 *
	 * @param ReflectionMethod $method
	 * @param array $args 
	 */
	protected function resolvedMethodParams($method, &$args) {
		$argNum = count($args);
		$requiredNum = $method->getNumberOfRequiredParameters();
		if ($requiredNum > $argNum) throw new WindException(
			'[resolvedMethodParams].ERROR_PARAMETER_TYPE_ERROR');
		$realNum = $method->getNumberOfParameters();
		
		if ($realNum > $requiredNum) {
			$parameters = $method->getParameters();
			for ($i = $argNum; $i < $realNum; $i++) {
				$args[] = $parameters[$i]->getDefaultValue();
			}
		}
	}

	/**
	 * @return WindErrorMessage
	 */
	public function getErrorMessage() {
		return $this->_getErrorMessage();
	}

	/**
	 * @param WindErrorMessage $errorMessage
	 */
	public function setErrorMessage($errorMessage) {
		$this->errorMessage = $errorMessage;
	}
	
}

?>