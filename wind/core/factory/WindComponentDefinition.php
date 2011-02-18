<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-12-31
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.factory.WindClassDefinition');
/**
 * 组件定义
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindComponentDefinition extends WindClassDefinition {

	/* 配置 */
	const CONFIG = 'config';

	const RESOURCE = 'resource';

	const SUFFIX = 'suffix';

	/* 是否支持隐藏变量 */
	const HIDDEN_PRO = 'hidden-pro';

	/* 配置缓存 */
	const CONFIG_CACHE = 'wind_components_config';

	/* component 定义 */
	
	const PROXY = 'proxy';

	protected $proxyClass = 'WIND:core.factory.proxy.WindClassProxy';

	/**
	 * 类代理对象定义
	 *
	 * @var string
	 */
	protected $proxy = '';

	protected $hiddenPro = '';

	/**
	 * @var array
	 */
	protected $config = array();

	/* (non-PHPdoc)
	 * @see WindClassDefinition::createInstance()
	 */
	protected function createInstance($factory, $args = array()) {
		$instance = parent::createInstance($factory, $args);
		if (!($instance instanceof WindComponentModule)) return $instance;
		$windConfig = null;
		if (isset($this->config[self::RESOURCE]) && ($resource = $this->config[self::RESOURCE])) {
			L::import('WIND:core.config.parser.WindConfigParser');
			$configPath = L::getRealPath($resource, $this->config[self::SUFFIX]);
			$windConfig = new WindConfig($configPath, new WindConfigParser(), $this->getAlias(), self::CONFIG_CACHE);
		} else {
			$windConfig = new WindConfig($this->config);
		}
		$instance->setConfig($windConfig);
		$this->setHiddenProperty($instance, $factory);
		$this->setProxyForClass($instance, $factory);
		return $instance;
	}

	/**
	 * 设置组件对象的隐藏属性
	 */
	protected function setHiddenProperty($instance, $factory) {
		if ($this->getHiddenPro() === 'false') return;
		if (isset($factory->request)) {
			$instance->windSystemConfig = $factory->request->getAttribute(WindFrontController::WIND_CONFIG);
			$instance->windFactory = $factory->request->getAttribute(WindFrontController::WIND_FACTORY);
			$instance->request = $factory->request;
		}
		if (isset($factory->response)) $instance->response = $factory->response;
	}

	/**
	 * 为类设置代理
	 * 
	 * @param WindModule $instance
	 * @param WindFactory $factory
	 */
	protected function setProxyForClass($instance, $factory) {
		if (!($proxyPath = $this->getProxy()) || $proxyPath === 'false') return;
		$proxyPath = ($proxyPath === 'true' || $proxyPath === true) ? $this->proxyClass : $this->getProxy();
		$proxyClass = L::import($proxyPath);
		if (!class_exists($proxyClass)) return;
		
		$proxyClass = $factory->createInstance($proxyClass);
		if ($proxyClass instanceof WindClassProxy) $instance->setClassProxy($proxyClass);
	}

	/* (non-PHPdoc)
	 * @see WindClassDefinition::init()
	 */
	protected function init($classDefinition) {
		parent::init($classDefinition);
		if (isset($classDefinition[self::CONFIG])) {
			$this->config = $classDefinition[self::CONFIG];
		}
		if (isset($classDefinition[self::PROXY])) {
			$this->setProxy($classDefinition[self::PROXY]);
		}
		if (isset($classDefinition[self::HIDDEN_PRO])) {
			$this->setHiddenPro($classDefinition[self::HIDDEN_PRO]);
		}
	}

	/**
	 * @return the $proxy
	 */
	public function getProxy() {
		return $this->proxy;
	}

	/**
	 * @param string $proxy
	 */
	public function setProxy($proxy) {
		$this->proxy = $proxy;
	}

	/**
	 * @return the $hiddenPro
	 */
	public function getHiddenPro() {
		return $this->hiddenPro;
	}

	/**
	 * @param field_type $hiddenPro
	 */
	public function setHiddenPro($hiddenPro) {
		$this->hiddenPro = $hiddenPro;
	}

	/**
	 * @return the $config
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @param array $config
	 */
	public function setConfig($config) {
		$this->config = $config;
	}

}