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

	/**
	 * @param string $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 */
	final public function errorHandle($errno, $errstr, $errfile, $errline) {
		if ($errno & error_reporting()) {
			$header = $message = $trace = '';
			$header = $errstr;
			if (IS_DEBUG) {
				$message = $errstr . '(' . $errfile . ' : ' . $errline . ')';
				$_trace = debug_backtrace();
				foreach ($_trace as $key => $value) {
					if (!isset($value['file']) || !isset($value['line']) || !isset($value['function']))
						continue;
					$trace .= "#$key {$value['file']}({$value['line']}): ";
					if (isset($value['object']) && is_object($value['object']))
						$trace .= get_class($value['object']) . '->';
					$trace .= "{$value['function']}()\r\n";
				}
			}
			$this->buildMessage($header, $message, $trace);
		}
	}

	/**
	 * 异常处理句柄
	 * @param Exception $exception
	 */
	final public function exceptionHandle($exception) {
		$header = $message = $trace = '';
		$header = $exception->getMessage();
		if (IS_DEBUG) {
			$message = $exception->getMessage() . '(' . $exception->getFile() . ' : ' . $exception->getLine() . ')';
			$trace = $exception->getTraceAsString();
		}
		$this->buildMessage($header, $message, $trace);
	}

	/**
	 * @param string $header
	 * @param string $message
	 * @param string $trace
	 */
	public function buildMessage($header, $message = '', $trace = '') {
		$_tmp = "<h4>$header</h4>";
		$_tmp .= "<p>$message</p>";
		$_tmp .= "<pre>$trace</pre>";
		$this->getResponse()->sendError(500, $_tmp);
	}

	/**
	 * @param $string $errno
	 */
	private function errnoMap($errno) {
		$_tmp = '';
		switch ($errno) {
			case E_ERROR:
				$_tmp = "Error";
				break;
			case E_WARNING:
				$_tmp = "Warning";
				break;
			case E_PARSE:
				$_tmp = "Parse Error";
				break;
			case E_NOTICE:
				$_tmp = "Notice";
				break;
			case E_CORE_ERROR:
				$_tmp = "Core Error";
				break;
			case E_CORE_WARNING:
				$_tmp = "Core Warning";
				break;
			case E_COMPILE_ERROR:
				$_tmp = "Compile Error";
				break;
			case E_COMPILE_WARNING:
				$_tmp = "Compile Warning";
				break;
			case E_USER_ERROR:
				$_tmp = "User Error";
				break;
			case E_USER_WARNING:
				$_tmp = "User Warning";
				break;
			case E_USER_NOTICE:
				$_tmp = "User Notice";
				break;
			case E_STRICT:
				$_tmp = "Strict Notice";
				break;
			case E_RECOVERABLE_ERROR:
				$_tmp = "Recoverable Error";
				break;
			default:
				$_tmp = "Unknown error ($errno)";
				break;
		}
		return $_tmp;
	}

}