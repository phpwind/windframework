<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
header("Content-type: text/html; charset=gbk");
define('R_P', dirname(__FILE__));
define('F_P', R_P . '/../../wind/');
define('C_P', R_P . '/wind/');
define('COMPILE_PATH', R_P . '/cache');
require_once (F_P . '/wind.php');

W::application('SmartyDemo', R_P . '/config.xml')->run();
