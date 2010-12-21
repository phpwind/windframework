<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-29
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:core.base.WindFactory');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindRouterFactory extends WindFactory {
	private $router = null;
	
	/** 
	 * 返回router实例
	 * (non-PHPdoc)
	 * @see wind/component/factory/base/WindFactory#create()
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function create($request = null, $response = null) {
		$systemConfig = $response->getData('WindSystemConfig');
		$parser = $systemConfig->getRouter(IWindConfig::ROUTER_PARSER);
		$parserConfig = $systemConfig->getRouterParsers($parser);
		$parserPath = $parserConfig[IWindConfig::ROUTER_PARSERS_PATH];
		$className = L::import($parserPath);
		if (!class_exists($className)) throw new WindException('The router ' . $className . ' is not exists.');
		return new $className($parserConfig);
	}
	
	/**
	 * @return WindRouterFactory
	 */
	static public function getFactory() {
		return parent::getFactory(__CLASS__);
	}
}