<?php
/**Your pack list
*IWindApplication=>WIND:core.base.IWindApplication
*IWindConfig=>core\base\IWindConfig.php
*WindBaseAction=>core\base\WindBaseAction.php
*WindConfig=>core\base\WindConfig.php
*WindServlet=>core\base\WindServlet.php
*WindAction=>core\WindAction.php
*WindConfigParser=>core\WindConfigParser.php
*WindController=>core\WindController.php
*WindError=>core\WindError.php
*WindFrontController=>core\WindFrontController.php
*WindModelAndView=>core\WindModelAndView.php
*WindSystemConfig=>core\WindSystemConfig.php
*WindWebApplication=>core\WindWebApplication.php
*WindXMLConfig=>core\WindXMLConfig.php
*WindModule=>utility\container\WindModule.php
*IWindFactory=>utility\factory\IWindFactory.php
*WindPackge=>utility\WindPackge.php
*xml=>utility\xml\xml.php
*
*/

Interface WindApplicationImpl {
	public function init();
	public function processRequest($request, $response);
	public function destory();
}
interface WindConfigImpl {
	const APP = 'app';
	const APPNAME = 'appName';
	const APPROOTPATH = 'rootPath';
	const APPCONFIG = 'appConfig';
	const APPAUTHOR = 'appAuthor';
	const ISOPEN = 'isOpen';
	const DESCRIBE = 'describe';
	const FILTERS = 'filters';
	const FILTER = 'filter';
	const FILTERNAME = 'filterName';
	const FILTERPATH = 'filterPath';
	const TEMPLATE = 'template';
	const TEMPLATEDIR = 'templateDir';
	const COMPILERDIR = 'compileDir';
	const CACHEDIR = 'cacheDir';
	const TEMPLATEEXT = 'templateExt';
	const ENGINE = 'engine';
	const URLRULE = 'urlRule';
	const ROUTERPASE = 'routerPase';
	
	const MERGEARRAY = "app,filters";
}
L::import('WIND:core.WindModelAndView');
abstract class WindBaseAction {
	
	protected $mav = null;
	
	protected $view = null;
	public function __construct() {
		$this->view = new stdClass();
		$this->mav = new WindModelAndView();
	}
	public function beforeAction() {}
	public function afterAction() {}
	
	public function setDefaultViewTemplate($default) {
		$this->getModelAndView()->setViewName($default);
	}
	
	public function getModelAndView() {
		return $this->mav;
	}
}
L::import('WIND:utility.container.WindModule');
abstract class WindConfig extends WindModule {
	
	public function parse($configSystem, $configCustom) {}
	
	public function parseXML($configSystem, $configCustom) {}
	
	public function getConfig($configName) {}
}
L::import('WIND:component.exception.WindException');
L::import('WIND:component.request.WindHttpRequest');
L::import('WIND:component.request.WindHttpResponse');
abstract class WindServlet {
	protected $request = null;
	protected $response = null;
	const METHOD_DELETE = "DELETE";
	const METHOD_HEAD = "HEAD";
	const METHOD_GET = "GET";
	const METHOD_OPTIONS = "OPTIONS";
	const METHOD_POST = "POST";
	const METHOD_PUT = "PUT";
	const METHOD_TRACE = "TRACE";
	protected function __construct() {
		try {
			$this->request = WindHttpRequest::getInstance();
			$this->response = $this->request->getResponse();
		
		} catch (Exception $exception) {
			throw new WindException('init action servlet failed!!');
		}
	}
	public function run() {
		if ($this->request === null || $this->response === null) throw new WindException('init action servlet failed!!');
		$this->service($this->request, $this->response);
		$this->response->sendResponse();
	}
	abstract function process($request, $resopnse);
	
	protected function service(WindHttpRequest $request, WindHttpResponse $response) {
		$method = $request->getRequestMethod();
		
		if (strcasecmp($method, self::METHOD_GET) == 0) {
			$this->doGet($request, $response);
		
		} else if (strcasecmp($method, self::METHOD_POST) == 0) {
			$this->doPost($request, $response);
		
		} else if (strcasecmp($method, self::METHOD_PUT) == 0) {
			$this->doPut($request, $response);
		
		} else if (strcasecmp($method, self::METHOD_DELETE) == 0) {
			$this->doDelete($request, $response);
		
		} else if (strcasecmp($method, self::METHOD_HEAD) == 0) {
		} else if (strcasecmp($method, self::METHOD_OPTIONS) == 0) {
		} else if (strcasecmp($method, self::METHOD_TRACE) == 0) {
		} else {
			$errMsg = 'your request method is not supported!!!';
			$response->sendError(WindHttpResponse::SC_METHOD_NOT_ALLOWED, $errMsg);
		}
	}
	
	protected function doPost(WindHttpRequest $request, WindHttpResponse $response) {
		$protocol = $request->getProtocol();
		$msg = "The method post is not supported.";
		if (!$protocol || (strpos($protocol, '1.1')) !== false) {
			$response->sendError(WindHttpResponse::SC_METHOD_NOT_ALLOWED, $msg);
		} else
			$this->process($request, $response);
	}
	
