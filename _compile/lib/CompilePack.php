<?php
/**
 * wind工具库，编译打包类
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com/license.php
 * @version $Id$
 * @package wind
 */
class CompilePack {
	private $extensions = 'php';
	private $_r = false;
	private $_p = false;
	public $fileList = array();
	public $imports = array();
	public $classes = array();
	public $namespace = array();

	/**
	 * 默认为 r,p 都为 true
	 *
	 * @param boolean $r 是否递归文件夹
	 * @param boolean $p 是否生成打包
	 */
	public function __construct($r = false, $p = false) {
		$this->_r = $r;
		$this->_p = $p;
	}

	/**
	 * 将一组文件列表合并为一个文件列表
	 *
	 * @param array $files 文件/文件夹列表
	 * @param string $packPath 打包文件存放路径
	 * @param $boolean
	 */
	public function pack($files, $packPath = '', $namespace = '') {
		$namespace = explode(' ', $namespace);
		$i = 0;
		foreach ($files as $filePath) {
			if (!$filePath = realpath($filePath)) continue;
			$_dir = dirname($filePath);
			$_n = isset($namespace[$i]) ? $namespace[$i] : basename($_dir);
			$_n = array(strtoupper($_n), $_dir . '/');
			$this->_import($filePath, $_n);
			$i++;
		}
		if ($this->_p && $this->fileList) {
			Wind::import('WIND:utility.WindPack');
			$pack = new WindPack();
			$pack->setContentInjectionCallBack(array($this, 'injectionImports'));
			if (!$pack->packFromFileList($this->fileList, $packPath, WindPack::STRIP_PHP, true)) throw new Exception(
				'failed to create pack file (' . $packPath . ')');
		}
	}

	/**
	 * 向打包好的文件头部注入 import 信息
	 */
	public function injectionImports() {
		$_content = WindString::varToString($this->imports);
		$_content = str_replace(array("\r\n", "\t", " "), '', $_content);
		return 'Wind::$_imports += ' . $_content . ';';
	}

	/**
	 * @param string $filePath
	 * @throws Exception
	 */
	private function _import($filePath, $namespace) {
		if (is_dir($filePath)) {
			if (false === ($files = scandir($filePath, 0))) throw new Exception(
				'the file ' . $filePath . ' open failed!');
			foreach ($files as $file) {
				if (!$file || $file[0] === '.') continue;
				if (!$this->_r && is_dir($filePath . '/' . $file)) continue;
				$this->_import($filePath . '/' . $file, $namespace);
			}
		} elseif (is_file($filePath)) {
			$_info = pathinfo($filePath);
			if ($_info['extension'] !== 'php') return;
			$this->fileList[$_info['filename']] = $filePath;
			$_file = $_info['dirname'] . '/' . $_info['filename'];
			$_key = str_replace(array($namespace[1], '/'), array($namespace[0] . ':', '.'), $_file);
			$this->imports[$_key] = $_info['filename'];
			$this->classes[$_info['filename']] = str_replace($namespace[1], '', $_file);
			$this->namespace[$namespace[0]] = $namespace[1];
			Wind::$_classes[$_info['filename']] = $_file;
		} else
			throw new Exception($filePath . ': No such file or directory');
	}
}

?>