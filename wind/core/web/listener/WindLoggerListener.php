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

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		$logger = $this->getLogger();
		if ($logger instanceof WindLogger) {
			$logger->info($this->getLogMessage(func_get_args()));
		}
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		$logger = $this->getLogger();
		if ($logger instanceof WindLogger) {
			$logger->info($this->getLogMessage(func_get_args()));
		}
	}

	/**
	 * @return WindLogger
	 */
	private function getLogger() {
		if (!isset($this->logger)) {
			$factory = $this->request->getAttribute(WindFrontController::WIND_FACTORY);
			$this->logger = $factory->getInstance('windLogger');
		}
		return $this->logger;
	}

	/**
	 * 获得调用的堆栈信息中回调的方法信息
	 *
	 * @param array $args
	 * @return string
	 */
	private function getLogMessage($args) {
		$method = '';
		$info = array();
		foreach (debug_backtrace(false) as $traceKey => $trace) {
			$class = isset($trace['class']) ? $trace['class'] : '';
			if (in_array($class, array('', 'WindLogger', __CLASS__, 'WindHandlerInterceptor'))) continue;
			$function = isset($trace['function']) ? $trace['function'] : '';
			($class == 'WindClassProxy' && $function == '__call') && $method = trim($trace['args'][0]);
			if ($function != $method) continue;
			$info[] = ' #[caller]: ' . (isset($trace['file']) ? addslashes($trace['file']) : 'null') . '(' . (isset($trace['line']) ? $trace['line'] : 'null') . '): ';
			break;
		}
		list($class, $method) = $this->event;
		$args = array_map(array($this, 'buildArg'), $args);
		$info[] = ' #[excute]: ' . $class . '->' . $method . '(' . implode(', ', $args) . ')';
		$info[] = ' #[output]: ' . $this->buildArg($this->result);
		return "<br/>" . implode("\r\n", $info);
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