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

require_once (C_P . '/config.php');
require_once (F_P . '/wind.php');

$haha = array('aa'=>'afa','asdfa','dddd','aa'=>'afa','dd'=>'eeeee','ffffff');




//trigger_error("afafa",E_USER_ERROR);
$frontController = new WFrontController();
$frontController->run();

$builder = new WMySqlBuilder();
	print_r($builder);
//throw new Exception('Uncaught Exception');