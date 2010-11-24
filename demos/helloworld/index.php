<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
header("Content-type: text/html; charset=gbk");
define('R_P', dirname ( __FILE__ ));
define('F_P', R_P . '/../../wind/');
define('C_P', R_P . '/wind/');
define('CONFIG_CACHE_PATH', R_P . '/data');//设置缓存文件位置

require_once (F_P . '/wind.php');

//W::setApps('TEST', array('rootPath' => R_P), true);
//W::setCurrentApp('helloworld');//设置当前应用的名字

WindFrontController::getInstance()->run();
