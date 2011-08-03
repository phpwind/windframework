<?php
Wind::import('COM:parser.IWindConfigParser');
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
	/**
	 * 配置文件支持的格式白名单
	 */
	const CONFIG_XML = '.XML';
	const CONFIG_PHP = '.PHP';
	const CONFIG_INI = '.INI';
	const CONFIG_PROPERTIES = '.PROPERTIES';
	
	/**
	 * 配置解析对象队列
	 * @var array object $configParser
	 */
	private $configParsers = array();

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
	 * @param string $append 追加的文件
	 * @param AbstractWindCache $cache  缓存策略
	 * @return array 解析结果
	 */
	public function parse($configPath, $alias = '', $append = '', AbstractWindCache $cache = null) {
		if ($config = $this->getCache($alias, $append, $cache)) return $config;
		$config = $this->doParser($configPath);
		$this->setCache($alias, $append, $cache, $config);
		return $config;
	}

	/**
	 * 设置配置缓存
	 * @param string $alias
	 * @param string $append
	 * @param AbstractWindCache $cache
	 */
	private function setCache($alias, $append, $cache, $data) {
		if (!$alias || !$cache) return;
		if ($append) {
			$_config = (array) $cache->get($append);
			$_config[$alias] = $data;
			$cache->set($append, $_config);
		} else {
			$cache->set($alias, $data);
		}
	}

	/**
	 * 返回配置缓存
	 * @param string alias
	 * @param string append
	 * @param AbstractWindCache cache
	 * @return array
	 */
	private function getCache($alias, $append, $cache) {
		if (IS_DEBUG) return array(); 
		if (!$alias || !$cache) return array();
		if (!$append) return $cache->get($alias);
		
		$config = $cache->get($append);
		return isset($config[$alias]) ? $config[$alias] : array();
	}

	/**
	 * 创建配置文件解析器
	 * 
	 * @access private
	 */
	private function createParser($type) {
		switch ($type) {
			case self::CONFIG_XML:
				Wind::import("WIND:component.parser.WindXmlParser");
				return new WindXmlParser();
				break;
			case self::CONFIG_INI:
				Wind::import("WIND:component.parser.WindIniParser");
				return new WindIniParser();
				break;
			case self::CONFIG_PROPERTIES:
				Wind::import("WIND:component.parser.WindPropertiesParser");
				return new WindPropertiesParser();
				break;
			default:
				throw new WindException('\'ConfigParser\' failed to initialize.');
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
	private function doParser($configFile) {
		if (!is_file($configFile)) throw new WindException(
			'[component.parser.WindConfigParser.doParser] The file \'' . $configFile . '\' is not exists');
		$ext = strtoupper(strrchr($configFile, '.'));
		if ($ext == self::CONFIG_PHP) return @include ($configFile);
		if (!isset($this->configParsers[$ext])) $this->configParsers[$ext] = $this->createParser($ext);
		return $this->configParsers[$ext]->parse($configFile);
	}
}