<?php
/**
 * 动态编译脚本,开发环境执行此文件,动态生成过程文件
 * @author wuq
 **/
define('_COMPILE_PATH', WIND_PATH . '_compile' . D_S);
Wind::clear();
Wind::import('WIND:core.*', true);
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
	$content[$value] = $key;
	$_key = Wind::getRealPath($key . '.' . self::$_extensions);
	$fileList[$_key] = array(
		$key, 
		$value);
}
$pack->packFromFileList($fileList, COMPILE_LIBRARY_PATH, WindPack::STRIP_PHP, true);
/* import信息写入编译文件 */
WindFile::write(_COMPILE_PATH . 'wind_imports.php', '<?php return ' . WindString::varToString($content) . ';');
/* 编译配置文件信息 */
$_systemConfig = Wind::getRealPath(WindFrontController::WIND_COMPONENT_CONFIG_RESOURCE);
$windConfigParser = new WindConfigParser();
$result = $windConfigParser->parse(_COMPILE_PATH . 'components_config.xml');
WindFile::write($_systemConfig, '<?php return ' . WindString::varToString($result) . ';');
