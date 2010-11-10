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
	static $parser = '';
	static $parserPath = '';
	
	/**
	 * 返回路由实例
	 * @param WSystemConfig $configObj
	 */
	static function create($configObj = '') {

	}
	
	static function initConfig($configObj) {
		$parser = $configObj->getRouterConfig('parser');
		W::import($configObj->getRouterParser($parser));
	
	}
}