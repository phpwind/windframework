<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:core.base.impl.WindConfigImpl");
/**
 * 解析并整合配置文件同时生成缓存
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindConfigParser implements WindConfigImpl {
	private $defaultPath = '';//缺省的配置文件路径
	private $userAppPath = '';//用户配置文件路径
	private $globalAppsPath = '';//全局应用配置路径
	private $globalAppsConfig = '';//全局应用配置文件
	private $parserEngine = '';//配置使用的解析引擎
	private $configExt = array('php' , 'xml', 'properpoties', 'ini');//会用到的用户配置格式
	private $encoding = 'gbk';
	/**
	 * 初始化
	 * 
	 * @param $globalAppsPath
	 * @param $userAppPath
	 * @param $defaultPath
	 */
	public function __construct($globalAppsPath, $userAppPath, $defaultPath, $outputEncoding = 'gbk') {
		$this->setGlobalAppsPath($globalAppsPath);
		$this->setUserAppPath(realpath($userAppPath));
		$this->setDefaultPath(realpath($defaultPath));
		$outputEncoding && $this->encoding = $outputEncoding;
	}
	
	/**
	 * 设置默认配置文件路径
	 * @param $defaultPath the $defaultPath to set
	 * @author xiaoxia xu
	 */
	public function setDefaultPath($defaultPath) {
		(file_exists($defaultPath)) && $this->defaultPath = rtrim(rtrim($defaultPath, '/'), '\\');
	}

	/**
	 * 设置用户应用的配置文件路径
	 * @param $userAppPath the $userAppPath to set
	 * @author xiaoxia xu
	 */
	public function setUserAppPath($userAppPath) {
		(file_exists($userAppPath)) && $this->userAppPath = rtrim(rtrim($userAppPath, '/'), '\\');
	}

	/**
	 * 设置全局应用配置文件
	 * @param $globalAppsPath the $globalAppsPath to set
	 * @author xiaoxia xu
	 */
	public function setGlobalAppsPath($globalAppsPath) {
		$this->globalAppsConfig = $globalAppsPath;//设置配置文件的位置
		$this->globalAppsPath = rtrim(rtrim(dirname($globalAppsPath), '/'), '\\');
		
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
	public function getConfig() {
		$oConfig = $this->isExist($this->userAppPath);
		$defaultConfig = $this->defaultPath . '/wind_config.xml';
		if ($oConfig == null) $oConfig = $defaultConfig;
		//如果缓存文件存在并且原文件没有更新，则直接读取缓存
		if (($path = $this->isCached()) && is_file($path)) {
			if (is_file($oConfig) && (filemtime($oConfig) < filemtime($path)) && filemtime($defaultConfig) < filemtime($path)) {
				include($path);
				return $config;
			}
		}
		return $this->parserConfig();
	}
	
	/**
	 * 解析配置文件
	 * 
	 * @return array 返回最终的配置信息
	 */
	public function parserConfig() {
		$uConfig = $dConfig = array();
		$defaultConfigP = $this->isExist($this->defaultPath);
		$default = $this->defaultPath . '/wind_config.xml';
		$this->parserEngine = 'XML';
		($default) && $dConfig = $this->switchParser($default, false);//获得缺省的配置文件
		$oConfigP = $this->isExist($this->userAppPath);
		($oConfigP) && $uConfig = $this->switchParser($oConfigP);
		return $this->mergeConfig($dConfig, $uConfig);
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
		$globalConfigP = $this->globalAppsPath . '/config.php';
		$sysConfig = array();
		if (is_file($globalConfigP)) {
			include($globalConfigP);
		}
		$appName = isset($config[WindConfigImpl::APPNAME]) ? $config[WindConfigImpl::APPNAME] : $this->getAppName();
		$sysConfig[$appName] = $config;
		$this->writeover($globalConfigP, "<?php\r\n \$sysConfig = " . $this->varExport($sysConfig) . ";\r\n?>");
		return $sysConfig;
	}
	/**
	 * 提供一个接口，用于获得全局应用配置文件内容
	 * @return array 
	 */
	public function getAppsConfig() {
		$globalConfigP = $this->globalAppsPath . '/config.php';
		include($globalConfigP);
		return $sysConfig;
	}
	
	/**
	 * 处理配置文件
	 * 根据在WindConfigImpl中的设置对相关配置项进行合并/覆盖
	 * 如果应用配置中没有配置相关选项，则使用默认配置中的选项
	 * 如果是需要合并的项，则将缺省项和用户配置项进行合并
	 * 
	 * @param array $defaultConfig 默认的配置文件
	 * @param array $appConfig 应用的配置文件
	 * @return array 返回处理后的配置文件
	 */
	public function mergeConfig($defaultConfig, $appConfig) {
		if (count($appConfig) == 0) $appConfig = $defaultConfig;
		$app = $appConfig[WindConfigImpl::APP];
		(!isset($app[WindConfigImpl::APPNAME]) || $app[WindConfigImpl::APPNAME] == '') && $app[WindConfigImpl::APPNAME] = $this->getAppName();
		(!isset($app[WindConfigImpl::APPROOTPATH]) || $app[WindConfigImpl::APPROOTPATH] == '') && $app[WindConfigImpl::APPROOTPATH] = $this->userAppPath;
		(isset($app[WindConfigImpl::APPCONFIG])) && $app[WindConfigImpl::APPCONFIG] = $this->userAppPath . $app[WindConfigImpl::APPCONFIG];
		$app[WindConfigImpl::APPCONFIG] = $app[WindConfigImpl::APPCONFIG]. '/' . $app[WindConfigImpl::APPNAME] . '_config.php';
		foreach ($defaultConfig as $key => $value) {
			$_merge = (strpos(WindConfigImpl::MERGEARRAY, ',') === false) ? array(WindConfigImpl::MERGEARRAY) : explode(',', WindConfigImpl::MERGEARRAY);
			if (in_array($key, $_merge) && $appConfig[$key]) {
				!is_array($value) && $value = array($value);
				!is_array($appConfig[$key]) && $appConfig[$key] = array($appConfig[$key]);
				$defaultConfig[$key] = array_merge($value, $appConfig[$key]);
			} else {
				($appConfig[$key]) && $defaultConfig[$key] = $appConfig[$key];
			}
		}
		$defaultConfig[WindConfigImpl::APP] = $app;
		$this->writeover($app[WindConfigImpl::APPCONFIG], "<?php\r\n \$config = " . $this->varExport($defaultConfig) . ";\r\n?>");
		$this->addAppsConfig($app);
		return $defaultConfig;
	}
	
	/**
	 * 选择合适的解析器
	 * 
	 * @param string $configPath
	 * @param boolean $isCheck 是否需要进行配置检查
	 * @return array 返回解析的结果
	 */
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
	
	/**
	 * 解析XML格式的配置文件
	 * 
	 * @param string $configPath  被解析文件的路径
	 * @param boolean $isCheck 是否需要检查配置
	 * @param string $encoding  输出的编码
	 */
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
	
	/**
	 * 获得当前应用的名字，解析路径的最后一个文件夹
	 * 
	 * @return string 返回符合的项
	 */
	private function getAppName() {
		$path = trim($this->userAppPath, '/');
		$path = trim($this->userAppPath, '\\');
		$_tmp = explode('/', $this->userAppPath);
		(!$_tmp) && $_tmp = explode('\\', $this->userAppPath);
		$pos = count($_tmp)-1;
		if ($_tmp[$pos]) return strtoupper($_tmp[$pos]);
	}
	
	/**
	 * 判断文件是否存在，如果存在则返回该配置文件（包含路径），并且设置解析的引擎类型，如果不存在返回NULL
	 * 
	 * @param string $path
	 * @return mixed null | string 
	 */
	private function isExist($path) {
		foreach ($this->configExt as $ext) {
			$_temp = realpath($path . '/' . 'config.' . $ext);
			if (is_file($_temp)) {
				$this->parserEngine = strtoupper($ext);
				return $_temp;
			} 
		}
		return null;
	}
	
	/**
	 * 判断是否已经被缓存
	 * 
	 * @return string 返回缓存路径 | '';
	 */
	private function isCached() {
		if (!is_file($this->globalAppsConfig)) return '';
		include($this->globalAppsConfig);
		$appConfig = array();
		foreach ($sysConfig as $appName => $config) {
			if (isset($config[WindConfigImpl::APPROOTPATH]) && $config[WindConfigImpl::APPROOTPATH] == $this->userAppPath) {
				$appConfig = $sysConfig[$appName];
				$appConfig[WindConfigImpl::APPNAME] = $appName;
				break;
			}
		}
		if (isset($appConfig[WindConfigImpl::APPCONFIG])) return $appConfig[WindConfigImpl::APPCONFIG];
		else return '';
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
		$tmparray = array('://',"\0");
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