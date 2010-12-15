<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:component.config.base.IWindConfig");
L::import('WIND:component.Common');
L::import("WIND:core.exception.WindException");

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
class WindConfigParser {
	private $defaultConfig = 'wind_config';
	private $globalAppsConfig = 'config.php';
	
	private $configParser = null;
	private $parserEngine = 'xml';
	private $configExt = array('xml', 'php', 'ini', 'properpoties');
	
	private $encoding = 'UTF-8';
	
	/**
	 * 初始化
	 * @param String $outputEncoding	//编码信息
	 */
	public function __construct($outputEncoding = 'UTF-8') {
		if ($outputEncoding) $this->encoding = $outputEncoding;
	}
	
	/**
	 * 1、缺省的配置文件，读取框架提供的php格式返回
	 * 2、如果输入的配置文件格式没有提供支持，则抛出异常
	 * 3、根据格式进行解析
	 * @param string $currentAppName 当前应用的名字
	 * @param string $configPath 当前应用配置文件地址
	 * @return array 
	 */
	public function parser($currentAppName, $configPath = '') {
		if ($this->isCompiled($currentAppName)) {
			$app = W::getApps($currentAppName);
			return @include $app[IWindConfig::APP_CONFIG];
		}
		$configPath = trim($configPath);
		$userConfig = array();
		if ($configPath === '') {
			$this->parserEngine = 'php';
		} elseif (!$this->fetchConfigExt($configPath)) {
			throw new WindException("The format of the config file doesn't sopported yet!");
		} else {
			list($userConfig) = $this->executeParser($configPath);
		}
		$defaultConfigPath = WIND_PATH . D_S . $this->defaultConfig . '.' . $this->parserEngine;
		list($defaultConfig, $globalTags, $mergeTags) = $this->executeParser($defaultConfigPath);
		$userConfig = $this->mergeConfig($defaultConfig, $userConfig, $mergeTags);
		
		W::setCurrentApp($currentAppName);
		$this->updateGlobalCache($currentAppName);
		
		Common::writeover(COMPILE_PATH . D_S . $currentAppName . '_config.php', "<?php\r\n return " . Common::varExport($userConfig) . ";\r\n?>");
		return $userConfig;
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
			case 'PHP':
				L::import("WIND:component.config.WindPHPConfig");
				$this->configParser = new WindPHPConfig($this->encoding);
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
	 * 如果没有设置缓存则返回false,进行解析，
	 * 如果设置了缓存模式，则判断该应用是否已经被解析
	 * 如果当前访问应用没有被解析过，则返回false,进行解析
	 * 如果当前应用解析过，则判断解析出来的文件是否存在
	 * 如果该解析出来的文件不存在，则返回false,执行解析
	 * 否则返回true,直接读取缓存
	 * 
	 * @return boolean false:需要进行解析， true：不需要进行解析，直接读取缓存文件
	 */
	private function isCompiled($currentAppName) {
		if (IS_DEBUG) return false;
		if (!W::ifCompile()) return false;
		if (!($app = W::getApps($currentAppName))) return false;
		if (!is_file($app[IWindConfig::APP_CONFIG])) return false;
		return true;
	}
	
	/**
	 * 获得文件的后缀，决定采用的是哪种配置格式，
	 * 如果传递的文件配置格式不在支持范围内，则返回false;
	 * 
	 * @return boolean :
	 */
	private function fetchConfigExt($configPath) {
		$ext = strtolower(trim(strrchr($configPath, '.'), '.'));
		if (!in_array($ext, $this->configExt)) return false;
		$this->parserEngine = $ext;
		return true;
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
		return array($this->configParser->getResult(), $this->configParser->getGlobalTags(), $this->configParser->getMergeTags());
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
	private function mergeConfig($defaultConfig, $appConfig, $mergeTags) {
		if (!$appConfig) return $defaultConfig;
		foreach ($appConfig as $key => $value) {
			if (in_array($key, $mergeTags) && isset($defaultConfig[$key])) {
				$appConfig[$key] = array_merge((array)$defaultConfig[$key], (array)$value);
			}
		}
		$appConfigKeys = array_keys($appConfig);
		foreach (array_keys($defaultConfig) as $key) {
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
	private function updateGlobalCache($currentAppName) {
		if (trim($currentAppName) == '') return false;
		$globalConfigPath = COMPILE_PATH . D_S . $this->globalAppsConfig;
		$sysConfig = array();
		if (is_file($globalConfigPath)) {
			$sysConfig = @include ($globalConfigPath);
		}
		$sysConfig[$currentAppName][IWindConfig::APP_NAME] = $currentAppName;
		$sysConfig[$currentAppName][IWindConfig::APP_CONFIG] = realpath(COMPILE_PATH . D_S . $currentAppName . '_config.php');
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
}