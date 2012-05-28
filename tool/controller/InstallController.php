<?php
Wind::import('WIND:command.WindCommandController');
Wind::import('WIND:utility.WindFolder');
Wind::import('WIND:utility.WindFile');
/**
 * 快速创建工程
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $id$
 * @package wind
 */
class InstallController extends WindCommandController {
	
	public function run($root) {
		$this->frameworkAction($root);
	}
	
	public function frameworkAction($root) {
		$dir = Wind::getRealDir('WIND:');
		$files = $this->_readRecursive(realpath($dir), '/var/www/laboratory/tool/demo/wind');
		foreach ($files as $src => $tgt) {
			WindFolder::mkRecur(dirname($tgt));
			echo 'generate ' . $tgt;
			$r = WindFile::write($tgt, WindFile::read($src)) ? " success \n" : 'fail';
			echo $r;
		}
	}
	
	protected function _readRecursive($dir, $target) {
		if (!$handle = @opendir($dir)) return array();
		static $files = array();
		while (false !== ($file = @readdir($handle))) {
			if ('.' === $file || '..' === $file || '.' === $file[0]) continue;
			if (WindFolder::isDir($dir . '/' . $file)) $this->_readRecursive($dir . '/' . $file, $target . '/' . $file);
			if (WindFile::isFile($dir . '/' . $file)) $files[$dir . '/' . $file] = $target . '/' . $file;
		}
		@closedir($handle);
		return $files;
	}
}

?>