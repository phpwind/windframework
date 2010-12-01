<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:component.config.base.IWindConfig");
L::import('WIND:utility.Common');
/**
 * 配置文件解析类
 * 配置文件格式允许有3中格式：xml,properties,ini
 * 
 * 配置默认放在应用程序跟路径下面，解析生成的配置缓存文件默认放在‘COMPILE_PATH’下面
 * 如果‘$userAppConfig’文件中有定义了解析生成的配置文件存放路径则放置在该路径下面
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindConfigParser implements IWindConfig {
	private $defaultPath = WIND_PATH;
	private $defaultConfig = 'wind_config';
	
	private $userAppConfigPath;
	private $userAppConfig = 'config';
	
	private $globalAppsPath = COMPILE_PATH;
	private $globalAppsConfig = 'config.php';
	
	private $configParser = null;
	private $parserEngine = 'xml';
	private $configExt = array('xml', 'properpoties', 'ini');
	
	private $encoding = 'gbk';
	private $defaultGAM = array();
	private $userGAM = array();
	
	private $currentApp = '';
	
	/**
	 * 初始化
	 * @param String $outputEncoding	//编码信息
	 */
	public function __construct($outputEncoding = 'gbk') {
		$this->currentApp = W::getCurrentApp();
		if ($outputEncoding) $this->encoding = $outputEncoding;
	}
	
	/**
	 * 初始化配置文件解析器
	 * @access private
	 * @param string $parser
	 */
	private function initParser() {
		switch (strtoupper($this->parserEngine)) {
			case 'XML':
				L::import("WIND:component.config.WindXMLConfig");
				$this->configParser = new WindXMLConfig($this->encoding);
				break;
			default:
				throw new WindException('init config parser error.');
				break;
		}
	}
	
	/**
	 * 返回是否需要执行解析过程
	 * 
	 * 如果是debug模式，则返回false,进行每次都进行解析
	 * 如果不是debug模式，则先判断是否设置了缓存模式
	 *    如果没有设置缓存则返回false,进行解析，
	 * 如果设置了缓存模式，则判断该应用是否已经被解析
	 *    如果当前访问应用没有被解析过，则返回false,进行解析
	 * 如果当前应用解析过，则判断解析出来的文件是否存在
	 *    如果该解析出来的文件不存在，则返回false,执行解析
	 * 否则返回true,直接读取缓存
	 * 
	 * @return boolean false:需要进行解析， true：不需要进行解析，直接读取缓存文件
	 */
	private function isCompiled() {
		if (IS_DEBUG) return false;
		if (!W::ifCompile()) return false;
		if (!($app = W::getApps())) return false;
		if (!is_file($app[IWindConfig::APP_CONFIG])) return false;
		return true;
	}
	
	/**
	 * @return mixed boolean |multitype:
	 */
	private function fetchConfigExit($rootPath) {
		$rootPath = realpath($rootPath);
		foreach ($this->configExt as $ext) {
			if (is_file($rootPath . D_S . $this->userAppConfig . '.' . $ext)) {
				$this->parserEngine = $ext;
				return realpath($rootPath . D_S . $this->userAppConfig . '.' . $ext);
			}
		}
		return '';
	}
	
	/**
	 * @param WindHttpRequest $request
	 */
	public function parser($request) {
		$rootPath = dirname($request->getServer('SCRIPT_FILENAME'));
		if ($this->isCompiled()) {
			$app = W::getApps();
			return @include $app[IWindConfig::APP_CONFIG];
		}
		$defaultConfigPath = $this->defaultPath . D_S . $this->defaultConfig . '.' . $this->parserEngine;
		list($defaultConfig, $this->defaultGAM) = $this->executeParser(realpath($defaultConfigPath));
		list($userConfig, $this->userGAM) = $this->executeParser($this->userAppConfigPath);
		$userConfig = $this->mergeConfig($defaultConfig, $userConfig);
		$userConfig[IWindConfig::APP] = $this->getAppInfo($rootPath, $userConfig);
		
		W::setApps($userConfig[IWindConfig::APP][IWindConfig::APP_NAME], $userConfig[IWindConfig::APP]);
		W::setCurrentApp($userConfig[IWindConfig::APP][IWindConfig::APP_NAME]);
		$this->updateGlobalCache($userConfig);
		
		Common::writeover($userConfig[IWindConfig::APP][IWindConfig::APP_CONFIG], "<?php\r\n return " . Common::varExport($userConfig) . ";\r\n?>");
		return $userConfig;
	}
	
	/**
	 * @param rootPath
	 * @param userConfig
	 */
	private function getAppInfo($rootPath, $userConfig) {
		$app = isset($userConfig[IWindConfig::APP]) ? $userConfig[IWindConfig::APP] : array();
		if (!isset($app[IWindConfig::APP_NAME]) || $app[IWindConfig::APP_NAME] == '' || $app[IWindConfig::APP_NAME] == 'default') {
			$app[IWindConfig::APP_NAME] = $this->getAppName($rootPath);
		}
		if (!isset($app[IWindConfig::APP_ROOTPATH]) || $app[IWindConfig::APP_ROOTPATH] == '' || $app[IWindConfig::APP_ROOTPATH] == 'default') {
			$app[IWindConfig::APP_ROOTPATH] = realpath($rootPath);
		}
		$_file = D_S . $app[IWindConfig::APP_NAME] . '_config.php';
		if (!isset($app[IWindConfig::APP_CONFIG]) || $app[IWindConfig::APP_CONFIG] == '') {
			$app[IWindConfig::APP_CONFIG] = $this->globalAppsPath . $_file;
		} else {
			$app[IWindConfig::APP_CONFIG] = $this->getRealPath($app[IWindConfig::APP_ROOTPATH], $app[IWindConfig::APP_CONFIG]) . $_file;
		}
		return $app;
	}
	
	/**
	 * 接收一个配置文件路径，根据路径信息初始化配置解析器，并解析该配置
	 * 以数组格式返回配置解析结果
	 * 
	 * @param string $configFile
	 * @return array
	 */
	private function executeParser($configFile) {
		if (!$configFile) return array(null, null);
		if ($this->configParser === null) {
			$this->initParser();
		}
		$this->configParser->loadFile($configFile);
		$this->configParser->parser();
		return array($this->configParser->getResult(), $this->configParser->getGAM());
	}
	
	/**
	 * 处理配置文件
	 * 根据在IWindConfig中的设置对相关配置项进行合并/覆盖
	 * 如果应用配置中没有配置相关选项，则使用默认配置中的选项
	 * 如果是需要合并的项，则将缺省项和用户配置项进行合并
	 * 
	 * @param array $defaultConfig 默认的配置文件
	 * @param array $appConfig 应用的配置文件
	 * @return array 返回处理后的配置文件
	 */
	private function mergeConfig($defaultConfig, $appConfig) {
		if (!$appConfig) return $defaultConfig;
		$_merge = $this->defaultGAM[IWindConfig::ISMERGE];
		$hasInDefaultConfigKeys = array();
		foreach ($appConfig as $key => $value) {
			if (in_array($key, $_merge) && isset($defaultConfig[$key])) {
				!is_array($value) && $value = array($value);
				!is_array($defaultConfig[$key]) && $defaultConfig[$key] = array($defaultConfig[$key]);
				$appConfig[$key] = array_merge($value, $defaultConfig[$key]);
			}
			(!isset($defaultConfig[$key])) && $hasInDefaultConfigKeys[] = $key;
		}
		$appConfigKeys = array_keys($appConfig);
		$_notInAppConfig = array_diff(array_keys($defaultConfig), $hasInDefaultConfigKeys);
		foreach ($_notInAppConfig as $key) {
			if (in_array($key, $appConfigKeys)) continue;
			$appConfig[$key] = $defaultConfig[$key];
		}
		return $appConfig;
	}
	
	/**
	 * 将全局内容从数组中找出，并添加到缓存文件中
	 * 将该应用的相关配置merge到全局应有配置中
	 * 当前应用：如果没有配置应用的名字，则将当前访问的最后一个位置设置为应用名称
	 * 否则使用配置中配置好的应用名字。
	 * 添加缓存
	 * @param array $config
	 */
	private function updateGlobalCache($config) {
		$_global = $this->defaultGAM[IWindConfig::ISGLOBAL];
		if (count($_global) == 0) return false;
		$_globalArray = array();
		foreach ($_global as $key) {
			if (!isset($config[$key])) continue;
			$_temp = $config[$key];
			if ($_temp['name']) $key = $_temp['name'];
			$_globalArray[$key] = $_temp;
		}
		$globalConfigPath = $this->globalAppsPath . D_S . $this->globalAppsConfig;
		$sysConfig = array();
		if (is_file($globalConfigPath)) {
			$sysConfig = @include ($globalConfigPath);
		}
		$sysConfig = array_merge($sysConfig, $_globalArray);
		Common::writeover($globalConfigPath, "<?php\r\n return " . Common::varExport($sysConfig) . ";\r\n?>");
		return true;
	}
	
	/**
	 * 通过命名空间返回真实路径
	 * @param string $rootPath 路径
	 * @param string $oPath 需要查找的文件路径
	 */
	private function getRealPath($rootPath, $oPath) {
		if (strpos(':', $oPath) === false) {
			return L::getRealPath($oPath . '.*', '', '', $rootPath);
		} else {
			return L::getRealPath($oPath . '.*');
		}
	}
	
	/**
	 * 获得当前应用的名字，解析路径的最后一个文件夹
	 * 
	 * @return string 返回符合的项
	 */
	private function getAppName($rootPath) {
		if ($this->currentApp != '') return $this->currentApp;
		$path = rtrim(rtrim($rootPath, '\\'), '/');
		$pos = (strrpos($path, '\\') === false) ? strrpos($path, '/') : strrpos($path, '\\');
		return substr($path, -(strlen($path) - $pos - 1));
	}
}