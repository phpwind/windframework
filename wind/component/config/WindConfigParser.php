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
	 * 如果compile文件夹未被定义或不可写则返回false
	 * 如果config.php文件不存在则返回false
	 * 如果当前app信息不存在则返回false
	 * 如果当前app的配置文件不存在则返回false
	 */
	private function isCompiled() {
		if (!W::ifCompile()) return false;
		if (!W::getApps()) return false;
		$app = W::getApps();
		if (!is_file($app[IWindConfig::APP_CONFIG])) return false;
		$config = $this->fetchConfigExit($app[IWindConfig::APP_ROOTPATH]);
		if ($config == '') return false;
		
		$_configLastT = filemtime($config);
		$_cacheLastT = filemtime($app[IWindConfig::APP_CONFIG]);
		$defaultConfig = $this->defaultPath . D_S . $this->defaultConfig . '.' . $this->parserEngine;
		$_defaultConfigLastT = filemtime($defaultConfig);
		if ($_configLastT >= $_cacheLastT  || $_defaultConfigLastT >= $_cacheLastT) return false;
		return true;
	}

	/**
	 * 
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
	 * @param WindHttpRequest $request  //请求信息
	 */
	public function parser($request) {
		$rootPath = dirname($request->getServer('SCRIPT_FILENAME'));
		if ($this->isCompiled()) {
			$app = W::getApps();
			return @include $app[IWindConfig::APP_CONFIG];
		}
		$userConfigPath = $this->fetchConfigExit($rootPath);
		$defaultConfigPath = $this->defaultPath . D_S . $this->defaultConfig . '.' . $this->parserEngine;
		list($defaultConfig, $this->defaultGAM) = $this->execuseParser(realpath($defaultConfigPath));
		list($userConfig, $this->userGAM) = $this->execuseParser($userConfigPath);
		$empty = false;
		if (count($userConfig) == 0) {
			$userConfig = $defaultConfig;
			$empty = true;
		}
		if (isset($userConfig[IWindConfig::APP])) {
			$app = $userConfig[IWindConfig::APP];
			if (!isset($app[IWindConfig::APP_NAME]) || $app[IWindConfig::APP_NAME] == '' || $app[IWindConfig::APP_NAME] == 'default') {
			     $app[IWindConfig::APP_NAME] = $this->getAppName($rootPath);
			}
			if (!isset($app[IWindConfig::APP_ROOTPATH]) || $app[IWindConfig::APP_ROOTPATH] == '' || $app[IWindConfig::APP_ROOTPATH] == 'default') {
				$app[IWindConfig::APP_ROOTPATH] = realpath($rootPath);
			}
			$_file = '/' . $app[IWindConfig::APP_NAME] . '_config.php';
			if (!isset($app[IWindConfig::APP_CONFIG]) || $app[IWindConfig::APP_CONFIG] == '' ) {
				$app[IWindConfig::APP_CONFIG] = $this->globalAppsPath . $_file;
			} else {
				$app[IWindConfig::APP_CONFIG] = $this->getRealPath($app[IWindConfig::APP_NAME], $app[IWindConfig::APP_ROOTPATH], $app[IWindConfig::APP_CONFIG]) . $_file;
			}
			$userConfig[IWindConfig::APP] = $app;
		}
		return $this->mergeConfig($defaultConfig, $userConfig, $empty);
	}
	
	/**
	 * 接收一个配置文件路径，根据路径信息初始化配置解析器，并解析该配置
	 * 以数组格式返回配置解析结果
	 * 
	 * @param string $configFile
	 * @return array
	 */
	private function execuseParser($configFile) {
		//list(, $fileName, $ext, $realPath) = L::getRealPath($configFile, true);
		if (!$configFile) return array();
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
	private function mergeConfig($defaultConfig, $appConfig, $flag = false) {
		if ($flag === false) {
			$_merge = $this->getGAM(IWindConfig::ISMERGE);
			$hasInDefaultConfigKeys = array();
			foreach ($appConfig as $key => $value) {
				if (in_array($key, $_merge) && isset($defaultConfig[$key])) {
					!is_array($value) && $value = array($value);
					!is_array($defaultConfig[$key]) && $defaultConfig[$key] = array($defaultConfig[$key]);
					$appConfig[$key] = array_merge($value, $defaultConfig[$key]);
				}
				(!isset($defaultConfig[$key])) && $hasInDefaultConfigKeys[] = $key;
			}
			//将应用配置中不缺省的项填充到应用配置中；
			$appConfigKeys = array_keys($appConfig);
			$_notInAppConfig = array_diff(array_keys($defaultConfig), $hasInDefaultConfigKeys);
			foreach ($_notInAppConfig as $key) {
				if (in_array($key, $appConfigKeys)) continue;
				$appConfig[$key] = $defaultConfig[$key];
			}
		}
		if (!isset($appConfig[IWindConfig::APP])) return $appConfig;
		Common::writeover($appConfig[IWindConfig::APP][IWindConfig::APP_CONFIG], "<?php\r\n return " . Common::varExport($appConfig) . ";\r\n?>");
		$this->updateGlobalCache($appConfig);
		return $appConfig;
	}
	
	private function getGAM($key) {
		$_tmp1 = isset($this->userGAM[$key]) ? $this->userGAM[$key] : array();
		$_tmp2 = isset($this->defaultGAM[$key]) ? $this->defaultGAM[$key] : array();
		if ($_tmp1 && $_tmp2) return array_merge($_tmp1, $_tmp2);
		if ($_tmp1) return $_tmp1;
		return $_tmp2;
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
		if (!W::ifCompile()) return false;
		$_global = $this->getGAM(IWindConfig::ISGLOBAL);
		if (count($_global) == 0 ) return false;
		$_globalArray = array();
		foreach ($_global as $key) {
			isset($config[$key]) && $_globalArray[$key] = $config[$key];
		}
		$globalConfigPath = $this->globalAppsPath . D_S . $this->globalAppsConfig;
		$sysConfig = array();
		if (is_file($globalConfigPath)) {
			$sysConfig = @include ($globalConfigPath);
		}
		$sysConfig = (count($sysConfig) > 0) ? array_merge($sysConfig, $_globalArray) : $_globalArray;
		Common::writeover($globalConfigPath, "<?php\r\n return " . Common::varExport($sysConfig) . ";\r\n?>");
		return true;
	}
	
	/**
	 * 通过命名空间返回真实路径
	 * @param string $nameSpace 默认的命名空间
	 * @param string $oPah 路径
	 * @param string $file 需要查找的文件路径
	 */
	private function getRealPath($nameSpace, $rootPath, $oPah) {
		if (strpos(':', $oPah) === false) {
			return L::getRealPath($nameSpace . ':' . $oPah . '.*', '', '', $rootPath);
		} else {
			return L::getRealPath($oPah . '.*', '', '', $rootPath);
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