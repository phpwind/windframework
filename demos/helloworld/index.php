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
W::application('test')->run();
//W::application('test', R_P . 'config.xml')->run();
//W::application('test', R_P . 'config.php')->run();
//W::application('test', R_P . 'config.ini')->run();
//W::application('test', R_P . 'config.properties')->run();
echo microtime(true) - $start;
echo '<br>';
echo memory_get_usage()/1024/1024;