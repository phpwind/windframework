<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 异常处理机制
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: weihu $>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id: WindException.php 37 2010-11-08 12:57:04Z weihu $ 
 * @package
 */
class WindException extends Exception {

	/* 类错误 */
	const ERROR_CLASS_NOT_EXIST = '100';

	const ERROR_CLASS_TYPE_ERROR = '101';

	const ERROR_CLASS_METHOD_NOT_EXIST = '102';

	/* 参数错误 */
	const ERROR_PARAMETER_TYPE_ERROR = '110';

	/* 配置错误 */
	const ERROR_CONFIG_ERROR = '120';

	private $innerException = null;

	/**
	 * 异常构造函数
	 * 
	 * @param $message		     异常信息
	 * @param $code			     异常代号
	 * @param $innerException 内部异常
	 */
	public function __construct($message = '', $code = 0, Exception $innerException = null) {
		$message = $this->buildMessage($message, $code);
		parent::__construct($message, $code);
		$this->innerException = $innerException;
	}

	/**
	 * 取得内部异常
	 */
	public function getInnerException() {
		return $this->innerException;
	}

	/**
	 * 取得异常堆栈信息
	 */
	public function getStackTrace() {
		if ($this->innerException) {
			$thisTrace = $this->getTrace();
			$class = __CLASS__;
			$innerTrace = $this->innerException instanceof $class ? $this->innerException->getStackTrace() : $this->innerException->getTrace();
			foreach ($innerTrace as $trace)
				$thisTrace[] = $trace;
			return $thisTrace;
		} else {
			return $this->getTrace();
		}
		return array();
	}

	/**
	 * 组装异常信息
	 * 
	 * 根据输入的异常号组装相对应的异常信息
	 * 
	 * @param string $message 用户自定义的信息
	 * @param int $code  异常号
	 * @return string 组装后的异常信息
	 */
	public function buildMessage($message, $code) {
		eval('$message="' . addcslashes($this->messageMapper($code), '"') . '";');
		return $message;
	}

	/**
	 * 自定义异常号的对应异常信息
	 * 
	 * @param int $code  异常号
	 * @return string 返回异常号对应的异常组装信息原型
	 */
	protected function messageMapper($code) {
		$messages = array(self::ERROR_CLASS_TYPE_ERROR => 'Incorrect class type \'$message\'.', 
			self::ERROR_CLASS_NOT_EXIST => 'Unable to create instance for \'$message\' , class is not exist.', 
			self::ERROR_PARAMETER_TYPE_ERROR => 'Incorrect parameter type \'$message\'.', 
			self::ERROR_CONFIG_ERROR => 'Incorrect config. the config about \'$message\' error.', 
			self::ERROR_CLASS_METHOD_NOT_EXIST => '\'$message\' Unable to access the method in the class , or the method is not exist.');
		
		return isset($messages[$code]) ? $messages[$code] : '$message';
	}

}