<?php
Wind::import('COM:fitler.WindHandlerInterceptor');
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class WindLoggerListener extends WindHandlerInterceptor {

	/**
	 * Enter description here ...
	 * @param WindHttpRequest $request
	 */
	public function __construct($request) {
		$this->request = $request;
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		$logger = $this->getLogger();
		if ($logger instanceof WindLogger) {
			$logger->info($this->getPreLogMessage(func_get_args()));
		}
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
		$logger = $this->getLogger();
		if ($logger instanceof WindLogger) {
			$logger->info($this->getPostLogMessage(func_get_args()));
		}
	}

	/**
	 * @return WindLogger
	 */
	private function getLogger() {
		if (!isset($this->logger)) {
			$factory = $this->request->getAttribute(WindFrontController::WIND_FACTORY);
			$this->logger = $factory->getInstance(COMPONENT_LOGGER);
		}
		return $this->logger;
	}

	private function getPreLogMessage($args) {
		$log = $this->getLogMessage($args);
		$log['caller'] = ' #[caller]: ' . $log['caller'];
		$log['excute'] = ' #[excute-begin]: ' . $log['excute'];
		$message = 'Begin ' . $this->event[0] . '->' . $this->event[1];
		return "{$message}<br/>" . implode("\r\n", $log) . '<br/>';
	}

	private function getPostLogMessage($args) {
		$log = $this->getLogMessage($args);
		$log['caller'] = ' #[caller]: ' . $log['caller'];
		$log['excute'] = ' #[excute-end]: ' . $log['excute'];
		$log['output'] = ' #[output]: ' . $this->buildArg($this->result);
		$message = 'End ' . $this->event[0] . '->' . $this->event[1];
		return "{$message}<br/>" . implode("\r\n", $log) . '<br/>';
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
		$flag = false;
		foreach (debug_backtrace(false) as $traceKey => $trace) {
			$class = isset($trace['class']) ? $trace['class'] : '';
			if (in_array($class, array('', 'WindLogger', __CLASS__, 'WindHandlerInterceptor')))
				continue;
			$function = isset($trace['function']) ? $trace['function'] : '';
			($class == 'WindClassProxy' && $function == '__call') && $method = trim(
				$trace['args'][0]);
			($function == $method) && $flag = true;
			if (!isset($trace['file']))
				continue;
			$info['caller'] = addslashes($trace['file']) . '(' . $trace['line'] . '): ';
			break;
		}
		list($class, $method) = $this->event;
		$args = array_map(array($this, 'buildArg'), $args);
		$info['excute'] = $class . '->' . $method . '(' . implode(', ', $args) . ')';
		return $info;
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