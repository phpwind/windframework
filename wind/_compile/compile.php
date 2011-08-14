<?php
/**
 * 动态编译脚本,开发环境执行此文件,动态生成过程文件
 * @author wuq
 **/
define('_COMPILE_PATH', WIND_PATH . '_compile' . D_S);
define('_COMPILE_LIBRARY_PATH', WIND_PATH . 'wind_basic.php');
Wind::clear();
Wind::import('COM:log.WindLogger');
Wind::import('WIND:core.*', true);
Wind::import('COM:filter.*', true);
Wind::import('COM:parser.WindConfigParser', true);
Wind::import('COM:http.request.*', true);
Wind::import('COM:http.response.*', true);
Wind::import('COM:router.*', true);

$imports = Wind::getImports();
/* 载入需要的文件信息 */
Wind::import('COM:utility.WindPack');
Wind::import('COM:utility.WindFile');
Wind::import('COM:utility.WindString');
/* 打包 */
$pack = new WindPack();
$fileList = array();
$content = array();
foreach ($imports as $key => $value) {
	$_key = Wind::getRealPath($key);
	$fileList[$_key] = array($key, $value);
	$content[$value] = parseFilePath($key);
}
$pack->packFromFileList($fileList, _COMPILE_LIBRARY_PATH, WindPack::STRIP_PHP, true);
/* import信息写入编译文件 */
WindFile::write(WIND_PATH . 'wind_imports.php', 
	'<?php return ' . WindString::varToString($content) . ';');

/* 编译配置文件信息 */
$_systemConfig = Wind::getRealPath('WIND:components_config');
$windConfigParser = new WindConfigParser();
$result = $windConfigParser->parse(_COMPILE_PATH . 'components_config.xml');
WindFile::write($_systemConfig, '<?php return ' . WindString::varToString($result) . ';');

/* 清理所有缓存 */
function parseFilePath($filePath) {
	list($namespace, $filePath) = explode(':', $filePath);
	return str_replace('.', '/', $filePath);
}
