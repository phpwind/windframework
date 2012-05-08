<?php
/**
 * 自定义errorController
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class ErrorController extends WindErrorHandler {

	/**
	 * (non-PHPdoc)
	 * @see WindErrorHandler::run()
	 */
	public function run() {
		$this->setLayout('layout');
		$this->setGlobal($this->getRequest()->getBaseUrl(true) . '/template/images', 'images');
		$this->setGlobal($this->getRequest()->getBaseUrl(true) . '/template/images', 'css');
		$topic = "Blog Error";
		$this->setOutput($topic, "errorHeader");
		$this->setOutput($this->urlReferer, "baseUrl");
		$this->setOutput($this->error, "errors");
		$this->setTemplate('error');
	}
}