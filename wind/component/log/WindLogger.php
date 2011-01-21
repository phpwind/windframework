<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

defined('LOG_PATH') or define('LOG_PATH', './log/');
/**
 * 日志记录
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WindLogger {

	/*错误类型*/
	const ERROR = 'error';

	const TRACE = 'trace';

	const INFO = 'info';

	const DEBUG = 'debug';

	/**日志展示类型***/
	const LOG = 'log';

	const HTML = 'html';

	/*写入日志类别*/
	const SYSTEM = 0;

	const EMAIL = 1;

	const TCP = 2;

	const FILE = 3;

	private static $msgType = array('system' => 0, 'email' => 1, 'tcp' => 2, 'file' => 3);

	private static $logs = array();

	private static $logDisplay = self::LOG;

	/**
	 * Enter description here ...
	 * 
	 * @param string $msg
	 */
	//	public static function info($msg) {
	//		self::add($msg, self::INFO);
	//	}
	

	/**
	 * Enter description here ...
	 * 
	 * @param string $msg
	 */
	public static function trace($msg) {
		self::add($msg, self::TRACE);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $msg
	 */
	public static function debug($msg) {
		self::add($msg, self::DEBUG);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $msg
	 */
	public static function error($msg) {
		self::add($msg, self::ERROR);
	}

	/**
	 * 记录日志信息，但不写入文件
	 * @param string $msg	     日志信息
	 * @param const  $logType 日志类别
	 */
	public static function add($msg, $logType = self::INFO) {
		self::$logs[] = self::build($msg, $logType);
		count(self::$logs) > 100 && self::flush();
	}

	/**
	 * 直将将日志写入文件
	 * @param $msg 		日志信息
	 * @param $logType	日志类别
	 * @param $type		记录类别
	 * @param $dst		日志被记录于何处
	 * @param $header	其它信息
	 * @return boolean
	 */
	public static function log($msg, $logType = self::INFO, $type = self::FILE, $dst = '', $header = '') {
		$type = in_array($type, self::$msgType) ? $type : self::FILE;
		$dst = empty($dst) ? self::getFileName() : $dst;
		error_log(self::build($msg, $logType), $type, $dst, $header);
		return true;
	}

	/**
	 * 将记录的日志列表信息写入文件
	 * @param string $type 日志类别
	 * @param string $dst  日志被记录于何处
	 * @param string $header 其它信息
	 * @return boolean
	 */
	public static function flush($type = self::FILE, $dst = '', $header = '') {
		if (self::$logs) {
			$type = in_array($type, self::$msgType) ? $type : self::FILE;
			$dst = empty($dst) ? self::getFileName() : $dst;
			error_log(join("", self::$logs), $type, $dst, $header);
			self::$logs = array();
			return true;
		}
		return false;
	}

	/*
	 * 清空日志文件
	 */
	public static function clearFiles($time = 0) {
		if (!is_int($time) || 0 > intval($time) || !is_dir(LOG_PATH)) {
			return false;
		}
		$dir = dir(LOG_PATH);
		while (false != ($file = $dir->read())) {
			$file = LOG_PATH . $file;
			is_file($file) ? (microtime(true) - filectime($file)) > $time && unlink($file) : '';
		}
		$dir->close();
		return true;
	}

	/**
	 * 设置日志展示类型
	 * 
	 * @param sting $type 日志展示类型(log/html)
	 */
	public static function setLogDisplay($type = self::LOG) {
		self::$logDisplay = $type;
	}

	/**
	 * 取得日志文件名
	 */
	private static function getFileName() {
		self::createFolder(LOG_PATH);
		$size = 1024 * 50;
		$filename = LOG_PATH . date("Y_m_d") . '.' . self::$logDisplay;
		if (is_file($filename) && $size < filesize($filename)) {
			for ($i = 100; $counter = 100 - $i, $i >= 0; $i--) {
				$filename = LOG_PATH . date("Y_m_d_{$counter}") . '.' . self::$logDisplay;
				if (!is_file($filename) || (is_file($filename) && $size > filesize($filename))) break;
			}
		}
		return $filename;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $path
	 */
	private static function createFolder($path) {
		!is_dir($path) && mkdir($path, 0777, true);
	}

	/**
	 * Enter description here ...
	 * 
	 * @return string
	 */
	private static function info() {
		$info = '';
		foreach (debug_backtrace() as $info) {
			if (in_array($info['function'], array('log', 'add'))) {
				$info = 'This Log was recorded in ' . $info['file'] . ' on line ' . $info['line'] . ' [' . date('Y-m-d H:i', time()) . ']';
				break;
			}
		}
		return $info;
	}

	/**
	 * 组装日志信息
	 * 
	 * @param string $msg	     日志信息
	 * @param const  $logType 日志类别
	 * @return string
	 */
	private static function build($msg, $logType = self::INFO) {
		$result = '';
		switch (self::$logDisplay) {
			case 'log':
				$result = self::buildLog(var_export($msg, true), $logType);
				break;
			case '':
				$result = self::buildHtml(var_export($msg, true), $logType);
				break;
			default:
				break;
		}
		return $result;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $msg
	 * @param string $logType
	 * @return string
	 */
	private static function buildHtml($msg, $logType = self::INFO) {
		$msg = stripslashes(str_replace(array("\r\n", "\r", "\n\r", "\n"), "<br/>", $msg));
		return '<span><strong>##' . strtoupper($logType) . '##</strong>' . self::info() . "<br/>The Detail Message:" . $msg . "</span><br/><br/>";
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $msg
	 * @param string $logType
	 * @return string
	 */
	private static function buildLog($msg, $logType = self::INFO) {
		$msg = stripslashes(str_replace("<br/>", "\r\n", $msg));
		return '##' . strtoupper($logType) . '##' . self::info() . "\r\nThe Detail Message:" . $msg . "\r\n\r\n";
	}

}

	