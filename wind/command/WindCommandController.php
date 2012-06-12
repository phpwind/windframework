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
abstract class WindCommandController extends WindModule {

	/**
	 * 默认的操作处理方法
	 * 
	 * @return void
	 */
	abstract public function run();

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
	
	/* (non-PHPdoc)
	 * @see IWindController::doAction()
	 */
	public function doAction($handlerAdapter) {
		$this->beforeAction($handlerAdapter);
		$action = $handlerAdapter->getAction();
		if ($action !== 'run') $action = $this->resolvedActionName($action);
		if (in_array($action, array('doAction', 'beforeAction', 'afterAction', 'resolvedActionName')) || !method_exists(
			$this, $action)) {
			throw new WindException('[command.WindCommandController.doAction] ', 
				WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		}
		$method = new ReflectionMethod($this, $action);
		if ($method->isProtected()) throw new WindException('[command.WindCommandController.doAction] ', 
			WindException::ERROR_CLASS_METHOD_NOT_EXIST);
		$args = $this->getRequest()->getRequest('argv');
		call_user_func_array(array($this, $action), $args);
		$this->afterAction($handlerAdapter);
	}

	/**
	 * 显示错误信息
	 * 
	 * @param string $error
	 */
	protected function showError($error) {
		echo "Error: " . $error . "\r\n";
		echo "Try: command help -m someModule -c someController -a someAction";
		exit();
	}

	/**
	 * 显示信息
	 * 
	 * @param string $message 默认为空字符串
	 * @return void
	 */
	protected function showMessage($message) {
		if (is_array($message)) {
			foreach ($message as $key => $value)
				echo "'" . $key . "' => '" . $value . "',\r\n";
		} else
			echo $message, "\r\n";
	}

	/**
	 * 解析action操作方法名称
	 * 
	 * 默认解析规则,在请求的action名称后加'Action'后缀<code>
	 * 请求的action为 'add',则对应的处理方法名为 'addAction',可以通过覆盖本方法,修改解析规则</code>
	 * @param string $action
	 * @return void
	 */
	public function resolvedActionName($action) {
		return $action . 'Action';
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
}

?>