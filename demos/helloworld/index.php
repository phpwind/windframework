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


Wlog::add("afdafafa",'afda');
function a(){
	echo '<br/>';
throw new WException('ddddd');	
}
echo 1111;
//a();
echo 222;
$frontController = new WFrontController();
$frontController->run();







//throw new Exception('Uncaught Exception');