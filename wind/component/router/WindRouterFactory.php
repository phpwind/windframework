<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.factory.base.WindFactory');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindRouterFactory extends WindFactory {
	private $router = null;
	
	/* 
	 * 返回router实例
	 * (non-PHPdoc)
	 * @see wind/component/factory/base/WindFactory#create()
	 */
	public function create() {
		if ($this->router === null) {
			$parserConfig = C::getRouterParsers(C::getRouter('parser'));
			$parserPath = $parserConfig[IWindConfig::ROUTER_PARSERS_PATH];
			list($className, $parserPath) = L::getRealPath($parserPath, true);
			L::import($parserPath);
			if (!class_exists($className)) {
				throw new WindException('The router ' . $className . ' is not exists.');
			}
			$this->router = &new $className($parserConfig);
		}
		return $this->router;
	}
	
	/**
	 * @return WindRouterFactory
	 */
	static public function getFactory() {
		return parent::getFactory(__CLASS__);
	}
}