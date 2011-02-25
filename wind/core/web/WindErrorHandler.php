<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-1
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindErrorHandler extends WindAction {

	/* (non-PHPdoc)
	 * @see WindAction::run()
	 */
	public function run() {
		$this->getInput($name);
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