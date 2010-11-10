<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WRouterFactory extends WFactory {
	private $parser = 'url';
	private $parserPath = 'router.parser.WUrlRouteParser';
	private $router = null;
	
	private static $instance = null;
	
	/**
	 * 返回路由实例
	 * @param WSystemConfig $configObj
	 * @return WRouter
	 */
	function create($configObj = null) {
		if ($this->router === null) {
			$this->_initConfig($configObj);
			if (($pos = strrpos($this->parserPath, '.')) === false)
				$className = $this->parserPath;
			else
				$className = substr($this->parserPath, $pos + 1);
			W::import($this->parserPath);
			if (!class_exists($className))
				return null;
			$class = new ReflectionClass($className);
			$this->router = call_user_func_array(array(
				$class, 
				'newInstance'
			), array($configObj));
		}
		return $this->router;
	}
	
	/**
	 * 初始化路由配置信息
	 * @param WSystemConfig $configObj
	 */
	private function _initConfig($configObj) {
		if (!$configObj)
			return;
		$parser = $configObj->getRouterConfig('parser');
		$parserPath = $configObj->getRouterParser($parser);
		$parser && $this->parser = $parser;
		$parserPath && $this->parserPath = $parserPath;
	}
	
	/**
	 * @return WRouterFactory
	 */
	static function getFactory() {
		if (self::$instance === null) {
			$class = new ReflectionClass(__CLASS__);
			$args = func_get_args();
			self::$instance = call_user_func_array(array(
				$class, 
				'newInstance'
			), (array) $args);
		}
		return self::$instance;
	}

}