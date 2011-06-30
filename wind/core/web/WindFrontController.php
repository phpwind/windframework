<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */
Wind::import('WIND:core.AbstractWindServer');
/**
 * 抽象的前端控制器接口，通过集成该接口可以实现以下职责
 * 
 * 职责定义：
 * 接受客户请求
 * 处理请求
 * 向客户端发送响应
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindFrontController extends AbstractWindServer {
	const WIND_CONFIG = 'WIND:core.config.WindSystemConfig';
	const WIND_FACTORY = 'WIND:core.factory.WindComponentFactory';
	const COMPONENTS_CONFIG = 'WIND:components_config.php';
	/**
	 * @var WindSystemConfig
	 */
	protected $windSystemConfig = null;
	protected $windFactory = null;
	protected $windErrorHandler = null;

	/**
	 * @param WindConfig $windConfig
	 * @param WindFactory $windFactory
	 */
	public function __construct($appName, $config = array()) {
		parent::__construct();
		$this->initWindConfig($appName, $config);
		$this->initWindFactory();
	}

	/**
	 * 初始化类工厂
	 */
	protected function initWindFactory() {
		$configPath = Wind::getRealPath(self::COMPONENTS_CONFIG);
		$classesDefinitions = $this->getWindConfig()->getConfigParser()->parse($configPath, 'components', WIND_CONFIG_CACHE);
		$factoryClass = Wind::import(self::WIND_FACTORY);
		if (!class_exists($factoryClass)) {
			throw new WindException($factoryClass, WindException::ERROR_CLASS_NOT_EXIST);
		}
		$this->windFactory = new $factoryClass($classesDefinitions);
	}

	/**
	 * @param string $appName
	 * @param string $config
	 */
	protected function initWindConfig($appName, $config) {
		Wind::import('WIND:core.config.parser.WindConfigParser');
		Wind::import('WIND:core.config.WindSystemConfig');
		$configParser = new WindConfigParser();
		$this->windSystemConfig = new WindSystemConfig($config, $configParser, ($appName ? $appName : 'default'));
		Wind::register($this->getWindConfig()->getRootPath(), $this->getWindConfig()->getAppName());
			//TODO register all apps
	}

	/* (non-PHPdoc)
	 * @see wind/core/base/WindServer#process()
	 */
	protected function process(WindHttpRequest $request, WindHttpResponse $response) {
		$this->getWindFactory()->request = $request;
		$this->getWindFactory()->response = $response;
		$request->setAttribute(self::WIND_CONFIG, $this->windSystemConfig);
		$request->setAttribute(self::WIND_FACTORY, $this->windFactory);
		$appName = $this->getWindConfig()->getAppClass();
		$application = $this->getWindFactory()->getInstance($appName);
		if (null === $application) {
			throw new WindException('application', WindException::ERROR_CLASS_NOT_EXIST);
		}
		$this->getWindFactory()->application = $application;
		if (null !== $filterChain = $this->getFilterChain()) {
			$filterChain->setCallBack(array($application, 'processRequest'), array());
			$filterChain->getHandler()->handle($request, $response);
		} else
			$application->processRequest();
	}

	/**
	 * @return WindFilterChain
	 */
	private function getFilterChain() {
		$filterChainPath = $this->getWindConfig()->getFilters(WindSystemConfig::CLASS_PATH);
		return $this->getWindFactory()->createInstance($filterChainPath, array($this->getWindConfig()->getFilters()));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindServer::beforeProcess()
	 */
	protected function beforeProcess(WindHttpRequest $request, WindHttpResponse $response) {
		Wind::import('WIND:core.web.WindErrorHandler');
		set_error_handler(array(new WindErrorHandler(), 'errorHandle'), error_reporting());
		set_exception_handler(array(new WindErrorHandler(), 'exceptionHandle'));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindServer::afterProcess()
	 */
	protected function afterProcess(WindHttpRequest $request, WindHttpResponse $response) {
		Wind::getLogger()->flush();
		restore_error_handler();
		restore_exception_handler();
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function doPost(WindHttpRequest $request, WindHttpResponse $response) {
		$this->process($request, $response);
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function doGet(WindHttpRequest $request, WindHttpResponse $response) {
		$this->process($request, $response);
	}

	/**
	 * @return WindSystemConfig $windConfig
	 */
	public function getWindConfig() {
		if ($this->windSystemConfig instanceof WindConfig)
			return $this->windSystemConfig;
		else
			throw new WindException(get_class($this) . '->windSystemConfig', WindException::ERROR_CLASS_TYPE_ERROR);
	}

	/**
	 * @return WindFactory $windFactory
	 */
	public function getWindFactory() {
		if ($this->windFactory instanceof WindFactory)
			return $this->windFactory;
		else
			throw new WindException(get_class($this) . '->windFactory', WindException::ERROR_CLASS_TYPE_ERROR);
	}
}