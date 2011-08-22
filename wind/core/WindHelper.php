<?php
/**
 * wind core 基础帮助类
 * 不建议被外部调用
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindHelper {
	const INTERNAL_LOCATION = "~Internal Location~";

	/**
	 * 错误处理句柄
	 * @param string $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 */
	public static function errorHandle($errno, $errstr, $errfile, $errline) {
		echo $errno,$errfile,'.....',$errline,'<br>';
		if ($errno & error_reporting()) {
			restore_error_handler();
			restore_exception_handler();
			$trace = debug_backtrace();
			unset($trace[0]["function"], $trace[0]["args"]);
			self::crash(self::getErrorName($errno) . ':' . $errstr, $errfile, $errline, $trace);
			exit();
		}
	}

	/**
	 * 异常处理句柄
	 * @param Exception $exception
	 */
	public static function exceptionHandle($exception) {
		restore_error_handler();
		restore_exception_handler();
		$trace = $exception->getTrace();
		if (@$trace[0]['file'] == '') {
			unset($trace[0]);
			$trace = array_values($trace);
		}
		$file = @$trace[0]['file'];
		$line = @$trace[0]['line'];
		self::crash($exception->getMessage(), $file, $line, $trace, $exception->getCode());
		exit();
	}

	public static function errorInfo() {
		$info = "The server encountered an internal error and failed to process your request. Please try again later. If this error is temporary, reloading the page might resolve the problem.\nIf you are able to contact the administrator report this error message.";
		$info .= "(" . Wind::getApp()->getConfig('siteInfo', '', "http://www.windframework.com/") . ")";
		return $info;
	}

	/**
	 * @param string $message
	 * @param string $file
	 * @param string $line
	 * @param array $trace
	 */
	protected static function crash($message, $file, $line, $trace, $status = 0) {
		$errmessage = substr($message, 0, 8000) . "\n";
		$_headers = Wind::getApp()->getResponse()->getHeaders();
		$_errhtml = false;
		foreach ($_headers as $_header) {
			if (strtolower($_header['name']) == strtolower('Content-type')) {
				$_errhtml = strpos(strtolower($_header['value']), strtolower('text/html')) !== false;
				break;
			}
		}
		$msg = '';
		if (IS_DEBUG) {
			$errtrace = "__Stack:\n";
			$count = count($trace);
			$padLen = strlen($count);
			foreach ($trace as $key => $call) {
				if (!isset($call['file']) || $call['file'] == '') {
					$call['file'] = self::INTERNAL_LOCATION;
					$call['line'] = 'N/A';
				}
				
				$traceLine = '#' . str_pad(($count - $key), $padLen, "0", STR_PAD_LEFT) . '  ' . self::getCallLine(
					$call);
				$errtrace .= "$traceLine\n";
			}
			$errsample = '';
			if ($_errhtml && is_file($file)) {
				$currentLine = $line - 1;
				$fileLines = explode("\n", file_get_contents($file, null, null, 0, 10000000));
				$topLine = $currentLine - 5;
				$fileLines = array_slice($fileLines, $topLine > 0 ? $topLine : 0, 10, true);
				if (($count = count($fileLines)) > 0) {
					$padLen = strlen($count);
					foreach ($fileLines as $line => &$fileLine)
						$fileLine = " " . htmlspecialchars(
							str_pad($line + 1, $padLen, "0", STR_PAD_LEFT) . ": " . str_replace(
								"\t", "    ", rtrim($fileLine)), null, "UTF-8");
					
					$fileLines[$currentLine] = "<b style=\"color:red; background:whiteSmoke\">" . $fileLines[$currentLine] . "</b>";
					$errsample = implode("\n", $fileLines) . "\n";
				}
			}
			if ($_errhtml)
				$errfile = "<b style=\"background:whiteSmoke\">$file</b>";
			else
				$errfile = "$file";
			$msg = "$errfile\n$errsample\n$errtrace\n";
		}
		$msg .= self::errorInfo();
		if ($status >= 400 && $status <= 505) {
			$_statusMsg = ucwords(Wind::getApp()->getResponse()->codeMap($status));
			$topic = "$status - " . $_statusMsg . "\n";
			header('HTTP/1.x ' . $status . ' ' . $_statusMsg);
			header('Status: ' . $status . ' ' . $_statusMsg);
		} else
			$topic = "Wind Framework - Error Caught\n";
		if ($_errhtml) {
			$msg = "<html><head><title>$topic</title></head><body><pre><h3>$topic</h3><b style=\"background:whiteSmoke\">$errmessage</b>\n$msg</pre></body></html>";
		} else
			$msg = "$topic\n$errmessage\n$msg";
		
		ob_end_clean();
		die($msg);
	}

	/**
	 * @param array $call
	 * @return string
	 */
	private static function getCallLine($call) {
		$call_signature = "";
		if (isset($call['file']))
			$call_signature .= $call['file'] . " ";
		if (isset($call['line']))
			$call_signature .= "(" . $call['line'] . ") ";
		if (isset($call['function'])) {
			$call_signature .= $call['function'] . '(';
			if (isset($call['args'])) {
				foreach ($call['args'] as $arg) {
					if (is_string($arg))
						$arg = '"' . (strlen($arg) <= 64 ? $arg : substr($arg, 0, 64) . "…") . '"';
					else if (is_object($arg))
						$arg = "[Instance of '" . get_class($arg) . "']";
					else if ($arg === true)
						$arg = "true";
					else if ($arg === false)
						$arg = "false";
					else if ($arg === null)
						$arg = "null";
					else
						$arg = strval($arg);
					$call_signature .= $arg . ',';
				}
			}
			$call_signature = trim($call_signature, ',') . ")";
		}
		return $call_signature;
	}

	/**
	 * @param int $errorNumber
	 * @return string
	 */
	protected static function getErrorName($errorNumber) {
		$errorMap = array(E_ERROR => "E_ERROR", E_WARNING => "E_WARNING", E_PARSE => "E_PARSE", 
			E_NOTICE => "E_NOTICE ", E_CORE_ERROR => "E_CORE_ERROR", 
			E_CORE_WARNING => "E_CORE_WARNING", E_COMPILE_ERROR => "E_COMPILE_ERROR", 
			E_COMPILE_WARNING => "E_COMPILE_WARNING", E_USER_ERROR => "E_USER_ERROR", 
			E_USER_WARNING => "E_USER_WARNING", E_USER_NOTICE => "E_USER_NOTICE", 
			E_STRICT => "E_STRICT", E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR", E_ALL => "E_ALL");
		return isset($errorMap[$errorNumber]) ? $errorMap[$errorNumber] : 'E_UNKNOWN';
	}

	/**
	 * 解析ControllerPath
	 * 返回解析后的controller信息，controller，module，app
	 * 
	 * @param string $controllerPath
	 * @return array
	 */
	public static function resolveController($controllerPath) {
		$_m = $_c = '';
		if (!$controllerPath)
			return array($_c, $_m);
		if (false !== ($pos = strrpos($controllerPath, '.'))) {
			$_m = substr($controllerPath, 0, $pos);
			$_c = substr($controllerPath, $pos + 1);
		} else {
			$_c = $controllerPath;
		}
		return array($_c, $_m);
	}
}
?>