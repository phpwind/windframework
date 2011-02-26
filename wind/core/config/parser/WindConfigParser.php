<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.config.parser.IWindConfigParser');
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
class WindConfigParser implements IWindConfigParser {
	
	const ISMERGE = 'isMerge';
	
	/**
	 * 配置文件支持的格式白名单
	 */
	const CONFIG_XML = 'XML';
	
	const CONFIG_PHP = 'PHP';
	
	const CONFIG_INI = 'INI';
	
	const CONFIG_PROPERTIES = 'PROPERTIES';
	
	const WIND_ROOT = 'wind';
	
	/**
	 * 框架缺省配置文件的名字
	 * @var string $defaultConfig 
	 */
	private $windConfig = 'wind_config';
	
	/**
	 * 配置解析对象队列
	 * @var array object $configParser
	 */
	private $configParsers = array();
	
	/**
	 * 配置文件解析出来的数据编码
	 * @var string $encoding
	 */
	private $encoding = 'UTF-8';
	
	/**
	 * ISMERGE的有效值范围
	 * @var array $mergeValue
	 */
	private $mergeValue = array('true', '1');
	
	/**
	 * 初始化
	 * 设置解析数据输出的编码方式
	 * @param String $outputEncoding	
	 */
	public function __construct($outputEncoding = 'UTF-8') {
		$this->encoding = $outputEncoding;
	}
	
	/**
	 * 解析文件，保存缓存，返回解析结果
	 * 
	 * 1、缺省的配置文件，采用XML格式解析返回
	 * 2、如果输入的配置文件格式没有提供支持，则抛出异常
	 * 3、根据格式进行解析
	 * 4、参数三$isApp 用来配置该解析式组件格式的解析还是应用配置的解析，
	 * 如果是应用解析需要进行merge操作，如果是组件解析则不用
	 * 
	 * @param string $name       解析后保存的文件名字
	 * @param string $configPath 待解析文件的绝对路径
	 * @param boolean $isApp     判断是应用解析还是组件配置解析
	 * @return array             解析成功返回的数据
	 */
	public function parseConfig($configPath = '', $alias = '') {
		$alias = trim($alias);
		$cacheFile = '';
		$config = array();
		if ($alias) {
			$cacheFile = $this->buildCacheFilePath($alias);
			$config = $this->getCacheContent($cacheFile);
			if (isset($config[self::WIND_ROOT]) && !$this->needCompiled()) return $config[self::WIND_ROOT];
		}
		$config = (array) $config;
		$configPath = trim($configPath);
		$parseFormat = $this->getConfigFormat($configPath);
		$config[self::WIND_ROOT] = $this->doParser($configPath, $parseFormat);
		$config[self::WIND_ROOT] = $this->mergeConfig($this->doParser($this->getWindConfigPath($parseFormat), $parseFormat), $config[self::WIND_ROOT]);
		$alias && $this->saveConfigFile($cacheFile, $config);
		return $config[self::WIND_ROOT];
	}
	
	/**
	 * 解析组件的配置文件
	 * 
	 * 如果用户没有传入别名，则每次都执行解析
	 * 如果用户传入别名，判断是否传入了追加的文件名
	 * 如果传入了追加的文件名，则判断该文件的内容中是否存在以别名为key的值
	 * 如果有该值则返回该值，否则继续
	 * 如果没有传入追加的文件名，则判断该别名命名的缓存文件是否存在
	 * 如果存在则返回该文件内容，否则继续
	 * 如果没有传入别名，则继续
	 * 
	 * 如果该缓存文件不存在，则判断如果不是以追加的方式，并且已经存在该缓存文件，则返回该缓存文件
	 * 如果都不存在，则执行解析，并根据是否追加的条件，进行追加或是新建。
	 * 
	 * @param string $configPath 待解析的文件路径
	 * @param string $alias 解析后保存的key名
	 * @param string $append  采用最佳的方法追加到$appandName指定的文件中
	 * @return array 解析结果
	 */
	public function parse($configPath, $alias = '', $append = '') {
		$config = array();
		$alias = trim($alias);
		$append = !$append ? '' : trim($append);
		$alias && $cacheFileName = ($append ? $this->buildCacheFilePath($append) : $this->buildCacheFilePath($alias));
		if ($alias) {
			$append && $config = $this->getCacheContent($cacheFileName);
			if (isset($config[$alias]) && !$this->needCompiled()) {
				return $config[$alias];
			}
		}
		if (!($configPath = trim($configPath))) throw new WindException('Please input the file path!');
		$result = $this->doParser($configPath, $this->getConfigFormat($configPath));
		if (!$alias) return $result;
		$config[$alias] = $result;
		$append && is_file($cacheFileName) && $this->saveConfigFile($cacheFileName, $config);
		return $result;
	}
	
