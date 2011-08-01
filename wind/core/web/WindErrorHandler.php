<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindErrorHandler extends WindController {
	protected $error = array();
	protected $urlReferer = '';

	/* (non-PHPdoc)
	 * @see WindAction::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		$this->error = $this->getInput('error');
		if ($this->request->getUrlReferer())
			$this->urlReferer = $this->getRequest()->getUrlReferer();
		else
			$this->urlReferer = $this->getRequest()->getBaseUrl();
		return true;
	}

	/* (non-PHPdoc)
	 * @see WindAction::run()
	 */
	public function run() {
		$this->setOutput("User Error Message: " . $this->error[0], "errorHeader");
		$this->setOutput('', "errorTrace");
		$this->setOutput($this->urlReferer, "baseUrl");
		$this->setOutput($this->error, "errors");
		$this->setTemplate('default_error');
		$this->setTemplatePath('COM:viewer.errorPage');
	}

}