<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
$start = microtime(true);
header("Content-type: text/html; charset=utf8");
define('R_P', dirname(__FILE__) . '/');
define('IS_DEBUG', true);
/* 框架文件路径 */
define('FREAMWORK_PATH', R_P . '/../../wind/');
/* 缓存文件路径 */
define('COMPILE_PATH', R_P . 'compile/');
require_once (FREAMWORK_PATH . '/wind.php');

/*$_GET['formName'] = 'userForm';
$_POST['username'] = 'asssss';*/

W::application('HelloWorld')->run();
echo microtime(true) - $start;