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
 * 配置文件格式允许有4中格式：xml, php, properties, ini
 * 
 * 根据用户传入的配置文件所在位置解析配置文件，
 * 并将生成的配置缓存文件， 以php格式默认放在‘COMPILE_PATH’下面
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindConfigParser {
	const ISMERGE = 'isMerge';
	/**
	 * 框架缺省配置文件的名字
	 * @var string $defaultConfig 
	 */
	private $defaultConfig = 'wind_config';
	/**
	 * 生成的全局缓存文件的文件名
	 * @var string $globalAppsConfig
	 */
	private $globalAppsConfig = 'config.php';
	
	/**
	 * 配置解析对象
	 * @var object $configParser
	 */
	private $configParser = null;
	/**
	 * 配置解析的引擎
	 * @var string $parserEngine
	 */
	private $parserEngine = 'xml';
	/**
	 * 配置文件支持的格式白名单
	 * @var array $configExt
	 */
	private $configExt = array('xml', 'php', 'ini', 'properties');
	
	/**
	 * 配置文件解析出来的数据编码
	 * @var string $encoding
	 */
	private $encoding = 'UTF-8';
	
	/**
	 * 初始化
	 * 设置解析数据输出的编码方式
	 * @param String $outputEncoding	
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
	 * @return array 解析成功返回的数据
	 */
	public function parser($currentAppName, $configPath = '') {
		$currentAppName = strtolower(trim($currentAppName));
		if ($this->isCompiled($currentAppName)) {
			$app = W::getApps($currentAppName);
			return include $app[IWindConfig::APP_CONFIG];
		}
		$configPath = trim($configPath);
		$userConfig = array();
		if ($configPath === '') {
			$this->parserEngine = 'php';
		} else {
			$this->fetchConfigExt($configPath);
		}
		$userConfig = $this->executeParser($configPath);
		$defaultConfig = $this->executeParser(WIND_PATH . D_S . $this->defaultConfig . '.' . $this->parserEngine);
		$userConfig = $this->mergeConfig($defaultConfig, $userConfig);
		
		//$this->updateGlobalCache($currentAppName);
		(!isset($userConfig[IWindConfig::ROOTPATH]) || trim($userConfig[IWindConfig::ROOTPATH]) == '') && $userConfig[IWindConfig::ROOTPATH] = dirname($_SERVER['SCRIPT_FILENAME']);
		$this->saveAsFile($currentAppName . '_config.php', $userConfig);
		return $userConfig;
	}
	
	/**
	 * 初始化配置文件解析器
	 * @access private
	 * 
	 */
	private function initParser() {
		switch (strtoupper($this->parserEngine)) {
			case 'XML':
				L::import("WIND:component.parser.WindXmlParser");
				$this->configParser = new WindXmlParser('1.0', $this->encoding);
				break;
			case 'INI':
				L::import("WIND:component.parser.WindIniParser");
				$this->configParser = new WindIniParser();
				break;
			case 'PROPERTIES':
				L::import("WIND:component.parser.WindPropertiesParser");
				$this->configParser = new WindPropertiesParser();
				break;
			default:
				throw new WindException('init config parser error.');
				break;
		}
	}
	
	/**
	 * 返回是否需要执行解析
	 * 
	 * 如果是debug模式，则返回false, 进行每次都进行解析
	 * 如果不是debug模式，则先判断是否设置了缓存模式
	 * 如果没有设置缓存则返回false, 进行解析，
	 * 如果设置了缓存模式，则判断该应用是否已经被解析
	 * 如果当前访问应用没有被解析过，则返回false, 进行解析
	 * 如果当前应用解析过，则判断解析出来的文件是否存在
	 * 如果该解析出来的文件不存在，则返回false, 执行解析
	 * 否则返回true, 直接读取缓存
	 * 
	 * @return boolean  false:需要进行解析， true：不需要进行解析，直接读取缓存文件
	 */
	private function isCompiled($currentAppName = '') {
		if (IS_DEBUG) return false;
		if (!W::ifCompile()) return false;
		if (!($app = W::getApps($currentAppName))) return false;
		if (!is_file($app[IWindConfig::APP_CONFIG])) return false;
		return true;
	}
	
	/**
	 * 获得文件的后缀，决定采用的是哪种配置格式，
	 * 如果传递的文件配置格式不在支持范围内，则抛出异常
	 * 
	 * @return boolean : true  解析文件格式成功，解析失败则抛出异常
	 */
	private function fetchConfigExt($configPath) {
		$this->parserEngine = strtolower(trim(strrchr($configPath, '.'), '.'));
		if (!in_array($this->parserEngine, $this->configExt)) {
			throw new WindException("The format of the config file doesn't sopported yet!");
		}
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
		if (!$configFile) return array();
		if (strtoupper($this->parserEngine) == 'PHP') {
			return include($configFile);
		}
		if ($this->configParser === null) {
			$this->initParser();
		}
		return $this->configParser->parse($configFile);
	}
	
	/**
	 * 合并配置文件
	 * 
	 * 如果应用配置中没有配置相关选项，则使用默认配置中的选项
	 * 如果是需要合并的项，则将缺省项和用户配置项进行合并，合并规则为用户配置优先级大于缺省配置
	 * 
	 * @param array $defaultConfig 默认的配置文件
	 * @param array $appConfig 应用的配置文件
	 * @return array  合并后的配置
	 */
	private function mergeConfig($defaultConfig, $appConfig) {
		list($defaultConfig, $mergeTags) = $this->getMergeTags($defaultConfig);
		if (!$appConfig) return $defaultConfig;
		list($appConfig) = $this->getMergeTags($appConfig);
		foreach ($defaultConfig as $key => $value) {
			if (in_array($key, $mergeTags) && isset($appConfig[$key])) {
				$defaultConfig[$key] = array_merge((array)$defaultConfig[$key], (array)$appConfig[$key]);
				continue;
			}
			if (isset($appConfig[$key])) {
				$defaultConfig[$key] = $appConfig[$key];
				continue;
			}
		}

		$defaultKeys = array_keys($defaultConfig);
		$appConfigKeys = array_keys($appConfig);
		if (!($difKeys = array_diff($appConfigKeys, $defaultKeys))) return $defaultConfig;
		foreach($difKeys as $key) {
			$defaultConfig[$key] = $appConfig[$key];
		}
		return $defaultConfig;
	}
	
	/**
	 * 将应用的APP内容添加到缓存文件中
	 * 添加缓存
	 * @param string $currentAppName 应用的名字
	 * @return boolean
	 */
	private function updateGlobalCache($currentAppName) {
		if (trim($currentAppName) == '') return false;
		$globalConfigPath = COMPILE_PATH . D_S . $this->globalAppsConfig;
		$sysConfig = array();
		if (is_file($globalConfigPath)) {
			$sysConfig = include ($globalConfigPath);
		}
		$sysConfig[$currentAppName][IWindConfig::APP_NAME] = $currentAppName;
		$sysConfig[$currentAppName][IWindConfig::APP_CONFIG] = COMPILE_PATH . D_S . $currentAppName . '_config.php';
		return $this->saveAsFile($this->globalAppsConfig, $sysConfig);
	}
	
	/**
	 * 获得isMerge属性的标签，同时将该属性删除
	 * 
	 * @param array $parames
	 * @return array (处理后的数组，含有merge属性的标签集合)
	 */
	private function getMergeTags($params) {
		if (!is_array($params)) return array($params, array());
		$mergeTags = array();
		foreach ($params as $key => $value) {
			if (is_array($value) && isset($value[self::ISMERGE])) {
				(in_array(strtolower(trim($value[self::ISMERGE])), array('true', '1'))) && $mergeTags[] = $key;
				unset($params[$key][self::ISMERGE]);
			}
		}
		return array($params, $mergeTags);
	}
	
	/**
	 * 保存成文件
	 * 
	 * @param string $filename
	 * @param array $data
	 * @return boolean 保存成功则返回true,保存失败则返回false
	 */
	private function saveAsFile($filename, $data) {
		if (!W::ifCompile() || !$filename || !$data) return false;
		Common::writeover(COMPILE_PATH . D_S . strtolower($filename), "<?php\r\n return " . Common::varExport($data) . ";\r\n?>");
		return true;
	}
	
}