	protected function doGet(WindHttpRequest $request, WindHttpResponse $response) {
		$protocol = $request->getProtocol();
		$msg = "The method get is not supported.";
		if (!$protocol || (strpos($protocol, '1.1')) !== false) {
			$response->sendError(WindHttpResponse::SC_METHOD_NOT_ALLOWED, $msg);
		} else
			$this->process($request, $response);
	}
	
	protected function doPut(WindHttpRequest $request, WindHttpResponse $response) {
		$this->process($request, $response);
	}
	
	protected function doDelete(WindHttpRequest $request, WindHttpResponse $response) {
		$this->process($request, $response);
	}
	
	protected function doTrace(WindHttpRequest $request, WindHttpResponse $response) {
	}
	
	protected function doOptions(WindHttpRequest $request, WindHttpResponse $response) {
	}
	
	protected function doHead(WindHttpRequest $request, WindHttpResponse $response) {
	}
}
L::import('WIND:core.base.WindBaseAction');
class WindAction extends WindBaseAction {
	
	public function run($request, $response) {}
}
L::import("WIND:core.base.impl.WindConfigImpl");
class WindConfigParser implements WindConfigImpl {
	private $defaultPath = '';
	private $defaultConfig = '';
	private $userAppPath = '';
	private $userAppConfig = '';
	private $globalAppsPath = '';
	private $globalAppsConfig = '';
	private $parserEngine = '';
	private $currentApp = '';
	private $configExt = array('xml', 'properpoties', 'ini');
	private $encoding = 'gbk';
	
	public function __construct($request, $outputEncoding = 'gbk') {
		$this->setGlobalAppsPath(realpath(CONFIG_CACHE_PATH));
		$this->setUserAppPath(realpath(dirname($request->getServer('SCRIPT_FILENAME'))));
		$this->setDefaultPath(realpath(WIND_PATH));
		$outputEncoding && $this->encoding = $outputEncoding;
		$this->currentApp = W::getCurrentApp();
	}
	
	public function setDefaultPath($defaultPath) {
		(file_exists($defaultPath)) && $this->defaultPath = rtrim(rtrim($defaultPath, '/'), '\\');
		$this->defaultConfig = $this->defaultPath . '/wind_config.xml';
	}
	
	public function setUserAppPath($userAppPath) {
		(file_exists($userAppPath)) && $this->userAppPath = rtrim(rtrim($userAppPath, '/'), '\\');
	}
	
	public function setGlobalAppsPath($globalAppsPath) {
		$this->globalAppsPath = rtrim(rtrim($globalAppsPath, '/'), '\\');
		$this->globalAppsConfig = $globalAppsPath . '/config.php';
		
	}
	
	public function parser() {
		$oConfig = $this->isExist($this->userAppPath, true);
		($oConfig === false) && $oConfig = $this->defaultConfig;
		
		
		$config = $this->isCached();
		if ($config !== false) {
			$appName = $config[WindConfigImpl::APPNAME];
			$cacheP = $config[WindConfigImpl::APPCONFIG];
			if ((filemtime($oConfig) < filemtime($cacheP)) && filemtime($this->defaultConfig) < filemtime($cacheP)) {
				echo 'include';
				return true;
			}
		}
		return $this->parserConfig();
	}
	
	private function parserConfig() {
		$uConfig = $dConfig = array();
		$dConfig = $this->parserXML($this->defaultConfig, $this->encoding, false);
		$oConfigP = ($this->userAppConfig != '') ? $this->userAppConfig : $this->isExist($this->userAppPath, true);
		($oConfigP !== false) && $uConfig = $this->switchParser($oConfigP);
		return $this->mergeConfig($dConfig, $uConfig);
	}
	
	public function addAppsConfig($config) {
		$sysConfig = array();
		if (is_file($this->globalAppsConfig)) {
			include($this->globalAppsConfig);
		}
		
		$appName = isset($config[WindConfigImpl::APPNAME]) ? $config[WindConfigImpl::APPNAME] : $this->getAppName();
		$sysConfig[$appName] = $config;
		
		$this->writeover($this->globalAppsConfig, "\r\n \$sysConfig = " . $this->varExport($sysConfig) . ";\r\n");
		return $sysConfig;
	}
	
	public function getAppsConfig() {
		if (is_file($this->globalAppsConfig)) {
			include($this->globalAppsConfig);
			return $sysConfig;
		} else throw new WindException('The file "' . $this->globalAppsConfig . '" is not exists!');
	}
	
