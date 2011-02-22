<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-1
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindErrorHandler {

	private $response = null;

	/**
	 * @param string $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 */
	public function errorHandle($errno, $errstr, $errfile, $errline) {
		
	}

	public function buildErrorMessage($errstr, $errfile, $errline, $type = '') {
		$message = self::buildMessage($errstr, $errfile, $errline, $type);
		$message .= "PHP " . PHP_VERSION . "(" . PHP_OS . ")\n";
		$message .= "Aborting...\n";
		return $message;
	}

	public function buildMessage($errstr, $errfile, $errline, $type = '') {
		$message = "Error Type: $type\nError Message: $errstr\n";
		$message .= "Info: on line $errline in file $errfile \n";
		return $message;
	}

}