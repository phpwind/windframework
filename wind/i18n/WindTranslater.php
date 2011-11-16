<?php
Wind::import("WIND:cache.dependency.WindFileCacheDependency");
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
class WindTranslater extends WindModule implements IWindTranslater {
	
	/**
	 * 缓存key前缀
	 *
	 * @var string
	 */
	protected $_cachePrefix = 'Wind.i18n.WindTranslater';
	/**
	 * 消息文件
	 *
	 * @var string
	 */
	protected $_fileName = 'message';
	/**
	 * 语言包目录
	 *
	 * @var string
	 */
	protected $langPath;
	/**
	 * 语言
	 *
	 * @var string
	 */
	protected $language;
	/**
	 * 消息存储池
	 *
	 * @var array
	 */
	protected $_messages = array();
	/**
	 * 缓存
	 *
	 * @var AbstractWindCache
	 */
	protected $cache;
	
	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config){
		parent::setConfig($config);
		$this->langPath = $this->getConfig('langPath', '', '');
		$this->language = $this->getConfig('language', '', 'zh_cn');
	}
	
	/* (non-PHPdoc)
	 * @see IWindTranslater::translate()
	 */
	public function translate($message, $params = array()) {
		list($namespace, $message) = explode(':', $message, 2);
		$path = Wind::getRealDir();
		$file = Wind::getRealPath($this->langPath . $this->language . $namespace . $this->_fileName);
		$messages = Wind::getApp()->getComponent('configParser')->parse($file, $this->_cachePrefix . $file, '', $this->_getCache());
		return $params == array() ? $messages[$message] : strtr($messages[$message], $params);
	}
}

?>