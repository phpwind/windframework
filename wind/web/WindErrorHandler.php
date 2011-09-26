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
 * @package wind.web
 */
class WindErrorHandler extends WindController {
	protected $error = array();
	protected $errorCode = 0;
	protected $urlReferer = '';
	protected $errorDir = 'WIND:web.view';

	/* (non-PHPdoc)
	 * @see WindAction::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		$this->error = $this->getForward()->getVars('error');
		$this->errorCode = (int) $this->getForward()->getVars('errorCode');
		if ($this->request->getUrlReferer())
			$this->urlReferer = $this->getRequest()->getUrlReferer();
		else
			$this->urlReferer = $this->getRequest()->getBaseUrl();
	}

	/* (non-PHPdoc)
	 * @see WindAction::run()
	 */
	public function run() {
		if ($this->errorCode >= 400 && $this->errorCode <= 505) {
			$_statusMsg = ucwords($this->getResponse()->codeMap($this->errorCode));
			$topic = "$this->errorCode - " . $_statusMsg;
			$this->getResponse()->setStatus($this->errorCode);
		} else
			$topic = "Error message";
		$this->setOutput($topic, "errorHeader");
		$this->setOutput($this->urlReferer, "baseUrl");
		$this->setOutput($this->error, "errors");
		$errDir = Wind::getApp()->getConfig('errorpage');
		!$errDir && $errDir = $this->errorDir;
		$this->setTemplatePath($errDir);
		$this->setTemplate('erroraction');
	}
}