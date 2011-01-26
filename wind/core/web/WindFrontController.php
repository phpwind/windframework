<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

L::import('WIND:core.web.AbstractWindServer');
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

	const WIND_APPLICATION = 'WIND:core.web.WindWebApplication';

	const WIND_CONFIG = 'WIND:core.config.WindSystemConfig';

	const WIND_FACTORY = 'WIND:core.factory.WindComponentFactory';

	protected $windSystemConfig = null;

	protected $windFactory = null;

	protected $windUrlPathHelper = null;

	/**
	 * Enter description here ...
	 * 
	 * @param WindConfig $windConfig
	 * @param WindFactory $windFactory
	 */
	public function __construct($appName = '', $config = array()) {
		parent::__construct();
		$this->initWindConfig($appName, $config);
		$this->initWindFactory();
	}

	/**
	 * Enter description here ...
	 */
	protected function initWindFactory() {
		$classesDefinition = $this->getWindConfig()->getFactory(WindSystemConfig::WEB_APP_FACTORY_CLASS_DEFINITION);
		$classesDefinitions = $this->getWindConfig()->getConfig($classesDefinition);
		$factoryClass = L::import($this->getWindConfig()->getFactory(WindSystemConfig::CLASS_PATH));
		if (!class_exists($factoryClass)) {
			throw new WindException($factoryClass, WindException::ERROR_CLASS_NOT_EXIST);
		}
		
		$this->windFactory = new $factoryClass($classesDefinitions);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $appName
	 * @param string $config
	 */
	protected function initWindConfig($appName, $config) {
		L::import('WIND:core.config.parser.WindConfigParser');
		$configParser = new WindConfigParser();
		$this->windSystemConfig = new WindSystemConfig($config, $configParser, $appName);
		//TODO register all apps
		

		L::register($this->getWindConfig()->getRootPath(), $this->getWindConfig()->getAppName());
	}

	/* (non-PHPdoc)
	 * @see wind/core/base/WindServer#process()
	 */
	protected function process(WindHttpRequest $request, WindHttpResponse $response) {
		//TODO set logger
		try {
			$applicationClass = L::import($this->getWindConfig()->getAppClass());
			if (!class_exists($applicationClass)) {
				throw new WindException($applicationClass, WindException::ERROR_CLASS_NOT_EXIST);
			}
			
			$application = $this->getWindFactory()->createInstance($applicationClass);
			
			$this->windFactory->request = $request;
			$this->windFactory->response = $response;
			$this->windFactory->application = $application;
			
			$request->setAttribute(self::WIND_APPLICATION, $application);
			$request->setAttribute(self::WIND_CONFIG, $this->windSystemConfig);
			$request->setAttribute(self::WIND_FACTORY, $this->windFactory);
			
			if (!($application instanceof IWindApplication)) {
				throw new WindException($applicationClass, WindException::ERROR_CLASS_TYPE_ERROR);
			}
			
			$filterChain = $this->getFilterChain();
			$filterChain->setCallBack(array($application, 'processRequest'), array($request, $response));
			$filterChain->getHandler()->handle($request, $response);
		
		} catch (WindException $exception) {
			//TODO addLog
			$response->sendError(WindHttpResponse::SC_NOT_FOUND, $exception->getMessage());
		}
	}

	/**
	 * Enter description here ...
	 * @return WindFilterChain
	 */
	private function getFilterChain() {
		$filterChainPath = $this->getWindConfig()->getFilters(WindSystemConfig::CLASS_PATH);
		$filterChainClass = L::import($filterChainPath);
		if (!class_exists($filterChainClass)) {
			throw new WindException($filterChainClass, WindException::ERROR_CLASS_NOT_EXIST);
		}
		return $this->getWindFactory()->createInstance($filterChainClass, array($this->getWindConfig()->getFilters()));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindServer::afterProcess()
	 */
	protected function afterProcess() {
		restore_error_handler();
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
		return $this->windSystemConfig;
	}

	/**
	 * @return WindFactory $windFactory
	 */
	public function getWindFactory() {
		return $this->windFactory;
	}

}