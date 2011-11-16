<?php
/**
 * php数组的方式，来保存要翻译的信息
 *
 * <code>
 * Wind::getApp()->getComponent('i18n')->translate('helloworld{you}', array('{you}' => $you), 'blog');
 * </code>
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package i18n
 */
class WindTranslater extends WindModule implements IWindLangResource {
	/**
	 * 缓存key前缀
	 *
	 * @var string
	 */
	protected $_cachePrefix = 'Wind.i18n.WindLangResource';
	/**
	 * 消息存储池
	 *
	 * @var array
	 */
	protected $_messages = array();
	/**
	 * 默认资源文件
	 *
	 * @var string
	 */
	protected $default = 'message';
	/**
	 * 资源文件后缀名定义
	 *
	 * @var string
	 */
	protected $suffix = '';
	/**
	 * 语言包目录
	 *
	 * @var string
	 */
	protected $path;
	/**
	 * 语言
	 *
	 * @var string
	 */
	protected $language;

	/* (non-PHPdoc)
	 * @see IWindTranslater::translate()
	 */
	public function translate($message, $params = array()) {
		list($package, $message) = explode(':', $message . ':', 2);
		$keys = explode('.', $message);
		$file = $keys[0];
		$path = $this->resolvedPath($package);
		if (is_file($path . '/' . $file . '.' . $this->suffix)) {
			$path = $path . '/' . $file . '.' . $this->suffix;
			unset($keys[0]);
		} elseif (is_file($path . '/' . $this->default . '.' . $this->suffix)) {
			$path = $path . '/' . $this->default . '.' . $this->suffix;
			$file = $this->default;
		} else
			throw new WindI18nException(
				'[wind.WindTranslater.translate] lang resource file  ' . $this->path . 'is not exit.');
		
		if (!isset($this->_messages[$path])) {
			$cache = Wind::getApp()->getComponent('windCache');
			$messages = Wind::getApp()->getComponent('configParser')->parse($path, $this->_cachePrefix . $file, '', 
				$cache);
			$this->_messages[$path] = $messages;
		}
		$message = $this->getMessage($this->_messages[$path], $keys);
		return $message;
	}

	/**
	 * 获取一条message信息
	 * 
	 * @param array $messages
	 * @param string $key
	 */
	protected function getMessage($messages, $keys) {
		if (is_string($keys)) return '';
		foreach ($keys as $value) {
			if (!isset($messages[$value])) continue;
			$messages = $messages[$value];
		}
		return is_array($messages) ? '' : $messages;
	}

	/**
	 * 解析资源文件路径信息
	 *
	 * @param string $package
	 * @return string
	 */
	protected function resolvedPath($package) {
		$this->path || $this->path = Wind::getRootPath(Wind::getAppName());
		$this->language || $this->language = Wind::getApp()->getRequest()->getAcceptLanguage();
		$path = $this->path . '/' . $this->language . '/' . $package;
		$path = Wind::getRealDir($path, true);
		if (!is_dir($path)) throw new WindI18nException(
			'[Wind.WindTranslater.resolvedPath] resolve resource path fail, path ' . $path . ' is not exit.');
		return $path;
	}

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		$this->path = $this->getConfig('path', '', '');
		$this->language = $this->getConfig('language', '', 'zh_cn');
	}
}

?>