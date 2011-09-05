<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
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