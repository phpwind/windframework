<?php
/**
 * 命令行方式打包工具
 * 
 * 支持参数<code>
 * -r	是否递归的方式打包文件夹
 * -p	将所列文件夹打包，合并成一个文件（去除空格，注释等）
 * example:
 * pack -pci /var/www/wind/utility /var/www/wind/base
 * 这个例子中包含两个文件夹utility、base(支持文件，文件夹；支持一个或者多个)。打包工具将这两个文件夹下面的文件打包到同一个文件下。
 * 并将 /var/www/wind/ 以WIND别名注册为系统目录别名
 * </code>
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license http://www.phpwind.com/license.php
 */
include '../../wind/Wind.php';
Wind::register(dirname(dirname(__FILE__)), '_COMPILE');
Wind::import('_COMPILE:lib.*');

$args = isset($_SERVER['argv']) ? $_SERVER['argv'] : array();
if (count($args) < 2) CompileCmdHelp::showError('Not enough arguments provided');
unset($args[0]);
$files = array();
$params = '';
foreach ($args as $value) {
	if (!$value) continue;
	if ($value[0] === '-')
		$params .= trim($value, '-');
	else
		$files[] = $value;
}
empty($files) && CompileCmdHelp::showError('Not enough arguments provided');

$params = '-' . $params;
$_p = strpos($params, 'p');
$_r = strpos($params, 'r');

$packFile = '';
if ($_p) {
	$packFile = CompileCmdHelp::getInput("Enter folder in which to save the pack file (default: /home/you/):");
	if (!$packFile) {
		$processUser = posix_getpwuid(posix_geteuid());
		$packFile = '/home/' . $processUser['name'] . '/';
	}
	$packFile .= 'wind_basic.php';
}

try {
	$pack = new CompilePack($_r, $_p);
	$pack->pack($files, $packFile);
} catch (Exception $e) {
	CompileCmdHelp::showError($e->getMessage());
}

CompileCmdHelp::showMessge("Operation was successful！\r\n");
$_p && CompileCmdHelp::showMessge(
	"Your pack file has been saved in: " . $packFile . " (" . filesize($packFile) . ")\r\n");
if ($pack->namespace) {
	CompileCmdHelp::showMessge(
		"If use the parameters (i or c), You need the 'wind_basic.php' is loaded before the registration of the following information to the system.\r\n");
	CompileCmdHelp::showMessge($pack->namespace);
}

do {
	$_op = CompileCmdHelp::getInput("Show Details(i/c/p):");
	switch ($_op) {
		case 'i':
			CompileCmdHelp::showMessge("The imports list:\r\n");
			CompileCmdHelp::showMessge($pack->imports);
			break;
		case 'c':
			CompileCmdHelp::showMessge("The classes list:\r\n");
			CompileCmdHelp::showMessge($pack->classes);
			break;
		case 'p':
			CompileCmdHelp::showMessge("The pack file list:\r\n");
			CompileCmdHelp::showMessge($pack->fileList);
			break;
		default:
			$_op = '';
			break;
	}
} while ($_op);

exit();