	public function mergeConfig($defaultConfig, $appConfig) {
		if (count($appConfig) == 0) $appConfig = $defaultConfig;
		$app = $appConfig[WindConfigImpl::APP];
		(!isset($app[WindConfigImpl::APPNAME]) || $app[WindConfigImpl::APPNAME] == '') && $app[WindConfigImpl::APPNAME] = $this->getAppName();
		(!isset($app[WindConfigImpl::APPROOTPATH]) || $app[WindConfigImpl::APPROOTPATH] == '') && $app[WindConfigImpl::APPROOTPATH] = $this->userAppPath;
		
		$_file = '/' . $app[WindConfigImpl::APPNAME] . '_config.php';
		if (!isset($app[WindConfigImpl::APPCONFIG])) {
			$app[WindConfigImpl::APPCONFIG] = $this->globalAppsPath . $_file;
		} else {
			$app[WindConfigImpl::APPCONFIG] = $this->getRealPath($app[WindConfigImpl::APPNAME], $app[WindConfigImpl::APPROOTPATH], $app[WindConfigImpl::APPCONFIG], $_file) . $_file;
		}
		
		$appConfig[WindConfigImpl::APP] = $app;
		$_merge = (strpos(WindConfigImpl::MERGEARRAY, ',') === false) ? array(WindConfigImpl::MERGEARRAY) : explode(',', WindConfigImpl::MERGEARRAY);
		
		foreach ($defaultConfig as $key => $value) {
			if (in_array($key, $_merge) && $appConfig[$key]) {
				!is_array($value) && $value = array($value);
				!is_array($appConfig[$key]) && $appConfig[$key] = array($appConfig[$key]);
				
				print_r($value);
				print_r($appConfig[$key]);
				$defaultConfig[$key] = array_merge($value, $appConfig[$key]);
			} else {
				($appConfig[$key]) && $defaultConfig[$key] = $appConfig[$key];
			}
		}
		
		$this->writeover($app[WindConfigImpl::APPCONFIG], "\r\n \$config = " . $this->varExport($defaultConfig) . ";\r\n");
		$this->addAppsConfig($app);
		return $app[WindConfigImpl::APPNAME];
	}
	
	private function getRealPath($nameSpace, $rootPath, $oPah) {
		if (strpos(':', $oPah) === false) {
			return L::getRealPath($nameSpace . ':' . $oPah . '.*', '', '', $rootPath);
		} else {
			return L::getRealPath($oPah . '.*', '', '', $rootPath);
		}
	}
	
	public function switchParser($configPath, $isCheck = true) {
		switch($this->parserEngine) {
			case 'XML':
				return $this->parserXML($configPath, $this->encoding, $isCheck);
				break;
			case 'PHP':
				include($configPath);
				return $config;
				break;
			default:
				throw new WindException('The Config ' . $configPath . ' cannot parsered because of the error format!');
				break;
		}
	}
	
	public function parserXML($configPath, $encoding, $isCheck = true) {
		L::import('WIND:core.WindXMLConfig');
	 $xml = new WindXMLConfig();
	 $xml->setXMLFile($configPath);
	 $xml->setOutputEncoding($encoding);
	 return $xml->getResult($isCheck);
	}
	public function parserProperties() {
		
	}
	public function parserIni() {
		
	}
	
	private function getAppName() {
		if ($this->currentApp != '') return $this->currentApp;
		$path = rtrim(rtrim($this->userAppPath, '\\'), '/');
		$_tmp = explode('/', $path);
		(!$_tmp) && $_tmp = explode('\\', $path);
		$pos = count($_tmp)-1;
		if ($_tmp[$pos]) return strtoupper($_tmp[$pos]);
	}
	
	private function isExist($path, $isSave = false) {
		foreach ($this->configExt as $ext) {
			$_temp = realpath($path . '/config.' . $ext);
			if (is_file($_temp)) {
				$this->parserEngine = strtoupper($ext);
				($isSave) && $this->userAppConfig = $_temp;
				return $_temp;
			} 
		}
		return false;
	}
	
	private function isCached() {
		if (!is_file($this->globalAppsConfig)) return false;
		include($this->globalAppsConfig);
		
		if ($this->currentApp && isset($sysConfig[$this->currentApp]) && is_file($sysConfig[$this->currentApp][WindConfigImpl::APPCONFIG])) 
				return $sysConfig[$this->currentApp];
		
		$appConfig = array();
		foreach ($sysConfig as $appName => $config) {
			if (isset($config[WindConfigImpl::APPROOTPATH]) && $config[WindConfigImpl::APPROOTPATH] == $this->userAppPath) {
				return $sysConfig[$appName];
			}
		}
		return false;
	}
	
	public function varExport($input, $indent = '') {
		switch (gettype($input)) {
			case 'string' :
				return "'" . str_replace(array("\\", "'"), array("\\\\", "\'"), $input) . "'";
			case 'array' :
				$output = "array(\r\n";
				foreach ($input as $key => $value) {
					$output .= $indent . "\t" . $this->varExport($key, $indent . "\t") . ' => ' . $this->varExport($value, $indent . "\t");
					$output .= ",\r\n";
				}
				$output .= $indent . ')';
				return $output;
			case 'boolean' :
				return $input ? 'true' : 'false';
			case 'NULL' :
				return 'NULL';
			case 'integer' :
			case 'double' :
			case 'float' :
				return "'" . (string) $input . "'";
		}
		return 'NULL';
	}
	
