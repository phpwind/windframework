<?php
/**
 * 抽象的前端控制器接口，通过集成该接口可以实现以下职责
 * 职责定义：
 * 接受客户请求
 * 处理请求
 * 向客户端发送响应
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindFrontController implements IWindFrontController {
	/**
	 * 框架系统配置信息资源地址，只接受php格式配置
	 */
	const WIND_COMPONENT_CONFIG_RESOURCE = 'WIND:components_config';
	/**
	 * @var WindHttpRequest
	 */
	private $request;
	/**
	 * @var WindHttpResponse
	 */
	private $response;
	/**
	 * @var WindSystemConfig
	 */
	protected $windSystemConfig = null;
	/**
	 * @var WindFactory
	 */
	protected $windFactory = null;
	
	private $config;

	/**
	 * @param WindConfig $windConfig
	 * @param WindFactory $windFactory
	 */
	public function __construct($config = '') {
		$this->request = new WindHttpRequest();
		$this->response = new WindHttpResponse();
		$this->config = $config;
	}

	/**
	 * 执行操作
	 * @throws Exception
	 */
	public function run() {
		$this->beforeProcess();
		$appName = $this->windSystemConfig->getAppClass();
		/* @var $application WindModule */
		$application = $this->windFactory->getInstance($appName);
		if ($application === null) {
			throw new WindException($appName . '[core.web.WindFrontController.process]', 
				WindException::ERROR_CLASS_NOT_EXIST);
		}
		$routerAlias = $this->windSystemConfig->getRouterClass();
		$application->setDelayAttributes(array('handlerAdapter' => array('ref' => $routerAlias)));
		
		if (null !== ($filterChain = $this->getFilterChain())) {
			$filterChain->setCallBack(array($application, 'processRequest'), array());
			$filterChain->getHandler()->handle($this->request, $this->response);
		} else
			$application->processRequest($this->request, $this->response);
		$this->afterProcess();
	}

	/**
	 * @return WindFilterChain
	 */
	protected function getFilterChain() {
		if (!($filters = $this->windSystemConfig->getFilters())) return null;
		if (!($filterChainPath = $this->windSystemConfig->getFilterClass())) return null;
		return $this->getWindFactory()->createInstance($filterChainPath, array($filters));
	}

	/**
	 * 预处理Process方法
	 */
	protected function beforeProcess() {
		try {
			ob_start();
			$configPath = Wind::getRealPath(self::WIND_COMPONENT_CONFIG_RESOURCE);
			$this->windFactory = new WindFactory(@include ($configPath));
			$this->windSystemConfig = new WindSystemConfig($this->config, Wind::getAppName(), $this->windFactory);
			set_error_handler(array($this, 'errorHandle'));
			set_exception_handler(array($this, 'exceptionHandle'));
		} catch (Exception $e) {
			Wind::log('System failed to initialize. (' . $e->getMessage() . ')', WindLogger::LEVEL_INFO, 'wind.core');
			throw new WindException('System failed to initialize.' . $e->getMessage());
		}
	}

	/**
	 * 错误处理句柄
	 * @param string $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 */
	final public function errorHandle($errno, $errstr, $errfile, $errline) {
		if ($errno & error_reporting()) {
			restore_error_handler();
			restore_exception_handler();
			$header = $message = $trace = '';
			$header = $errstr;
			if (IS_DEBUG) {
				$message = $errstr . '(' . $errfile . ' : ' . $errline . ')';
				$_trace = debug_backtrace();
				foreach ($_trace as $key => $value) {
					if (!isset($value['file']) || !isset($value['line']) || !isset($value['function'])) continue;
					$trace .= "#$key {$value['file']}({$value['line']}): ";
					if (isset($value['object']) && is_object($value['object'])) $trace .= get_class($value['object']) . '->';
					$trace .= "{$value['function']}()\r\n";
				}
			}
			$this->displayMessage($header, $message, $trace);
		}
	}

	/**
	 * 异常处理句柄
	 * @param Exception $exception
	 */
	final public function exceptionHandle($exception) {
		restore_error_handler();
		restore_exception_handler();
		$header = $message = $trace = '';
		$header = $exception->getMessage();
		if (IS_DEBUG) {
			$message = $exception->getMessage() . '(' . $exception->getFile() . ' : ' . $exception->getLine() . ')';
			$trace = $exception->getTraceAsString();
		}
		$this->displayMessage($header, $message, $trace);
	}

	/**
	 * @param string $header
	 * @param string $message
	 * @param string $trace
	 */
	public function displayMessage($header, $message = '', $trace = '') {
		$_tmp = "<h4>$header</h4>";
		$_tmp .= "<p>$message</p>";
		$_tmp .= "<pre>$trace</pre>";
		$this->getResponse()->sendError(500, $_tmp);
		$this->getResponse()->sendResponse();
	}

	/**
	 * 后处理Process方法
	 */
	protected function afterProcess() {
		ob_end_flush();
		$this->response->sendResponse();
	}

	/**
	 * @return WindsystemConfig
	 */
	public function getWindSystemConfig() {
		return $this->windSystemConfig;
	}

	/**
	 * @return WindComponentFactory
	 */
	public function getWindFactory() {
		return $this->windFactory;
	}

	/**
	 * @return WindHttpRequest
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @return WindHttpResponse
	 */
	public function getResponse() {
		return $this->response;
	}
}