<?php
/**
 * 日志记录
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WindLogger extends WindComponentModule {
	const LEVEL_INFO = 1;
	const LEVEL_TRACE = 2;
	const LEVEL_DEBUG = 3;
	const LEVEL_ERROR = 4;
	const LEVEL_PROFILE = 5;
	const TOKEN_BEGIN = 'begin:';
	const TOKEN_END = 'end:';
	const WRITE_ALL = 0;
	const WRITE_LEVEL = 1;
	const WRITE_TYPE = 2;
	/**
	 * 每次当日志数量达到100的时候，就写入文件一次
	 * @var int
	 */
	private $_autoFlush = 1000;
	private $_logs = array();
	private $_logCount = 0;
	private $_profiles = array();
	private $_logDir;
	private $_maxFileSize = 100;
	private $_writeType = '0'; //0只按照log记录打印全部结果，1并且按照level打印结果，2并且按照type打印结果
	private $_types = array();

	/**
	 * @param string $logDir
	 * @param int $writeType
	 */
	public function __construct($logDir = '', $writeType = 0) {
		$this->_logDir = $logDir;
		$this->_writeType = $writeType;
	}

	/**
	 * 添加info级别的日志信息
	 * @param string $msg
	 */
	public function info($msg, $type = 'wind.system') {
		$this->log($msg, self::LEVEL_INFO, $type);
	}

	/**
	 * 添加trace级别的日志信息
	 * @param string $msg
	 */
	public function trace($msg, $type = 'wind.system') {
		$this->log($msg, self::LEVEL_TRACE, $type);
	}

	/**
	 * 添加debug的日志信息
	 * @param string $msg
	 */
	public function debug($msg, $type = 'wind.system') {
		$this->log($msg, self::LEVEL_DEBUG, $type);
	}

	/**
	 * 添加Error级别的日志信息
	 * @param string $msg
	 */
	public function error($msg, $type = 'wind.core') {
		$this->log($msg, self::LEVEL_ERROR, $type);
	}

	/**
	 * @param $msg
	 * @param $type
	 */
	public function profileBegin($msg, $type = 'wind.core') {
		$this->log('begin:' . trim($msg), self::LEVEL_PROFILE, $type);
	}

	/**
	 * @param $msg
	 * @param $type
	 */
	public function profileEnd($msg, $type = 'wind.core') {
		$this->log('end:' . trim($msg), self::LEVEL_PROFILE, $type);
	}

	/**
	 * 记录日志信息，但不写入文件
	 * @param string $msg	     日志信息
	 * @param const  $logType 日志类别
	 */
	public function log($msg, $level = self::LEVEL_INFO, $type = 'wind.system') {
		if ($this->_writeType == self::WRITE_TYPE)
			(count($this->_types[$type]) >= 5 || $this->_logCount >= $this->_autoFlush) && $this->flush();
		else
			$this->_logCount >= $this->_autoFlush && $this->flush();
		if ($level === self::LEVEL_PROFILE)
			$message = $this->_build($msg, $level, $type, microtime(true), $this->getMemoryUsage(false));
		elseif ($level === self::LEVEL_DEBUG)
			$message = $this->_build($msg, $level, $type, microtime(true));
		else
			$message = $this->_build($msg, $level, $type);
		$this->_logs[] = array($level, $type, $message);
		$this->_logCount++;
		if ($this->_writeType == self::WRITE_TYPE) $this->_types[$type] = isset($this->_types[$type]) ? count($this->_types[$type]) + 1 : 1;
	}

	/**
	 * 将记录的日志列表信息写入文件
	 * @param string $dst  日志被记录于何处
	 * @return boolean
	 */
	public function flush() {
		if (empty($this->_logs)) return false;
		Wind::import('WIND:component.utility.WindFile');
		if ($this->_writeType == self::WRITE_LEVEL) {
			$_logs = array();
			foreach ($this->_logs as $key => $value) {
				$_logs[$value[0]][] = $value[2];
			}
			foreach ($_logs as $key => $value) {
				switch ($key) {
					case self::LEVEL_INFO:
						$key = 'info';
						break;
					case self::LEVEL_ERROR:
						$key = 'error';
						break;
					case self::LEVEL_DEBUG:
						$key = 'debug';
						break;
					case self::LEVEL_TRACE:
						$key = 'trace';
						break;
					case self::LEVEL_PROFILE:
						$key = 'profile';
						break;
					default:
						$key = 'all';
						break;
				}
				if (!$fileName = $this->_getFileName($key)) continue;
				WindFile::write($fileName, join("", $value), 'a');
			}
		} elseif ($this->_writeType == self::WRITE_TYPE) {
			$_logs = array();
			foreach ($this->_logs as $key => $value) {
				$_logs[$value[1]][] = $value[2];
			}
			foreach ($_logs as $key => $value) {
				if (!$fileName = $this->_getFileName($key)) continue;
				WindFile::write($fileName, join("", $value), 'a');
			}
		} else {
			if ($fileName = $this->_getFileName()) WindFile::write($fileName, join("", $this->_logs), 'a');
		}
		$this->_logs = array();
		$this->_logCount = 0;
		return true;
	}

	/**
	 * 返回内存使用量
	 * @param $peak | 是否是内存峰值
	 * @return int
	 */
	public function getMemoryUsage($peak = true) {
		if ($peak && function_exists('memory_get_peak_usage'))
			return memory_get_peak_usage();
		elseif (function_exists('memory_get_usage'))
			return memory_get_usage();
		$pid = getmypid();
		if (strncmp(PHP_OS, 'WIN', 3) === 0) {
			exec('tasklist /FI "PID eq ' . $pid . '" /FO LIST', $output);
			return isset($output[5]) ? preg_replace('/[\D]/', '', $output[5]) * 1024 : 0;
		} else {
			exec("ps -eo%mem,rss,pid | grep $pid", $output);
			$output = explode("  ", $output[0]);
			return isset($output[1]) ? $output[1] * 1024 : 0;
		}
	}

	/**
	 * 组装日志信息
	 * @param string $msg	     日志信息
	 * @param const  $logType 日志类别
	 * @return string
	 */
	private function _build($msg, $level, $type, $timer = 0, $mem = 0) {
		$msg = stripslashes(str_replace("<br/>", "\r\n", trim($msg)));
		switch ($level) {
			case self::LEVEL_INFO:
				$msg .= "\t(" . $type . ")";
				$result = $this->_buildInfo($msg);
				break;
			case self::LEVEL_ERROR:
				$msg .= "\t(" . $type . ")";
				$result = $this->_buildError($msg);
				break;
			case self::LEVEL_DEBUG:
				$msg .= "\t(" . $type . " timer: " . sprintf('%0.5f', ($timer - DEBUG_TIME)) . ")\r\n";
				$result = $this->_buildDebug($msg);
				break;
			case self::LEVEL_TRACE:
				$msg .= "\t(" . $type . ")";
				$result = $this->_buildTrace($msg);
				break;
			case self::LEVEL_PROFILE:
				$result = $this->_buildProfile($msg, $type, $timer, $mem);
				break;
			default:
				break;
		}
		return $result ? '[' . date('Y-m-d H:i:s') . '] ' . $result . "\r\n" : '';
	}

	/**
	 * @param $msg
	 * @param $type
	 * @param $timer
	 * @param $mem
	 */
	private function _buildProfile($msg, $type, $timer, $mem) {
		$_msg = '';
		if (strncasecmp($msg, self::TOKEN_BEGIN, strlen(self::TOKEN_BEGIN)) == 0) {
			$_token = substr($msg, strlen(self::TOKEN_BEGIN));
			$_token = substr($_token, 0, strpos($_token, ':'));
			$this->_profiles[] = array($_token, substr($msg, strpos($msg, ':', strlen(self::TOKEN_BEGIN)) + 1), $type, $timer, $mem);
		} elseif (strncasecmp(self::TOKEN_END, $msg, strlen(self::TOKEN_END)) == 0) {
			$_msg = "PROFILE! Message: \r\n";
			$_token = substr($msg, strlen(self::TOKEN_END));
			$_token = substr($_token, 0, strpos($_token, ':'));
			foreach ($this->_profiles as $key => $profile) {
				if ($profile[0] !== $_token) continue;
				if ($profile[1])
					$_msg .= $profile[1] . "\r\n";
				else
					$_msg .= substr($msg, strpos($msg, ':', strlen(self::TOKEN_END)) + 1) . "\r\n";
				$_msg .= "(type: $profile[2] time: " . ($timer - $profile[3]) . " mem: " . ($mem - $profile[4]) . ")";
				break;
			}
			unset($this->_profiles[$key]);
		}
		return $_msg;
	}

	/**
	 * 组装info级别的信息输出格式
	 * <code>
	 * [2011-01-24 10:00:00] INFO! Message: $msg
	 * </code>
	 * @param string $msg
	 * @return string
	 */
	private function _buildInfo($msg) {
		return "INFO! Message:  " . $msg;
	}

	/**
	 * 组装堆栈trace的信息输出格式
	 * <code>
	 * [2011-01-24 10:00:00] TRACE! Message: $msg
	 * #1 trace1
	 * #2 trace2
	 * </code>
	 * @param string $msg
	 * @return string
	 */
	private function _buildTrace($msg) {
		return "TRACE! Message:  " . $msg . implode("\r\n", $this->_getTrace());
	}

	/**
	 * 组装debug信息输出
	 * <code>
	 * [2011-01-24 10:00:00] DEBUG! Message: $msg
	 * #1 trace1
	 * #2 trace2
	 * </code>
	 * @param string $msg
	 * @return string
	 */
	private function _buildDebug($msg) {
		return 'DEBUG! Message:  ' . $msg . implode("\r\n", $this->_getTrace());
	}

	/**
	 *组装Error信息输出
	 * <code>
	 * [2011-01-24 10:00:00] ERROR! Message: $msg
	 * #1 trace1
	 * #2 trace2
	 * </code>
	 * @param string $msg
	 * @return string
	 */
	private function _buildError($msg) {
		return 'ERROR! Message:  ' . $msg;
	}

	/**
	 * 错误堆栈信息的获取及组装输出
	 * <code>
	 * #1 trace
	 * #2 trace
	 * </code>
	 * @return string
	 */
	private function _getTrace() {
		$num = 0;
		$info[] = 'Stack trace:';
		$traces = debug_backtrace(false);
		foreach ($traces as $traceKey => $trace) {
			if ($num >= 7) break;
			if ((isset($trace['class']) && $trace['class'] == __CLASS__) || isset($trace['file']) && strrpos($trace['file'], __CLASS__ . '.php') !== false) continue;
			$file = isset($trace['file']) ? $trace['file'] . '(' . $trace['line'] . '): ' : '[internal function]: ';
			$function = isset($trace['class']) ? $trace['class'] . $trace['type'] . $trace['function'] : $trace['function'];
			if ($function == 'WindBase::log') continue;
			$args = array_map(array($this, '_buildArg'), $trace['args']);
			$info[] = '#' . ($num++) . ' ' . $file . $function . '(' . implode(',', $args) . ')';
		}
		return $info;
	}

	/**
	 * 组装输出的trace中的参数组装
	 * @param mixed $arg
	 */
	private function _buildArg($arg) {
		switch (gettype($arg)) {
			case 'array':
				return 'Array';
				break;
			case 'object':
				return 'Object ' . get_class($arg);
				break;
			default:
				return "'" . $arg . "'";
				break;
		}
	}

	/**
	 * 取得日志文件名
	 * @return string 
	 */
	private function _getFileName($suffix = '') {
		$_maxsize = ($this->_maxFileSize ? $this->_maxFileSize : 100) * 1024;
		$_logfile = $this->_logDir . '/log' . ($suffix ? '_' . $suffix . '_' : '') . '.txt';
		if (is_file($_logfile) && $_maxsize <= filesize($_logfile)) {
			$counter = 0;
			do {
				$counter++;
				$_newFile = $_logfile . '_' . date("Y_m_d_{$counter}");
			} while (is_file($_newFile));
			@rename($_logfile, $_newFile);
		}
		return $_logfile;
	}

	public function __destruct() {
		$this->flush();
	}

	/**
	 * @param field_type $_logFile
	 */
	public function setLogDir($logDir) {
		$this->_logDir = $logDir;
	}

	/**
	 * @param field_type $_maxFileSize
	 */
	public function setMaxFileSize($maxFileSize) {
		$this->_maxFileSize = (int) $maxFileSize;
	}

}

	