	public function writeover($fileName, $data, $method = 'rb+', $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
		
		echo $fileName;
		
		$tmpname = strtolower($fileName);
		$tmparray = array(':\/\/',"\0");
		$tmparray[] = '..';
		if (str_replace($tmparray, '', $tmpname) != $tmpname) exit('forbidden');
		
		if (!touch($fileName)) throw WindException('The path "' . $fileName . '" is unwritable!');
		$handle = fopen($fileName, $method);
		$ifLock && flock($handle, LOCK_EX);
		$writeCheck = fwrite($handle, $data);
		$method == 'rb+' && ftruncate($handle, strlen($data));
		fclose($handle);
		$ifChmod && @chmod($fileName, 0777);
		return $writeCheck;
	}
}
L::import('WIND:core.base.WindBaseAction');
class WindController extends WindBaseAction {
	
	public function __construct(WindHttpRequest $request, WindHttpResponse $response) {
		parent::__construct();
		$this->request = $request;
		$this->response = $response;
		
		
		$default = $response->getRouter()->getDefaultViewHandle();
		$this->setDefaultViewTemplate($default);
	}
	public function run() {}
}
class WindError {
	private static $error = array();
	
	public function addError($message, $clear = false) {
		self::$error[] = $message;
	}
	
	public function clearError() {
		self::$error = array();
	}
	
	public function getError() {
		return self::$error;
	}
	
	public function showMessage($message) {
	}
	static public function getInstance() {
	}
}
L::import('WIND:core.base.WindServlet');
L::import('WIND:component.exception.WindException');
L::import('WIND:component.filter.WindFilterFactory');
L::import('WIND:core.WindSystemConfig');
L::import('WIND:core.WindWebApplication');
class WindFrontController extends WindServlet {
	private $config = null;
	private static $instance = null;
	protected function __construct($config = array()) {
		parent::__construct();
		echo '<pre/>';
		$this->_initConfig($config);
		exit();
	}
	public function run() {
		if ($this->config === null) throw new WindException('init system config failed!');
		$this->beforProcess();
		$filters = $this->config->getConfig('filters');
		if (!class_exists('WindFilterFactory') || empty($filters))
			parent::run();
		else
			$this->_initFilter();
		$this->afterProcess();
	}
	protected function beforProcess() {
	}
	function process($request, $response) {
		
		$applicationController = new WindWebApplication();
		$applicationController->init();
		
		$applicationController->processRequest($request, $response);
		
		$applicationController->destory();
	}
	protected function afterProcess() {
		if (defined('LOG_RECORD')) WindLog::flush();
		restore_exception_handler();
	}
	protected function doPost($request, $response) {
		$this->process($request, $response);
	}
	protected function doGet($request, $response) {
		$this->process($request, $response);
	}
	
	private function _initFilter() {
		WindFilterFactory::getFactory()->setExecute(array(get_class($this), 'process'), $this->reuqest, $this->response);
		$filter = WindFilterFactory::getFactory()->create($this->config);
		if (is_object($filter)) $filter->doFilter($this->reuqest, $this->response);
	}
	
	private function _initConfig($config) {
		$configParser = new WindConfigParser($this->request);
		$appName = $configParser->parser();
		W::parserConfig();
		W::setCurrentApp($appName);
		$configObj = WindSystemConfig::getInstance();
		$configObj->parse((array) W::getSystemConfig(), W::getCurrentApp());
		$this->config = $configObj;
	}
	
	static public function &getInstance(array $config = array()) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($config);
		}
		return self::$instance;
	}
}
class WindModelAndView {
	private $viewName = '';
	private $path = '';
	private $isRedirect = false;
	private $redirect = '';
	private $model = array();
	private $view = null;
	private $layout = null;
	
	public function __construct($viewName = '', $redirect = '') {
		$this->setViewName($viewName);
		$this->setRedirect($redirect);
	}
	public function getModel() {
		return $this->model;
	}
	
	public function setModel($model, $key = '') {
		if (is_array($model))
			$this->setModelWithArray($model, $key);
		elseif (is_object($model))
			$this->setModelWithObject($model, $key);
		else
			$this->setModelWithSimple($model, $key);
	}
	
	public function setModelWithSimple($model, $key = '') {
		if (!$key) return;
		$this->model[$key] = $model;
	}
	
	public function setModelWithObject($model, $key = '') {
		if (!is_object($model)) return;
		if ($key && is_string($key))
			$this->model[$key] = $this->model;
		else
			$this->model += get_object_vars($model);
	}
	
	public function setModelWithArray($model, $key = '') {
		if (!is_array($model)) return;
		if ($key && is_string($key))
			$this->model[$key] = $model;
		else
			$this->model += $model;
	}
	
	public function isRedirect() {
		return $this->isRedirect;
	}
	
	public function setRedirect($redirect) {
		if (!$redirect) return;
		$this->redirect = $redirect;
		$this->isRedirect = true;
	}
	public function getRedirect() {
		return $this->redirect;
	}
	
	public function setViewName($viewName) {
		if (!$viewName) return;
		$this->viewName = $viewName;
	}
	public function getViewName() {
		return $this->viewName;
	}
	public function setView($view = null) {
		$this->view = $view;
	}
	public function getView() {
		if ($this->view == null) {
			L::import('WIND:component.viewer.WindView');
			$this->view = new WindView();
			$this->view->setViewWithObject($this);
		}
		return $this->view;
	}
	