	/**
	 * 获得缓存文件内容
	 * 
	 * @param string $file 缓存文件名
	 * @return array 缓存文件内容
	 */
	private function getCacheContent($file) {
		$content = array();
		if (is_file($file)) $content = include ($file);
		return  is_array($content) ?  $content : array();
	}
	
	/**
	 * 创建配置文件解析器
	 * 
	 * @access private
	 */
	private function createParser($type) {
		switch ($type) {
			case self::CONFIG_XML:
				L::import("WIND:component.parser.WindXmlParser");
				return new WindXmlParser('1.0', $this->encoding);
				break;
			case self::CONFIG_INI:
				L::import("WIND:component.parser.WindIniParser");
				return new WindIniParser();
				break;
			case self::CONFIG_PROPERTIES:
				L::import("WIND:component.parser.WindPropertiesParser");
				return new WindPropertiesParser();
				break;
			default:
				throw new WindException('init config parser error.');
				break;
		}
	}
	
	/**
	 * 执行解析并返回解析结果
	 * 接收一个配置文件路径，根据路径信息初始化配置解析器，并解析该配置
	 * 以数组格式返回配置解析结果
	 * 
	 * @param string $configFile  解析的文件路径
	 * @return array			    返回解析结果
	 */
	private function doParser($configFile, $type) {
		if (!$configFile) return array();
		if (!is_file($configFile)) throw new WindException('The file <' . $configFile . '> is not exists');
		if ($type == 'PHP') return include ($configFile);
		if (!isset($this->configParsers[$type])) {
			$this->configParsers[$type] = $this->createParser($type);
		}
		return $this->configParsers[$type]->parse($configFile);
	}
	
	/**
	 * 合并配置文件
	 * 
	 * 如果应用配置中没有配置相关选项，则使用默认配置中的选项
	 * 如果是需要合并的项，则将缺省项和用户配置项进行合并，合并规则为用户配置优先级大于缺省配置
	 * 
	 * @param array $defaultConfig  默认的配置文件
	 * @param array $appConfig 		应用的配置文件
	 * @return array  				合并后的配置
	 */
	private function mergeConfig($defaultConfig, $appConfig) {
		if (!$defaultConfig || !is_array($defaultConfig)) return array();
		list($defaultConfig, $mergeTags) = $this->getMergeTags($defaultConfig);
		
		if (!$appConfig || !is_array($appConfig)) return $defaultConfig;
		list($appConfig) = $this->getMergeTags($appConfig);
		
		foreach ($defaultConfig as $key => $value) {
			if (in_array($key, $mergeTags) && isset($appConfig[$key])) {
				$defaultConfig[$key] = array_merge((array) $defaultConfig[$key], (array) $appConfig[$key]);
				continue;
			}
			if (isset($appConfig[$key])) {
				$defaultConfig[$key] = $appConfig[$key];
				continue;
			}
		}
		
		if (!($difKeys = array_diff(array_keys($appConfig), array_keys($defaultConfig)))) return $defaultConfig;
		foreach ($difKeys as $key) {
			$defaultConfig[$key] = $appConfig[$key];
		}
		return $defaultConfig;
	}
	
