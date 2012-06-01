<?php
/**
 * 动态编译脚本,开发环境执行此文件,动态生成过程文件
 * @author wuq
 **/
error_reporting(E_ALL);

define('WIND_DEBUG', 1);
define('EXT', '.php');
define('_COMPILE_PATH', dirname(__FILE__) . '/');
include '../../wind/Wind.php';

//检查是否在命令行下以及目录是否可写
if (!$_SERVER['argv']) e('Script should be run at command line');
if (!is_writable(WIND_PATH)) e('WIND_PATH not writable');

//检查输入的路径
$folders = array_slice($_SERVER['argv'], 1);
if (empty($folders) || in_array('--help', $folders)) {
	$message = <<<EOA
	这是一个动态编译脚本，你可以输入需要打包的文件夹进行打包。\n
	每一个输入的目录可被注册为命名空间，你可以通过这个命令空间来引入这个目录里打包好的类。\n
	例如：php compile.php ../wind/base ../wind/cache [...] \n
	对于'../wind/base'默认将用'BASE:'作为别名，你也可以输入自定义的'WIND:base'来作为这个目录的别名 \n
	注：此处的别名必须是你使用Wind::import方法导入类将使用的真实目录别名
EOA;
	e($message);
}

//遍历各个路径
$classes = $imports = $fileList = array();
foreach ($folders as $folder) {
	$alias = '';
	if (!is_dir($folder)) e("'$folder' is not a real directory!\n");
	$alias = strtoupper(basename($folder)) . ':';
	$r = getLine("'$folder' is to be register as '$alias' ? (Y|N) ");
	if (strtolower($r[0]) != 'y') $alias = getLine(
		'Please input the relative path using namespace: ');
	$fileList += readRecur(realpath($folder), $alias);
}

/* 载入需要的文件信息 */
Wind::import('WIND:utility.WindPack');

/* 打包 */
$pack = new WindPack();
$pack->packFromFileList($fileList, _COMPILE_PATH . 'wind_basic.php', WindPack::STRIP_PHP, true);
$message = array();
$message[] = "COMPILE: pack core file successful~";

/*装载imports和classes*/
$data = '<?php Wind::$_imports += ' . var_export($imports, true) . ';' . 'Wind::$_classes += ' . var_export(
	$classes, true) . ';';
WindFile::write(_COMPILE_PATH . 'wind_imports.php', $data);
$message[] = "COMPILE: wind_imports.php successful~";
$message[] = '';
exit(implode("\n", $message));

/**
 * 递归目录
 *
 * @param string $dir
 * @return array
 */
function readRecur($dir, $alias) {
	static $fileList = array();
	if (false === ($files = scandir($dir, 0))) e("$dir opened failed\n");
	foreach ($files as $file) {
		if ($file[0] === '.') continue;
		$pos = strrpos($file, '.');
		if ($pos !== false && substr($file, $pos) === EXT) {
			$fileName = substr($file, 0, $pos);
			$fileList[$dir . '/' . $file] = array('', $fileName);
			$classpath = getAlias($alias) . $fileName;
			$GLOBALS['imports'] += array($classpath => $fileName);
			$GLOBALS['classes'] += array($fileName => $dir . '/' . $fileName);
		} else if (is_dir($dir . '/' . $file)) {
			readRecur($dir . '/' . $file, $alias . '.' . $file);
		}
	}
	return $fileList;
}

/**
 * 输出错误信息
 *
 * @param string $message
 */
function e($message) {
	exit($message);
}

/**
 * 交互信息
 *
 * @param string $message
 * @return string
 */
function getLine($message) {
	echo $message;
	return fgets(STDIN);
}

/**
 * 处理目录别名
 *
 * @param string $alias
 * @return Ambigous <string, unknown>
 */
function getAlias($alias) {
	return substr($alias, '-1') == ':' ? $alias : $alias . '.';
}
