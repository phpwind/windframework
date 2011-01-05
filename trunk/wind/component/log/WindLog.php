<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
defined ( 'LOG_PATH' ) or define ( 'LOG_PATH', './log/' );
defined ( 'LOG_DISPLAY_TYPE' ) or define ( 'LOG_DISPLAY_TYPE', 'log' );
/**
 * 日志记录
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WindLog {
	/*错误类型*/
	const ERROR = 'error';
	const TRACE = 'trace';
	const INFO = 'info';
	const DB = 'db';
	/*写入日志类别*/
	private static $msgType = array ('system' => 0, 'email' => 1, 'tcp' => 2, 'file' => 3 );
	
	private static $logs = array ();
	/**
	 * 记录日志信息，但不写入文件
	 * @param string $msg	     日志信息
	 * @param const  $logType 日志类别
	 */
	public static function add($msg, $logType = self::INFO) {
		self::$logs [] = self::build ( $msg, $logType );
		count ( self::$logs ) > 100 && self::flush ();
	}
	
	/**
	 * 直将将日志写入文件
	 * @param $msg 		日志信息
	 * @param $logType	日志类别
	 * @param $type		记录类别
	 * @param $dst		日志被记录于何处
	 * @param $header	其它信息
	 */
	public static function log($msg, $logType = self::INFO, $type = 'file', $dst = '', $header = '') {
		$type = in_array ( $type, self::$msgType ) ? $type : 'file';
		$dst = empty ( $dst ) ? self::getFileName () : $dst;
		error_log ( self::build ( $msg, $logType ), self::$msgType [$type], $dst, $header );
	}
	
	/**
	 * 将记录的日志列表信息写入文件
	 * @param string $type 日志类别
	 * @param string $dst  日志被记录于何处
	 * @param string $header 其它信息
	 */
	public static function flush($type = 'file', $dst = '', $header = '') {
		if (self::$logs) {
			$type = in_array ( $type, self::$msgType ) ? $type : 'file';
			$dst = empty ( $dst ) ? self::getFileName () : $dst;
			error_log ( join ( "", self::$logs ), self::$msgType [$type], $dst, $header );
			self::$logs = array ();
		}
	}
	
	/*
	 * 清空日志文件
	 */
	public static function clearFiles($time = 0) {
		if (! is_int ( $time ) || 0 > intval ( $time ) || ! is_dir ( LOG_PATH )){
			return false;
		}
		$dir = dir ( LOG_PATH );
		while ( false != ($file = $dir->read ()) ) {
			$file = LOG_PATH . $file;
			is_file ( $file ) ? (microtime ( true ) - filectime ( $file )) > $time && unlink ( $file ) : '';
		}
		$dir->close ();
		return true;
	}
	
	/**
	 * 取得日志文件名
	 */
	private static function getFileName() {
		self::createFolder ( LOG_PATH );
		$size = 1024 * 50;
		$filename = LOG_PATH . date ( "Y_m_d" ) . '.' . LOG_DISPLAY_TYPE;
		if (is_file ( $filename ) && $size < filesize ( $filename )) {
			for($i = 100; $counter = 100 - $i, $i >= 0; $i --) {
				$filename = LOG_PATH . date ( "Y_m_d_{$counter}" ) . '.' . LOG_DISPLAY_TYPE;
				if (! is_file ( $filename ) || (is_file ( $filename ) && $size > filesize ( $filename )))
					break;
			}
		}
		return $filename;
	}
	
	private static function createFolder($path) {
		! is_dir ( $path ) && mkdir ( $path, 0777, true );
	}
	
	/**
	 * 组装日志信息
	 * @param string $msg	     日志信息
	 * @param const  $logType 日志类别
	 * @return string
	 */
	private static function build($msg, $logType = self::INFO) {
		return 'log' == LOG_DISPLAY_TYPE ? self::buildLog ( var_export ( $msg, true ), $logType ) : self::buildHtm ( var_export ( $msg, true ), $logType );
	}
	
	private static function info() {
		$info = '';
		foreach ( debug_backtrace () as $info ) {
			if (in_array ( $info ['function'], array ('log', 'add' ) )) {
				$info = 'This Log was recorded in ' . $info ['file'] . ' on line ' . $info ['line'] . ' [' . date ( 'Y-m-d H:i', time () ) . ']';
				break;
			}
		}
		return $info;
	}
	
	private static function buildHtm($msg, $logType = self::INFO) {
		$msg = stripslashes ( str_replace ( array ("\r\n", "\r", "\n\r", "\n" ), "<br/>", $msg ) );
		return '<span>【<strong>' . strtoupper ( $logType ) . '</strong>】' . self::info () . "<br/>The Detail Message:" . $msg . "</span><br/><br/>";
	}
	
	private static function buildLog($msg, $logType = self::INFO) {
		$msg = stripslashes ( str_replace ( "<br/>", "\r\n", $msg ) );
		return '【' . strtoupper ( $logType ) . '】' . self::info () . "\r\nThe Detail Message:" . $msg . "\r\n\r\n";
	}

}

	