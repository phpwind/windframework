<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-17
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.WindErrorHandle');

/**
 * test case.
 */
class WindErrorHandleTest extends BaseTestCase {
	private $message = 'Uncaught exception';
	public static function providerMessage() {
		return array(
		   array('i am error!', 'test1', '160', ''),
		   array('hahah you are wrong!', 'iamfile', 10000, 222),
		   array('this is error!', 'file1', 30, 'werwer'),
		   array('', '', '', ''),
		);
	}

    public static function providerException() {
    	return array(
    		array(new Exception('hahaha', 1)),
    		array(new Exception('May be i am wrong', 2)),
    		array(new Exception('', 1000)),
    		array(new Exception('')),
    	);
    }
    
    /**
     * E_USER_ERROR 级别的错误会在输出错误信息后进行截断exit(),退出，
     * 所以这里如果要测试E_USER_ERROR级别的错误的话，需要先将截断的语句注释掉
     */
    public static function providerErrorHanddle() {
    	return array(
    		//array(E_USER_ERROR, 'i am error', 'errorfile', 200),
    		array(E_USER_WARNING, 'i am warning', 'loging', 1),
    		array(E_USER_NOTICE, 'i am notice', 'logout', -1),
    		array('', 'i am fine', '', 0),
    		array('', 'i', '', ''),
    	);
    }
	/**
	 */
	public function testBuildMessage() {
		foreach(self::providerMessage() as $value) {
			list($message, $file, $line, $type) = $value;
			$result = "Error Type: $type\nError Message: $message\n";
			$result .= "Info: on line $line in file $file \n";
			$this->assertEquals($result, WindErrorHandle::buildMessage($message, $file, $line, $type));
		}
	}
	
	/**
	 */
    public function testBuildErrorMessage() {
    	foreach(self::providerMessage() as $value) {
    		list($message, $file, $line, $type) = $value;
	    	$result = "Error Type: $type\nError Message: $message\n";
			$result .= "Info: on line $line in file $file \n";
			$result .= "PHP " . PHP_VERSION . "(" . PHP_OS . ")\n";
			$result .= "Aborting...\n";
			$this->assertEquals($result, WindErrorHandle::buildErrorMessage($message, $file, $line, $type));
    	}
    }
    
    /**
	 */
	public function testExceptionHandle() {
		foreach(self::providerException() as $value) {
			$exception = $value[0];
			$message = "Error Type: " . $this->message . "\nError Message: " . $exception->getMessage() . "\n";
			$message .= "Info: on line " . $exception->getLine() . " in file " . $exception->getFile() . " \n";
			$message .= "PHP " . PHP_VERSION . "(" . PHP_OS . ")\n";
			$message .= "Aborting...\n";
			$message .= $exception->getTraceAsString();
			ob_start();
			WindErrorHandle::ExceptionHandle($exception);
			$result = ob_get_clean();
			$this->assertEquals($message, $result);
		}
	}
	
	private function buildMessage($errno, $errstr, $errfile, $errline) {
		switch ($errno) {
			case E_USER_ERROR:
				$result = "Error Type: ERROR\nError Message: $errstr\n";
				$result .= "Info: on line $errline in file $errfile \n";
				$result .= "PHP " . PHP_VERSION . "(" . PHP_OS . ")\n";
				$result .= "Aborting...\n";
				break;
			case E_USER_WARNING:
				$result = "Error Type: WARNING\nError Message: $errstr\n";
				$result .= "Info: on line $errline in file $errfile \n";
				break;
			case E_USER_NOTICE:
				$result = "Error Type: NOTICE\nError Message: $errstr\n";
				$result .= "Info: on line $errline in file $errfile \n";
				break;
			default:
				$result = '';
				break;
		}
		return $result;
	}
	/**
	 */
	public function testErrorHandle() {
		foreach(self::providerErrorHanddle() as $value) {
			list($errno, $errstr, $errfile, $errline) = $value;
			$message = $this->buildMessage($errno, $errstr, $errfile, $errline);
			ob_start();
			WindErrorHandle::errorHandle($errno, $errstr, $errfile, $errline);
			$result = ob_get_clean();
			$this->assertEquals($message, $result);
		}
	}
}

