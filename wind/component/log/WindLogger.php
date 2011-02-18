<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

defined('LOG_PATH') or define('LOG_PATH', COMPILE_PATH . 'log/');
/**
 * 日志记录
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
/**
 * 
 * @modify xiaoxia xu <x_824@sina.com>
 * @version $Id$ 2011-01-21 
 * @package
 */
class WindLogger extends WindComponentModule {

	/*错误类型*/
	const INFO = 0;

	const TRACE = 1;

	const DEBUG = 2;

	const ERROR = 3;

	/**
	 * 每次当日志数量达到100的时候，就写入文件一次
	 * 
	 * @var int
	 */
	const FLUSH = 100;

	private $logs = array();

	/**
	 * 添加info级别的日志信息
	 * 
	 * @param string $msg
	 */
	public function info($msg) {
		$this->log($msg, self::INFO);
	}

	/**
	 * 添加trace级别的日志信息
	 * 
	 * @param string $msg
	 */
	public function trace($msg) {
		$this->log($msg, self::TRACE);
	}

	/**
	 * 添加debug的日志信息
	 * 
	 * @param string $msg
	 */
	public function debug($msg) {
		$this->log($msg, self::DEBUG);
	}

	/**
	 * 添加Error级别的日志信息
	 * 
	 * @param string $msg
	 */
	public function error($msg) {
		$this->log($msg, self::ERROR);
	}

	/**
	 * 记录日志信息，但不写入文件
	 * 
	 * @param string $msg	     日志信息
	 * @param const  $logType 日志类别
	 */
	public function log($msg, $logType = self::INFO) {
		$this->logs[] = $this->build($msg, $logType);
		count($this->logs) >= self::FLUSH && $this->flush();
	}

	/**
	 * 将记录的日志列表信息写入文件
	 * 
	 * @param string $type 日志类别
	 * @param string $dst  日志被记录于何处
	 * @param string $header 其它信息
	 * @return boolean
	 */
	public function flush($dst = '') {
		if (!$this->logs) return false;
		$this->writeLog($dst);
		$this->logs = array();
		return true;
	}

	/**
	 * 清空日志文件
	 * 
	 * 根据输入的值删除从现在回到$time秒之前的值
	 * 
	 * @param int $time
	 * @return bool
	 */
	public function clearFiles($time = 0) {
		if (!is_int($time) || 0 > intval($time) || !is_dir(LOG_PATH)) return false;
		$dir = dir(LOG_PATH);
		while (false != ($file = $dir->read())) {
			$file = LOG_PATH . $file;
			is_file($file) ? (microtime(true) - filectime($file)) > $time && @unlink($file) : '';
		}
		$dir->close();
		return true;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $path
	 */
	private function createFolder($path) {
		!is_dir($path) && mkdir($path, 0777, true);
	}

	/**
	 * 组装日志信息
	 * 
	 * @param string $msg	     日志信息
	 * @param const  $logType 日志类别
	 * @return string
	 */
	private function build($msg, $logType = self::INFO) {
		$msg = stripslashes(str_replace("<br/>", "\r\n", trim($msg)));
		return call_user_func_array(array($this, $this->getLogType($logType)), array($msg));
	}

	/**
	 * 组装info级别的信息输出格式
	 * <code>
	 * [2011-01-24 10:00:00] INFO! Message: $msg
	 * </code>
	 * 
	 * @param string $msg
	 * @return string
	 */
	private function buildInfo($msg) {
		return $this->getLogDate() . 'INFO! Message:  ' . $msg . "\r\n";
	}

	/**
	 * 组装堆栈trace的信息输出格式
	 * <code>
	 * [2011-01-24 10:00:00] TRACE! Message: $msg
	 * #1 trace1
	 * #2 trace2
	 * </code>
	 * 
	 * @param string $msg
	 * @return string
	 */
	private function buildTrace($msg) {
		return $this->getLogDate() . 'TRACE! Message:  ' . $msg . "\r\n" . implode("\r\n", $this->getTrace()) . "\r\n";
	}

	/**
	 * 组装debug信息输出
	 * 
	 * <code>
	 * [2011-01-24 10:00:00] DEBUG! Message: $msg
	 * #1 trace1
	 * #2 trace2
	 * </code>
	 * 
	 * @param string $msg
	 * @return string
	 */
	private function buildDebug($msg) {
		return $this->getLogDate() . 'DEBUG! Message:  ' . $msg . "\r\n" . implode("\r\n", $this->getTrace()) . "\r\n";
	}

	/**
	 *组装Error信息输出
	 * 
	 * <code>
	 * [2011-01-24 10:00:00] ERROR! Message: $msg
	 * #1 trace1
	 * #2 trace2
	 * </code>
	 * 
	 * @param string $msg
	 * @return string
	 */
	private function buildError($msg) {
		return $this->getLogDate() . 'ERROR! Message:  ' . $msg . "\r\n" . implode("\r\n", $this->getTrace($this->getLogDate())) . "\r\n";
	}

	/**
	 * 错误堆栈信息的获取及组装输出
	 * 
	 * <code>
	 * #1 trace
	 * #2 trace
	 * </code>
	 * 
	 * @return string
	 */
	private function getTrace($type = '') {
		$info = array();
		$num = 0;
		$lineHeader = ($type = trim($type)) ? $type : '';
		$info[] = $lineHeader . ' Stack trace:';
		foreach (debug_backtrace(false) as $traceKey => $trace) {
			if ((isset($trace['class']) && $trace['class'] == __CLASS__) || isset($trace['file']) && strrpos($trace['file'], __CLASS__ . '.php') !== false) continue;
			$file = isset($trace['file']) ? $trace['file'] . '(' . $trace['line'] . '): ' : '[internal function]: ';
			$function = isset($trace['class']) ? $trace['class'] . $trace['type'] . $trace['function'] : $trace['function'];
			$args = array_map(array($this, 'buildArg'), $trace['args']);
			$info[] = $lineHeader . ' #' . ($num++) . ' ' . $file . $function . '(' . implode(',', $args) . ')';
		}
		return $info;
	}

	/**
	 * 组装输出的trace中的参数组装
	 *
	 * @param mixed $arg
	 */
	private function buildArg($arg) {
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
	 * 根据错误级别代码决定使用的组装方法
	 *
	 * @param int $code
	 * @return string
	 */
	private function getLogType($code) {
		$logType = array(self::INFO => 'buildInfo', self::TRACE => 'buildTrace', self::DEBUG => 'buildDebug', 
			self::ERROR => 'buildError');
		return $logType[intval($code)];
	}

	/**
	 * 获取日志记录时间
	 * 
	 * @return string
	 */
	private function getLogDate() {
		return '[' . date('Y-m-d H:i:s', time()) . '] ';
	}

	/**
	 * 将日志内容写入文件
	 * 
	 * @param string $dst
	 */
	private function writeLog($dst = '') {
		$dst = empty($dst) ? $this->getFileName() : $dst;
		L::import('WIND:utility.WindFile');
		WindFile::writeover($dst, join("", $this->logs), 'a');
	}

	/**
	 * 取得日志文件名
	 * 
	 * @return string 
	 */
	private function getFileName() {
		$this->createFolder(LOG_PATH);
		$size = 1024 * 50;
		$filename = LOG_PATH . date("Y_m_d") . '.log';
		if (is_file($filename) && $size <= filesize($filename)) {
			for ($i = 100; $counter = 100 - $i, $i >= 0; $i--) {
				$filename = LOG_PATH . date("Y_m_d_{$counter}") . '.log';
				if (!is_file($filename) || $size > filesize($filename)) break;
			}
		}
		return $filename;
	}
}

	