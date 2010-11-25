<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:core.base.IWindConfig");
/**
 * 解析并整合配置文件同时生成缓存
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindConfigParser implements IWindConfig {
	private $defaultPath = '';//缺省的配置文件路径
	private $defaultConfig = '';//缺省的配置文件
	
	private $userAppPath = '';//用户配置文件路径
	private $userAppConfig = '';//保存用户配置文件
	
	private $globalAppsPath = '';//全局应用配置路径
	private $globalAppsConfig = '';//全局应用配置文件
	private $parserEngine = '';//配置使用的解析引擎
	private $currentApp = '';
	private $configExt = array('xml', 'properpoties', 'ini');//会用到的用户配置格式
	private $encoding = 'gbk';
	private $defaultGAM;
	private $userGAM;
	/**
	 * 初始化
	 * 
	 * @param $globalAppsPath
	 * @param $userAppPath
	 * @param $defaultPath
	 */
	public function __construct($request, $outputEncoding = 'gbk') {
		$this->setDefaultPath(WIND_PATH);
		$this->setGlobalAppsPath(COMPILE_PATH);
		$this->setUserAppPath(dirname($request->getServer('SCRIPT_FILENAME')));
		$outputEncoding && $this->encoding = $outputEncoding;
		$this->currentApp = W::getCurrentApp();
		$this->parserEngine = 'xml';
		$this->defaultGAM = array();
		$this->userGAM = array();
	}
	
	/**
	 * 设置默认配置文件路径
	 * 
	 * @param $defaultPath the $defaultPath to set
	 * @author xiaoxia xu
	 */
	public function setDefaultPath($defaultPath) {
		(file_exists($defaultPath)) && $this->defaultPath = rtrim(rtrim($defaultPath, '/'), '\\');
		$this->defaultConfig = $this->defaultPath . '/wind_config.xml';
	}

	/**
	 * 设置用户应用的配置文件路径
	 * 
	 * @param $userAppPath the $userAppPath to set
	 * @author xiaoxia xu
	 */
	public function setUserAppPath($userAppPath) {
		(file_exists($userAppPath)) && $this->userAppPath = rtrim(rtrim($userAppPath, '/'), '\\');
	}

	/**
	 * 设置全局应用配置文件
	 * 
	 * @param $globalAppsPath the $globalAppsPath to set
	 * @author xiaoxia xu
	 */
	public function setGlobalAppsPath($globalAppsPath) {
		$this->globalAppsPath = rtrim(rtrim($globalAppsPath, '/'), '\\');
		$this->globalAppsConfig = $this->globalAppsPath . '/config.php';//设置配置文件的位置
	}
	
	/**
	 * 获得配置文件，
	 * 首先解析全局应用配置文件，检查当前被访问应用是否已经被解析
	 * 如果已经被解析：则检查原配置文件，如果原配置文件不存在，则读取默认配置文件为原配置文件，
	 * 					如果原配置文件已被修改，则再次解析原配置文件及默认配置文件生成缓存。
	 * 					如果没有修改，则直接返回缓存文件
	 * 如果配置文件不存在，则读取缺省的配置文件，并检查是否已被修改
	 *        			如果原配置文件已被修改，则再次解析原配置文件及默认配置文件生成缓存。
	 *        			如果没有修改，则直接返回缓存文件
	 */
	public function parser() {
		$oConfig = $this->isExist($this->userAppPath, true);
		($oConfig === false) && $oConfig = $this->defaultConfig;
		
		//如果缓存文件存在并且原文件没有更新，则直接读取缓存
		$config = $this->isCached();
		if ($config !== false) {
			$appName = $config[IWindConfig::APP_NAME];
			$cacheP = $config[IWindConfig::APP_CONFIG];
			if ((filemtime($oConfig) < filemtime($cacheP)) && filemtime($this->defaultConfig) < filemtime($cacheP)) {
				return true;
			}
		}
		return $this->parserConfig();
	}
	
	/**
	 * 解析配置文件
	 * 
	 * @return mixed 返回当前应用的配置名字
	 */
	private function parserConfig() {
		$uConfig = $dConfig = array();
		list($dConfig, $this->defaultGAM) = $this->parserXML($this->defaultConfig, $this->encoding);//获得缺省的配置文件
		$oConfigP = ($this->userAppConfig != '') ? $this->userAppConfig : $this->isExist($this->userAppPath, true);
		($oConfigP !== false) && list($uConfig, $this->userGAM) = $this->switchParser($oConfigP);
		return $this->mergeConfig($dConfig, $uConfig);
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
	public function mergeConfig($defaultConfig, $appConfig) {
		if (count($appConfig) == 0) $appConfig = $defaultConfig;
		$app = $appConfig[IWindConfig::APP];
		(!isset($app[IWindConfig::APP_NAME]) || $app[IWindConfig::APP_NAME] == '') && $app[IWindConfig::APP_NAME] = $this->getAppName();
		(!isset($app[IWindConfig::APP_ROOTPATH]) || $app[IWindConfig::APP_ROOTPATH] == '') && $app[IWindConfig::APP_ROOTPATH] = realpath($this->userAppPath);
		$_file = '/' . $app[IWindConfig::APP_NAME] . '_config.php';
		if (!isset($app[IWindConfig::APP_CONFIG])) {
			$app[IWindConfig::APP_CONFIG] = $this->globalAppsPath . $_file;
		} else {
			$app[IWindConfig::APP_CONFIG] = $this->getRealPath($app[IWindConfig::APP_NAME], $app[IWindConfig::APP_ROOTPATH], $app[IWindConfig::APP_CONFIG]) . $_file;
		}
		$appConfig[IWindConfig::APP] = $app;
		
		$_merge = $this->getGAM(IWindConfig::MERGEATTR);
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
			if (in_array($key, $appConfigKeys))  continue;
			$appConfig[$key] = $defaultConfig[$key];
		}
		$this->writeover($app[IWindConfig::APP_CONFIG], "<?php\r\n return " . $this->varExport($appConfig) . ";\r\n?>");
		$this->addGlobalArray($appConfig);
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
	 * @param array $config
	 */
	private function addGlobalArray($config) {
		$_global = $this->getGAM(IWindConfig::GLOBALATTR);;//获得需要放置到全局中的项
		$_globalArray = array();
		foreach ($_global as $key) {
			isset($config[$key]) && $_globalArray[$key] = $config[$key];
		}
		$this->addAppsConfig($_globalArray);
		return true;
	}
	
	/**
	 * 将该应用的相关配置merge到全局应有配置中
	 * 当前应用：如果没有配置应用的名字，则将当前访问的最后一个位置设置为应用名称
	 * 否则使用配置中配置好的应用名字。
	 * 添加缓存
	 * 
	 * @param array $config  当前应用的应用配置信息
	 * @return array 返回修改后的应用配置信息
	 */
	public function addAppsConfig($config) {
		$sysConfig = array();
		if (is_file($this->globalAppsConfig)) {
			include($this->globalAppsConfig);
		}
		//不存在，则创建
		$appName = isset($config[IWindConfig::APP_NAME]) ? $config[IWindConfig::APP_NAME] : $this->getAppName();
		$sysConfig = array($sysConfig, $config);
		$this->writeover($this->globalAppsConfig, "<?php\r\n return " . $this->varExport($sysConfig) . ";\r\n?>");
		return $sysConfig;
	}
	
	/**
	 * 提供一个接口，用于获得全局应用配置文件内容
	 * @return array 
	 */
	public function getAppsConfig() {
		if (is_file($this->globalAppsConfig)) {
			include($this->globalAppsConfig);
			return $sysConfig;
		}
		return false;
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
	 * 选择合适的解析器
	 * 
	 * @param string $configPath
	 * @param boolean $isCheck 是否需要进行配置检查
	 * @return array array(content, globalAndMerge的array)   返回解析的结果
	 */
	public function switchParser($configPath) {
		switch($this->parserEngine) {
			case 'XML':
				return $this->parserXML($configPath, $this->encoding);
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
	
	/**
	 * 解析XML格式的配置文件
	 * 
	 * @param string $configPath  被解析文件的路径
	 * @param boolean $isCheck 是否需要检查配置
	 * @param string $encoding  输出的编码
	 */
	public function parserXML($configPath, $encoding) {
		L::import('WIND:core.WindXMLConfig');
	    $xml = new WindXMLConfig();
	    $xml->setXMLFile($configPath);
	    $xml->setOutputEncoding($encoding);
	    $xml->parser();
	    return array($xml->getResult(), $xml->getGAM());
	}
	
	public function parserProperties() {
		
	}
	public function parserIni() {
		
	}
	
	/**
	 * 获得当前应用的名字，解析路径的最后一个文件夹
	 * 
	 * @return string 返回符合的项
	 */
	private function getAppName() {
		if ($this->currentApp != '') return $this->currentApp;
		$path = rtrim(rtrim($this->userAppPath, '\\'), '/');
		$pos = (strrpos($path, '\\') === false) ? strrpos($path, '/') : strrpos($path, '\\');
		return substr($path, -(strlen($path)-$pos-1));
	}
	
	/**
	 * 判断文件是否存在，如果存在则返回该配置文件（包含路径），并且设置解析的引擎类型，如果不存在返回NULL
	 * 
	 * @param string $path
	 * @param string $isSave
	 * @return mixed null | string 
	 */
	private function isExist($path, $isSave = false) {
		foreach ($this->configExt as $ext) {
			$_temp = $path . '/config.' . $ext;
			if (is_file($_temp)) {
				$this->parserEngine = strtoupper($ext);
				($isSave) && $this->userAppConfig = $_temp;
				return $_temp;
			} 
		}
		return false;
	}
	
	/**
	 * 判断是否已经被缓存
	 * 如果用户通过setCurrentApp设置了当前应用的名字，则通过该名字进行检索
	 * 如果查找不到，则根据访问路径匹配来进行检索
	 * 
	 * @return string 返回缓存路径 | '';
	 */
	private function isCached() {
		if (!is_file($this->globalAppsConfig)) return false;
		include($this->globalAppsConfig);
		//如果用户设置的当前应用名字可以找到，则直接返回
		if ($this->currentApp && isset($sysConfig[$this->currentApp]) && is_file($sysConfig[$this->currentApp][IWindConfig::APPCONFIG])) 
				return $sysConfig[$this->currentApp];
		//否则通过路径查找		
		$appConfig = array();
		foreach ($sysConfig as $appName => $config) {
			if (isset($config[IWindConfig::APPROOTPATH]) && $config[IWindConfig::APPROOTPATH] == $this->userAppPath) {
				return $sysConfig[$appName];
			}
		}
		return false;
	}
	
	/**
	 * 变量导出为字符串
	 *
	 * @param mixed $input 变量
	 * @param string $indent 缩进
	 * @return string
	 */
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
	
	/**
	 * 写文件
	 *
	 * @param string $fileName 文件绝对路径
	 * @param string $data 数据
	 * @param string $method 读写模式
	 * @param bool $ifLock 是否锁文件
	 * @param bool $ifCheckPath 是否检查文件名中的“..”
	 * @param bool $ifChmod 是否将文件属性改为可读写
	 * @return bool 是否写入成功
	 */
	public function writeover($fileName, $data, $method = 'rb+', $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
		$tmpname = strtolower($fileName);
		$tmparray = array(':\/\/',"\0");
		$tmparray[] = '..';
		if (str_replace($tmparray, '', $tmpname) != $tmpname) return false;
		
		@touch($fileName);
		if (!$handle = @fopen($fileName, $method)) return false;
		$ifLock && flock($handle, LOCK_EX);
		$writeCheck = fwrite($handle, $data);
		$method == 'rb+' && ftruncate($handle, strlen($data));
		fclose($handle);
		$ifChmod && @chmod($fileName, 0777);
		return $writeCheck;
	}
}