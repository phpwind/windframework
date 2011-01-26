<?php

L::import('WIND:core.filter.WindHandlerInterceptor');
L::import('WIND:component.log.WindLogger');
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindLoggerListener extends WindHandlerInterceptor {

	private $logger;

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		$this->logger->info($this->getLogMessage());
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		$this->logger->info($this->getLogMessage());
	}

	/**
	 * 获得调用的堆栈信息中回调的方法信息
	 * 
	 * @return string
	 */
	private function getLogMessage() {
		$num = 0;
		$method = ''
		$info = array();
		foreach (debug_backtrace(false) as $traceKey => $trace) {
			$class = isset($trace['class']) ? $trace['class'] : '';
			if ($class == 'WindLogger' || $class == '') continue;
			$function = isset($trace['function']) ? $trace['function'] : '';
			($class == 'WindClassProxy' && $function == '__call') && $method = trim($trace['args'][0]);
			if ($function != $method) continue;
			$info[$num] = $trace['file'] . '(' . $trace['line'] . '): ';
			$args = array_map(array($this, 'buildArg'), $trace['args']);
			$info[$num] .= $class . $trace['type'] . $function . '(' . implode(',', $args) . ')';
			$num ++;
		}
		return implode("\r\n", $info);
	}
	
	/**
	 * 将参数进行类型判断返回类型
	 * 如果类型是字符串，则直接返回该字符串
	 *
	 * @param mixed $arg
	 * @return string
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
}

?>