	public function setPath($path) {
		if ($path) return;
		$this->path = $path;
	}
	public function getPath() {
		return $this->path;
	}
}
class WindSystemConfig extends WindConfig {
	private $globalConfig = array();
	private $config = array();
	private static $instance = null;
	
	public function _parse($configSystem, $configCustom = array()) {
		if (!is_array($configSystem) || !is_array($configCustom)) throw new Exception('the format of config file is error!!!');
		
		if (empty($configSystem)) throw new Exception('system config file is not exists!!!');
		
		$this->config = array_merge($configSystem, $configCustom);
		$this->system = $configSystem;
		$this->custom = $configCustom;
	}
	
	public function parse($configSystem, $current) {
		if (!is_array($configSystem) || !$current) 
			throw new Exception('the format of config file is error!!!');
		$this->system = $configSystem;
		if (!$configSystem[$current]) throw new Exception('the current app name is error!!!');
		if (is_file($configSystem[$current]['appConfig'])) {
			include ($configSystem[$current]['appConfig']);
			$this->config = $config;
		} else {
			include (L::getRealPath($configSystem[$current]['appConfig']));
			$this->config = $config;
		}
	}
	
	public function getConfig($configName) {
		if ($configName && isset($this->config[$configName])) return $this->config[$configName];
	}
	
	public function getFiltersConfig($name = '') {
		if (isset($this->config['filters'])) return !$name ? $this->config['filters'] : ($this->config['filters'][$name] ? $this->config['filters'][$name] : '');
	}
	
	public function getModulesConfig($name = '', $default = null) {
		if (!isset($this->config['modules'])) return $default;
		if (!$name) return $this->config['modules'];
		
		return $this->config['modules'][$name] ? $this->config['modules'][$name] : $default;
	}
	
	public function getRouterConfig($name = '', $default = null) {
		if (!isset($this->config['router'])) return $default;
		if (!$name) return $this->config['router'];
		
		return isset($this->config['router'][$name]) ? $this->config['router'][$name] : $default;
	}
	
	public function getRouterRule($name = '', $default = null) {
		if ($name) {
			$name = $name . 'Rule';
			return isset($this->config[$name]) ? $this->config[$name] : $default;
		} else
			throw new WindException('');
	}
	
	public function getRouterParser($name = '', $default = null) {
		if (!isset($this->config['routerParser'])) return $default;
		if (!$name) return $this->config['routerParser'];
		
		return $this->config['routerParser'][$name] ? $this->config['routerParser'][$name] : $default;
	}
	
	static public function getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
}
L::import('WIND:core.base.impl.WindApplicationImpl');
L::import('WIND:component.exception.WindException');
L::import('WIND:component.viewer.WindViewFactory');
class WindWebApplication implements WindApplicationImpl {
	
	public function init() {}
	
	public function processRequest($request, $response) {
		$router = $this->createRouter();
		$router->doParser($request, $response);
		
		
		list($action, $method) = $this->getActionHandle($request, $response);
		$action->beforeAction();
		$action->$method($request, $response);
		$action->afterAction();
		
		
		$mav = $action->getModelAndView();
		$this->processDispatch($request, $response, $mav);
	}
	
	protected function getActionHandle($request, $response) {
		list($className, $method) = $response->getRouter()->getActionHandle();
		if ($className === null || $method === null) {
			throw new WindException('can\'t create action handle.');
		}
		$class = new ReflectionClass($className);
		$action = call_user_func_array(array($class, 'newInstance'), array($request, $response));
		return array($action, $method);
	}
	
	protected function processActionForm($request, $response, $router) {
		if (($formHandle = $router->getActionFormHandle()) == null) return;
		
		
		$actionForm = W::getInstance($formHandle, array($request, $response));
		if ($actionForm->getIsValidation()) $actionForm->validation();
	}
	
	protected function processDispatch($request, $response, $mav) {
		$view = $mav->getView();
		$view->dispatch($request, $response);
		
	}
	
	public function &createRouter() {
		$configObj = WindSystemConfig::getInstance();
		$parser = $configObj->getRouterConfig('parser');
		$parserPath = $configObj->getRouterParser($parser);
		list(, $className, , $parserPath) = L::getRealPath($parserPath, true);
		L::import($parserPath);
		if (!class_exists($className)) throw new WindException('The router ' . $className . ' is not exists.');
		$router = new $className($configObj);
		return $router;
	}
	public function destory() {}
}
L::import('WIND:core.base.impl.WindConfigImpl');
L::import('WIND:utility.xml.xml');
class WindXMLConfig extends XML implements WindConfigImpl {
	private $xmlArray;
 private $childConfig;
	
