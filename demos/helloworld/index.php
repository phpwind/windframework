<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
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

$_GET['c'] = 'error';
/*$_GET['formName'] = 'userForm';
$_POST['username'] = 'asssss';*/


require '../../wind/component/db/base/WindDbAdapter.php';
require '../../wind/component/exception/WindException.php';
require '../../wind/component/exception/WindSqlException.php';

require '../../wind/component/db/WindMySql.php';
require '../../wind/component/db/WindMsSql.php';
require '../../wind/component/db/WindDbManager.php';

require '../../wind/component/config/base/IWindConfig.php';


$manager = WindDbManager::getInstance ( $config );
$db = $manager->dbDriverFactory ();
$db->query("select * from pw_members");
print_r($db->getLastInsertId());

W::application('TEST')->run();