	/**
	 * 获得isMerge属性的标签，同时将该属性删除
	 * 
	 * @param array $parames     需要查找的数组
	 * @return array 			(处理后的数组，含有merge属性的标签集合)
	 */
	private function getMergeTags($params) {
		$mergeTags = array();
		foreach ($params as $key => $value) {
			if (is_array($value) && isset($value[self::ISMERGE])) {
				($this->checkCanMerge($value[self::ISMERGE])) && $mergeTags[] = $key;
				$params[$key][self::ISMERGE] = null;
				unset($params[$key][self::ISMERGE]);
			}
		}
		return array($params, $mergeTags);
	}
	
	/**
	 * 返回是否需要执行解析
	 * 
	 * 如果是debug模式，则返回false, 进行每次都进行解析
	 * 如果不是debug模式，则先判断是否设置了缓存模式
	 * 如果没有设置缓存则返回false, 进行解析，
	 * 如果设置了缓存模式，则判断缓存文件是否存在
	 * 如果该解析出来的文件不存在，则返回false, 执行解析
	 * 否则返回true, 直接读取缓存
	 * 
	 * @param string $cacheFile  缓存文件路径
	 * @return boolean  		 false:需要进行解析， true：不需要进行解析，直接读取缓存文件
	 */
	private function needCompiled() {
		if (IS_DEBUG || !W::ifCompile()) return true;
		return false;
	}
	
	/**
	 * 获得文件的后缀，决定采用的是哪种配置格式，
	 * 如果传递的文件配置格式不在支持范围内，则抛出异常
	 * 
	 * @param string $configPath 配置文件路径
	 * @return boolean           : true  解析文件格式成功，解析失败则抛出异常
	 */
	private function getConfigFormat($configPath) {
		if ($configPath === '') return self::CONFIG_XML;
		$format = strtoupper(trim(strrchr($configPath, '.'), '.'));
		if (!in_array($format, $this->getConfigFormatList())) {
			throw new WindException("The format of the config file doesn't sopported yet!");
		}
		return $format;
	}
	
	/**
	 * 判断数值是否在IsMerge的可用范围内
	 * 
	 * @param string $value 需要判断的字串
	 * @return boolean  	返回判断结果
	 */
	private function checkCanMerge($value) {
		return in_array(strtolower(trim($value)), $this->mergeValue);
	}
	
	/**
	 * 保存成文件
	 * 
	 * @param string $filename   保存的文件名
	 * @param array $data		  需要保持的数据
	 * @return boolean 			  保存成功则返回true,保存失败则返回false
	 */
	private function saveConfigFile($filename, $data) {
		if (!W::ifCompile() || !$filename || !$data) return false;
		L::import('COM:utility.WindFile');
		return WindFile::saveData($filename, $data);
	}
	
	/**
	 * 获得默认配置文件的路径
	 * 
	 * @param string $parseFormat 后缀
	 * @return string 	获得默认配置文件的路径
	 */
	private function getWindConfigPath($parseFormat) {
		return WIND_PATH . $this->windConfig . '.' . strtolower($parseFormat);
	}
	
	/**
	 * 构造文件的路径
	 * 
	 * @param string $fileName   缓存文件的名字
	 * @return string 			 返回缓存文件的$fileName的绝对路径
	 */
	private function buildCacheFilePath($fileName) {
		return rtrim(COMPILE_PATH, '/') . D_S . strtolower($fileName) . '.php';
	}
	
	/**
	 * 获得支持解析的配置文件格式的白名单
	 * 
	 * @return array  返回配置文件格式的白名单
	 */
	private function getConfigFormatList() {
		return array(self::CONFIG_XML, self::CONFIG_PHP, self::CONFIG_INI, self::CONFIG_PROPERTIES);
	}
	
	/**
	 * 析构函数
	 * 
	 */
	public function __destruct() {
		$this->configParser = null;
	}
}