	public function __construct($data = '', $encoding = 'gbk') {
		parent::__construct($data, $encoding);
		$this->xmlArray = array();
		$this->setChildConfig();
	}
 
	
	private function setChildConfig() {
		$_config = array();
		
		$_config[WindConfigImpl::ISOPEN] = WindConfigImpl::ISOPEN;
		$_config[WindConfigImpl::DESCRIBE] = WindConfigImpl::DESCRIBE;
		
		
		$_config[WindConfigImpl::APP] = array(
					WindConfigImpl::APPNAME, 
					WindConfigImpl::APPROOTPATH, 
					WindConfigImpl::APPCONFIG, 
					WindConfigImpl::APPAUTHOR);
		
					
		$_config[WindConfigImpl::FILTERS] = array(
		 'secondNodes' => array(WindConfigImpl::FILTER),
		 'keyNodes' => array(WindConfigImpl::FILTERNAME),
		 'valueNodes' => array(WindConfigImpl::FILTERPATH));
		
		$_config[WindConfigImpl::TEMPLATE] = array(
					WindConfigImpl::TEMPLATEDIR, 
					WindConfigImpl::COMPILERDIR, 
					WindConfigImpl::CACHEDIR, 
					WindConfigImpl::TEMPLATEEXT, 
					WindConfigImpl::ENGINE);
		
	 $_config[WindConfigImpl::URLRULE] = array(
	 			WindConfigImpl::ROUTERPASE);
	 			
		$this->childConfig = $_config;
	}
	
	public function getResult($isCheck = true) {
		return $this->fetchContents($isCheck);
	}
 
	
	private function fetchContents($isCheck = true) {
		$app = $this->createParser()->getElementByXPath(WindConfigImpl::APP);
		if ($isCheck && !$app) throw new WindException('the app config must be setting');
		$this->xmlArray[WindConfigImpl::APP] = $this->getSecondChildTree(WindConfigImpl::APP, $this->childConfig[WindConfigImpl::APP]);
		if ($isCheck && empty($this->xmlArray[WindConfigImpl::APP][WindConfigImpl::APPCONFIG]))
			throw new WindException('the "appconfig" of the "app" config must be setted!');
		$this->xmlArray[WindConfigImpl::ISOPEN] = $this->getNoChild(WindConfigImpl::ISOPEN);
		$this->xmlArray[WindConfigImpl::DESCRIBE] = $this->getNoChild(WindConfigImpl::DESCRIBE);
		$this->xmlArray[WindConfigImpl::FILTERS] = $this->getThirdChildTree(WindConfigImpl::FILTERS, 
																			$this->childConfig[WindConfigImpl::FILTERS]['secondNodes'], 
																			$this->childConfig[WindConfigImpl::FILTERS]['keyNodes'], 
																			$this->childConfig[WindConfigImpl::FILTERS]['valueNodes']);
		$this->xmlArray[WindConfigImpl::TEMPLATE] = $this->getSecondChildTree(WindConfigImpl::TEMPLATE, $this->childConfig[WindConfigImpl::TEMPLATE]);
		$this->xmlArray[WindConfigImpl::URLRULE] = $this->getSecondChildTree(WindConfigImpl::URLRULE, $this->childConfig[WindConfigImpl::URLRULE]);
		return $this->xmlArray;
	}
 
	
	private function getNoChild($node) {
		$dom = $this->getElementByXPath($node);
		if (!isset($dom[0])) return '';
		$contents = $this->getTagContents($dom[0]);
		return $contents['value'];
	}
 
	
	private function getSecondChildTree($parentNode, $nodes) {
		if (!$nodes || !$parentNode) return array();
		(!is_array($nodes)) && $nodes = array($nodes);
		$dom = $this->getElementByXPath($parentNode);
		if (!$dom) return array();
		$childs = $this->getChilds($dom[0]);
		$_result = array();
		foreach ($childs as $child) {
			(in_array($child['tagName'], $nodes)) && $_result[$child['tagName']] = $child['value'];
		}
		return $_result;
	}
 
	
	private function getThirdChildTree($parentNode, $secondeParentNode, $keyNode, $valueNode) {
		if (!$parentNode || !$secondeParentNode) return array();
		(!is_array($keyNode)) && $keyNode = array($keyNode);
		(!is_array($valueNode)) && $valueNode = array($valueNode);
		(!is_array($secondeParentNode)) && $secondeParentNode = array($secondeParentNode);
		$dom = $this->getElementByXPath($parentNode);
		if (!isset($dom[0])) return array(); 
		$childs = $this->getChilds($dom[0]);
		$_childs = array();
		foreach($childs as $child) {
			if (!in_array($child['tagName'], $secondeParentNode)) continue;
			$_secondeChild = $child['children'];
			$_keys = array();
			$_values = array();
			foreach ($_secondeChild as $_key => $_second) {
				if (!in_array($_second['tagName'], $keyNode) && !in_array($_second['tagName'], $valueNode)) continue;
				in_array($_second['tagName'], $keyNode) && $_keys[] = $_second['value'];
				in_array($_second['tagName'], $valueNode) && $_values[] = $_second['value'];
			}
			$_childs = array_merge($_childs, array_combine($_keys, $_values));
		}
		return $_childs;
	}
 
	
	private function createParser() {
		if (is_object($this->object)) return $this;
		$this->ceateParser();
		return $this;
	}
}
abstract class WindModule {
	protected $_trace = array();
	protected $_serialize = NULL;
	function __construct() {
		$this->_init();
	}
	private function _init() {}
	public function __get($propertyName) {
		if (!$this->_validateProperties($propertyName)) return;
		return $this->$propertyName;
	}
	public function __set($propertyName, $value) {
		if (!$this->_validateProperties($propertyName)) return;
		$this->_trace['setted'][$propertyName] = $value;
		$this->$propertyName = $value;
	}
	public function isseted($propertyName) {
		if (!$this->_validateProperties($propertyName)) return;
		return array_key_exists($propertyName, $this->_trace['setted']);
	}
	
