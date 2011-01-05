<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
define('D_S', DIRECTORY_SEPARATOR);
define('WIND_PATH', dirname(__FILE__) . D_S);
define('COMPILE_PATH', WIND_PATH . 'compile' . D_S);
define('VERSION', '1.0.5');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class upgrade {
	static function upgradePreLoad() {
		if (defined('COMPILE_PATH') && is_writable(COMPILE_PATH)) {
			$packfile = COMPILE_PATH . 'preload_' . VERSION . '.php';
			if (!is_file($packfile)) {
				require WIND_PATH . 'utility' . D_S . 'WindPack.php';
				$pack = new WindPack();
				$pack->pack(array('core'), $packfile);
			}
		}
	}
}
upgrade::upgradePreLoad();