<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-1
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindErrorHandler extends WindController {

	protected $error = array();

	protected $urlReferer = '';

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function __construct($request = null, $response = null) {
		$this->request = $request;
		$this->response = $response;
	}

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
		for ($i = 0; $i < count($this->error); $i++) {
			$_tmp .= "#$i " . $this->error[$i] . "\r\n";
		}
		echo "<h3>User Message: (" . count($this->error) . ")</h3>";
		echo "<p>" . nl2br($_tmp) . "</p>";
		echo "<a href='" . $this->urlReferer . "'>Click to go back.</a>";
		$this->addLog("User Error:", $_tmp);
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
				echo "<h3>Error [$errno] $errstr</h3>";
				echo "<p>" . nl2br($_tmp) . "</p>";
			} else
				echo "<h3>Error [$errno] $errstr</h3>";
			
			$this->addLog("Error [$errno] $errstr", $_tmp);
		}
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
		$this->addLog($_tmp, $exception->getTraceAsString());
		exit();
	}

	private function getFile($filePath) {
		$documentRoot = $this->request->getServer('DOCUMENT_ROOT', '');
		$filePath = str_replace(array('\\', '/'), '.', $filePath);
		$documentRoot = str_replace(array('\\', '/'), '.', $documentRoot);
		if ($documentRoot) $filePath = str_replace($documentRoot, '', $filePath);
		return str_replace(array('.'), D_S, $filePath);
	}

	/**
	 * 日志记录
	 * @param string $errno
	 * @param string $message
	 */
	private function addLog($errno, $message) {
		L::import('WIND:component.log.WindLogger');
		$logger = new WindLogger();
		$logger->info("$errno:" . $message);
	}
}