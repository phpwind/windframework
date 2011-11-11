<?php
/**
 * 动态编译脚本,开发环境执行此文件,动态生成过程文件
 * @author wuq
 **/
error_reporting(E_ALL);
define('WIND_DEBUG', 1);
include '../wind/Wind.php';
define('_COMPILE_PATH', dirname(__FILE__) . '/');
Wind::clear();
Wind::import('WIND:base.*', true);
Wind::import('WIND:filter.*', true);
Wind::import('WIND:web.*', true);
Wind::import('WIND:router.*', true);
Wind::import('WIND:http.request.*', true);
Wind::import('WIND:http.response.*', true);
Wind::import('WIND:utility.*', true);

$imports = Wind::getImports();
/* 载入需要的文件信息 */
Wind::import('WIND:utility.WindPack');
Wind::import('WIND:utility.WindFile');
Wind::import('WIND:utility.WindString');
Wind::import('WIND:parser.WindConfigParser');

/* 打包 */
$pack = new WindPack();
$fileList = array();
$content = array();
foreach ($imports as $key => $value) {
	$_key = Wind::getRealPath($key);
	$fileList[$_key] = array($key, $value);
	$content[$value] = parseFilePath($key);
}
$pack->setContentInjectionCallBack('addImports');
$pack->packFromFileList($fileList, _COMPILE_PATH . 'wind_basic.php', WindPack::STRIP_PHP, true);
WindFile::write(_COMPILE_PATH . 'wind_imports.php', '<?php return ' . WindString::varToString($content) . ';');

/* 编译配置文件信息 */
$windConfigParser = new WindConfigParser();
$dh = opendir(_COMPILE_PATH . 'config');
while (($file = readdir($dh)) !== false) {
	if (is_file(_COMPILE_PATH . 'config/' . $file) && $file !== '.' && $file !== '..') {
		$result = $windConfigParser->parse(_COMPILE_PATH . 'config/' . $file);
		$file = preg_replace('/\.(\w)*$/i', '', $file);
		WindFile::write(_COMPILE_PATH . $file . '.php', '<?php return ' . WindString::varToString($result) . ';');
		//WindFile::write(WIND_PATH . $file . '.php', '<?php return ' . WindString::varToString($result) . ';');
	}
}

echo 'compile successful!';

/*********************************************************************/
/* 向wind包中注入imports文件目录信息 */
function addImports() {
	$_content = WindString::varToString($GLOBALS['imports']);
	$_content = str_replace(array("\r\n", "\t", " "), '', $_content);
	return 'Wind::setImports(' . $_content . ');';
}

/* 清理所有缓存 */
function parseFilePath($filePath) {
	list($namespace, $filePath) = explode(':', $filePath);
	return str_replace('.', '/', $filePath);
}