	private function _validateProperties($propertyName) {
		return $propertyName && array_key_exists($propertyName, get_class_vars(get_class($this)));
	}
}
interface WindFactoryImpl {
	static public function getFactory();
	public function create($args = '');
}
class WindPack{
	private $packList = array();
	
	public function stripWhiteSpace($filename){
		return php_strip_whitespace($filename);
	}
	
	public function stripComment($content,$replace = ''){
		return preg_replace('/(?:\/\*.*\*\/)*|(?:\/\/[^\r\n]*[\r\n])*/Us',$replace,$content);
	}
	
	public function stripNR($content,$replace = "\n"){
		return preg_replace("/[\n\r]+/",$replace,$content);
	}
	
	public function stripSpace($content,$replace = ' '){
		return preg_replace("/[ ]+/",$replace,$content);
	}
	
	public function stripPhpIdentify($content,$replace = ''){
		return preg_replace("/(?:<\?(?:php)*)|(\)/i",$replace,$content);
	}
	public function getPackList(){
		return $this->packList;
	}
	public function formatPackList($comment = false,$pack = array(),$samekey = ''){
		$list = array();
		$sep = $comment ? "\r\n*" : "\r\n";
		$format = '';
		$pack = $pack && is_array($pack) ? $pack : $this->getPackList();
		foreach($pack as $key=>$value){
			if(is_array($value)){
				$format .= $this->formatPackList($comment,$value,$key);
			}else{
				$key = $samekey ? $samekey : $key;
				$format .= $key.'=>'.$value.$sep;
			}
		}
		return $format;
		
	}
	
	public function readContentFromFile($filename){
		if($this->isFile($filename)){
			$fp = fopen($filename, "r");
			while(!feof($fp)){
				$line = fgets($fp);
				if(in_array(strlen($line),array(2,3)) && in_array(ord($line),array(9,10,13)) )
					continue;
				$content .= $line;
			}
			fclose($fp);
			return $content;
		}
		return false;
	}
	
	public function writeContentToFile($filename,$content){
		$fp = fopen($filename, "w");
		fwrite($fp,$content);
		fclose($fp);
		return true;
	}
	
	public function getContentBySuffix($content,$suffix){
		switch($suffix){
			case 'php' : $content = ''.$content.'';
			default: ;
		}
		return $content;
	}
	public function getCommentBySuffix($content,$suffix,$other= ''){
		switch($suffix){
			case 'php' : $content = "\r\n\r\n";
			default: ;
		}
		return $content;
	}
	public function getPackComment($content,$suffix){
		return $this->getCommentBySuffix($this->formatPackList(true),$suffix,'Your pack list').$content;
	}
	
	public function readContentFromDir($dir,$ndir = array('.','..','.svn'),$suffix = array()){
		static $content = array();
		$dir = is_array($dir) ? $dir : array($dir);
		foreach($dir as $_dir){
			if($this->isDir($_dir)){
				$handle = dir($_dir);
				while(false != ($tmp = $handle->read())){
					$name = $this->realDir($_dir).$tmp;
					if($this->isDir($name) && !in_array($tmp,$ndir)){
						$this->readContentFromDir($name,$ndir,$suffix);
					}
					if($this->isFile($name) && !in_array($this->getFileSuffix($name),$suffix)){
						$content[] = $this->readContentFromFile($name);
						$this->setPackList($this->getFileName($name),$name);
					}
				}
				$handle->close();
			}
		}
		return $content;
	}
	public function setPackList($key,$value){
		if(isset($this->packList[$key])){
			if(is_array($this->packList[$key])){
				array_push($this->packList[$key],$value);
			}else{
				$tmp_name = $this->packList[$key];
				$this->packList[$key] = array($tmp_name,$value);
				
			}
		}else{
			$this->packList[$key] = $value;
		}
	}
	
	public function realDir($path){
		if(($pos = strrpos($path,DIRECTORY_SEPARATOR)) === strlen($path) - 1){
			return $path;
		}
		return $path.DIRECTORY_SEPARATOR;
	}
	
	public function isFile($filename){
		return is_file($filename);
	}
	
	public function isDir($dir){
		return is_dir($dir);
	}
	
	public function pack($dir,$dst,$ndir = array('.','..','.svn'),$suffix = array()){
		if(empty($dst)){
			return false;
		}
		$suffix = is_array($suffix) ? $suffix : array($suffix);
		if(!($content = $this->readContentFromDir($dir,$ndir,$suffix))){
			return false;
		}
		$fileSuffix = $this->getFileSuffix($dst);
		$content = implode("\n\r",$content);
		$content = $this->stripComment($content);
		$content = $this->stripPhpIdentify($content);
		$content = $this->stripNR($content);
		$content = $this->stripSpace($content);
		$content = $this->getPackComment($content,$fileSuffix);
		$content = $this->getContentBySuffix($content,$fileSuffix);
		$this->writeContentToFile($dst,$content);
		
		return true;
		
	}
	public function getFileSuffix($filename){
		return substr($filename,strrpos($filename,'.')+1);
	}
	public function getFileName($path,$ifsuffix = false){
		$filename = substr($path, strrpos($path,DIRECTORY_SEPARATOR)+1);
		return $ifsuffix ? $filename : substr($filename,0,strrpos($filename,'.'));
	}
}
class XML {
	
