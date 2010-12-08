<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-8
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

header("Content-type: text/html; charset=utf8");
define('R_P', dirname(__FILE__) . '/');
/* 框架文件路径 */
define('FREAMWORK_PATH', R_P . '/../../wind/');
/* 缓存文件路径 */
define('COMPILE_PATH', R_P . 'compile/');
require_once (FREAMWORK_PATH . '/wind.php');
W::application('TEST')->run();
