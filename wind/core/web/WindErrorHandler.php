<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-1
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindErrorHandler extends WindAction {

	protected $error = array();

	protected $backUrl = '';

	/* (non-PHPdoc)
	 * @see WindAction::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		$this->error = $this->getInput('error');
		return true;
	}

	/* (non-PHPdoc)
	 * @see WindAction::run()
	 */
	public function run() {
		echo $this->error[0];
		exit();
	}

	/**
	 * @param string $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 */
	public function errorHandle($errno, $errstr, $errfile, $errline) {

	}

}