<?php
/**
 * 动态编译脚本,开发环境执行此文件,动态生成过程文件
 * @author wuq
 **/
error_reporting(E_ALL);
include '../wind/Wind.php';
define('_COMPILE_PATH', dirname(__FILE__) . '/');
Wind::clear();
Wind::import('COM:log.WindLogger');
Wind::import('WIND:core.*', true);
Wind::import('COM:parser.*', true);
Wind::import('COM:router.*', true);
Wind::import('COM:http.*', true);

$imports = Wind::getImports();
/* 载入需要的文件信息 */
Wind::import('COM:utility.WindPack');
Wind::import('COM:utility.WindFile');
Wind::import('COM:utility.WindString');
Wind::import('COM:parser.WindConfigParser');

/* 打包 */
$pack = new WindPack();
$fileList = array();
$content = array();
foreach ($imports as $key => $value) {
	$_key = Wind::getRealPath($key);
	$fileList[$_key] = array($key, $value);
	$content[$value] = parseFilePath($key);
}
$pack->packFromFileList($fileList, _COMPILE_PATH . 'wind_basic.php', WindPack::STRIP_PHP, true);
/* import信息写入编译文件 */
WindFile::write(_COMPILE_PATH . 'wind_imports.php', 
	'<?php return ' . WindString::varToString($content) . ';');

/* 编译配置文件信息 */
$windConfigParser = new WindConfigParser();
$dh = opendir(_COMPILE_PATH . 'config');
while (($file = readdir($dh)) !== false) {
	if (is_file(_COMPILE_PATH . 'config/' . $file) && $file !== '.' && $file !== '..') {
		$result = $windConfigParser->parse(_COMPILE_PATH . 'config/' . $file);
		$file = preg_replace('/\.(\w)*$/i', '', $file);
		WindFile::write(_COMPILE_PATH . $file . '.php', 
			'<?php return ' . WindString::varToString($result) . ';');
	}
}

echo 'compile successful!';

/* 清理所有缓存 */
function parseFilePath($filePath) {
	list($namespace, $filePath) = explode(':', $filePath);
	return str_replace('.', '/', $filePath);
}
