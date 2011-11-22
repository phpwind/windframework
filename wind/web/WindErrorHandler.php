<?php
/**
 * 系统默认的错误处理类
 * 
 * 系统默认错误处理类,当不配置任何错误处理句柄定义时,该类自动被用于错误处理.
 * 可以通过配置'error'模块,或者重定义'error-handler'来改变当前的错误处理句柄.<code>
 * <module name='default'>
 * <error-handler>WIND:core.web.WindErrorHandler</error-handler>
 * ...
 * </module>
 * </code>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package web
 */
class WindErrorHandler extends WindController {
	protected $error = array();
	protected $errorCode = 0;
	protected $errorDir = 'WIND:web.view';

	/* (non-PHPdoc)
	 * @see WindAction::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		$this->error = $this->getForward()->getVars('error');
		$this->errorCode = (int) $this->getForward()->getVars('errorCode');
	}

	/* (non-PHPdoc)
	 * @see WindAction::run()
	 */
	public function run() {
		$this->setOutput("Error message", "errorHeader");
		$this->setOutput($this->error, "errors");
		$errDir = Wind::getApp()->getConfig('errorpage');
		!$errDir && $errDir = $this->errorDir;
		$this->setTemplatePath($errDir);
		$this->setTemplate('erroraction');
	}
}