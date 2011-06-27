<?php
Wind::import('WIND:core.web.WindController');
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
			$this->urlReferer = $this->request->getUrlReferer();
		else
			$this->urlReferer = $this->request->getBaseUrl();
		return true;
	}

	/* (non-PHPdoc)
	 * @see WindAction::run()
	 */
	public function run() {
		$_tmp = "User Message:\r\n";
		$i = 0;
		foreach ($this->error as $key => $value) {
			$i++;
			$_tmp .= "#$i " . $value . "\r\n";
		}
		echo "<h3>User Message: (" . count($this->error) . ")</h3>";
		echo "<p>" . nl2br($_tmp) . "</p>";
		echo "<a href='" . $this->urlReferer . "'>Click to go back.</a>";
		Wind::log('User Error:', $_tmp);
		exit();
	}

	/**
	 * @param string $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 */
	final public function errorHandle($errno, $errstr, $errfile, $errline) {
		if ($errno & error_reporting()) {
			$errfile = $this->getFile($errfile);
			$_tmp = "$errstr ($errfile:$errline)\r\nStack trace:\r\n";
			$_trace = debug_backtrace(false);
			foreach ($_trace as $key => $value) {
				if (!isset($value['file'])) continue;
				if (!isset($value['line'])) $value['line'] = 0;
				if (!isset($value['function'])) continue;
				$_tmp .= "#$key {$value['file']}({$value['line']}): ";
				if (isset($value['object']) && is_object($value['object'])) $_tmp .= get_class($value['object']) . '->';
				$_tmp .= "{$value['function']}()\r\n";
			}
			if (IS_DEBUG) {
				echo "<h3>" . $this->errnoMap($errno) . " $errstr</h3>";
				echo "<p>" . nl2br($_tmp) . "</p>";
			} else
				echo "<h3>" . $this->errnoMap($errno) . " $errstr</h3>";
			Wind::log($this->errnoMap($errno) . $errstr, $_tmp);
		}
	}

	/**
	 * Enter description here ...
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

	/**
	 * 异常处理句柄
	 */
	final public function exceptionHandle($exception) {
		$_tmp = $exception->getMessage() . ' (' . $this->getFile($exception->getFile()) . ':' . $exception->getLine() . ')';
		if (IS_DEBUG) {
			echo '<h3>' . get_class($exception) . '</h3>';
			echo "<p>$_tmp</p>";
			echo '<pre>' . $exception->getTraceAsString() . '</pre>';
		} else {
			echo '<h3>' . get_class($exception) . '</h3>';
			echo '<p>' . $exception->getMessage() . '</p>';
		}
		Wind::log("$_tmp:" . $exception->getTraceAsString());
		exit();
	}

	private function getFile($filePath) {
		return $filePath;
	}
}