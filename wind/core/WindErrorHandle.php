<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-1
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindErrorHandle {
	
	/**
	 * @param Exception $exception
	 */
	static public function exceptionHandle($exception) {
		$message = 'Uncaught exception';
		echo self::buildErrorMessage($exception->getMessage(), $exception->getFile(), $exception->getLine(), $message);
		echo $exception->getTraceAsString();
	}
	
	/**
	 * @param string $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 */
	static public function errorHandle($errno, $errstr, $errfile, $errline) {
		switch ($errno) {
			case E_USER_ERROR:
				echo self::buildErrorMessage($errstr, $errfile, $errline, 'ERROR');
				exit(1);
				break;
			case E_USER_WARNING:
				echo self::buildMessage($errstr, $errfile, $errline, 'WARNING');
				break;
			case E_USER_NOTICE:
				echo self::buildMessage($errstr, $errfile, $errline, 'NOTICE');
				break;
			default:
				break;
		}
	}
	
	static public function buildErrorMessage($errstr, $errfile, $errline, $type = '') {
		$message = self::buildMessage($errstr, $errfile, $errline, $type);
		$message .= "PHP " . PHP_VERSION . "(" . PHP_OS . ")\n";
		$message .= "Aborting...\n";
		return $message;
	}
	
	static public function buildMessage($errstr, $errfile, $errline, $type = '') {
		$message = "Error Type: $type\nError Message: $errstr\n";
		$message .= "Info: on line $errline in file $errfile \n";
		return $message;
	}

}