	protected $XMLData; 
	
	protected $object;
	
	protected $outputEncoding;
	
	public function __construct($data = '', $encoding = 'gbk') {
		$this->setXMLData($data);
		$this->setOutputEncoding($encoding);
	}
	
	public function setXMLData($data) {
		if (!$data) return false;
		if ($this->isXMLFile($data)) {
			$this->XMLData = trim($data);
		} else {
			throw new Exception('输入参数不是有效的xml格式');
		}
	}
	
	public function setOutputEncoding($encoding) {
		if ($encoding) $this->outputEncoding = strtoupper(trim($encoding));
	}
	
	public function setXMLFile($filePath) {
		$filePath = realpath($filePath);
		if (!is_file($filePath) || strtolower(substr($filePath, -4)) != '.xml') throw new Exception("The file which your put is not a well-format xml file!");
		$this->setXMLData(file_get_contents($filePath));
	}
	
	private function isXMLFile($data) {
		if (strpos(strtolower($data), 'xml') === false) {
			return false;
		}
		return true;
	}
	
	public function setXMLUrl($url) {
		$this->setXMLData(XML::PostHost($url));
	}
	
	public function ceateParser() {
 		$this->object = simplexml_import_dom(DOMDocument::loadXML($this->XMLData));
	}
	
	public function getXMLDocument() {
		return $this->object;
	}
	
	public function getElementByXPath($tagPath) {
		if ($tagPath) return $this->object->xpath($tagPath);
	}
	
 public function getContentsList($elements) {
 	(!is_array($elements)) && $elements = array($elements);
 	$_result = array();
 	foreach ($elements as $key => $element) {
 		$_result[] = self::getTagContents($element);
 	}
 	return $_result;
 }
 
 
	public function getTagContents($element) {
		$_array = array();
		$_array['tagName'] = $element->getName();
		$_array['value'] = self::escape($element[0]);
		$_array['attributes'] = self::getAttributes($element);
		$_array['children'] = self::getChilds($element);
		return $_array;
	}
	
	public function getAttributes($element) {
		$_attributes = array();
		$attributes = $element->attributes();
		if (!$attributes) return $_attributes;
		foreach ($attributes as $key => $value) {
			$_attributes[$key] = self::escape($value);
		}
		return $_attributes;
	}
	
	public function getChilds($element) {
		$_childs = array();
		$childs = $element->children();
		if (!$childs) return $_childs;
		foreach ($childs as $key => $value) {
			$_childs[] = self::getTagContents($value);
		}
		return $_childs;
	}
	
	public function escape($param) {
		return self::dataConvert(strval($param));
	}
		
	
	protected function dataConvert($data, $from_encoding = 'UTF-8', $to_encoding = '') {
		if (!$to_encoding) $to_encoding = $this->outputEncoding;
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($data, $to_encoding, $from_encoding);
		} else {
			
		}
		return $data;
	}
	
	private function PostHost($host, $data = '', $method = 'GET', $showagent = null, $port = null, $timeout = 30) {
		
		$parse = @parse_url($host);
		if (empty($parse)) return false;
		if ((int)$port > 0) {
			$parse['port'] = $port;
		} elseif (!$parse['port']) {
			$parse['port'] = '80';
		}
		$parse['host'] = str_replace(array('http:\/\/', 'https:\/\/'), array('', 'ssl:\/\/'), $parse['scheme'] . ":\/\/") . $parse['host'];
		if (!$fp = @fsockopen($parse['host'],$parse['port'],$errnum,$errstr,$timeout)) return false;
		$method = strtoupper($method);
		$wlength = $wdata = $responseText = '';
		$parse['path'] = str_replace(array('\\', '\/\/'), '/', $parse['path']) . "?" . $parse['query'];
		if ($method == 'GET') {
			$separator = $parse['query'] ? '&' : '';
			substr($data,0,1) == '&' && $data = substr($data,1);
			$parse['path'] .= $separator.$data;
		} elseif ($method == 'POST') {
			$wlength = "Content-length: " . strlen($data) . "\r\n";
			$wdata = $data;
		}
		$write = "{$method} $parse[path] HTTP/1.0\r\nHost: $parse[host]\r\nContent-type: application/x-www-form-urlencoded\r\n{$wlength}Connection: close\r\n\r\n{$wdata}";
		@fwrite($fp, $write);
		while ($data = @fread($fp, 4096)) {
			$responseText .= $data;
		}
		@fclose($fp);
		empty($showagent) && $responseText = trim(stristr($responseText, "\r\n\r\n"), "\r\n");
		return $responseText;
	}
}
?>