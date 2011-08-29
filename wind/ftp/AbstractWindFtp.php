<?php

/**
 * ftp类的父类
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-8-1  xiaoxiao
 */
abstract class AbstractWindFtp {
	protected $server = '';
	protected $port = 21;
	protected $user = '';
	protected $pwd = '';
	protected $dir = '';
	protected $timeout = 10;
	protected $rootPath = '';
	
	protected $conn = null;
	/**
	 * 初始化配置信息
	 * @param array $config
	 * @return bool
	 */
	public function initConfig($config) {
		if (!$config || !is_array($config)) return false;
		isset($config['server']) && $this->server = $config['server'];
		isset($config['port']) && $this->port = $config['port'];
		isset($config['user']) && $this->user = $config['user'];
		isset($config['pwd']) && $this->pwd = $config['pwd'];
		isset($config['dir']) && $this->dir = $config['dir'];
		isset($config['timeout']) && $this->timeout = $config['timeout'];
		return true;
	}
	
	/**
	 * 重命名文件
	 * @param string $oldName
	 * @param string $newName
	 * @return boolean
	 */
	abstract public function rename($oldName, $newName);
	/**
	 * 删除文件
	 * @param string $filename
	 * @return boolean
	 */
	abstract public function delete($filename);
	/**
	 * 上传文件
	 * @param string $sourceFile
	 * @param string $desFile
	 * @param string $mode
	 * @return mixed
	 */
	abstract public function upload($sourceFile, $desFile, $mode);
	/**
	 * 下载文件
	 * @param string $filename
	 * @param string $localname
	 * @param string $mode
	 * @return string
	 */
	abstract public function download($filename);
	/**
	 * 列出文件列表
	 * @param string $dir
	 * @return array
	 */
	abstract public function fileList($dir = '');
	/**
	 * 关闭链接
	 * @return boolean
	 */
	abstract public function close();
	
	/**
	 * 传建文件夹
	 * @param string $dir
	 * @return boolean
	 */
	abstract public function mkdir($dir);
	
	/**
	 * 更改当前目录
	 * @param string $dir
	 * @return boolean
	 */
	abstract public function changeDir($dir);
	
	/**
	 * 获得文件大小
	 * @param string $file
	 * @return boolean
	 */
	abstract public function size($file);
	
	/**
	 * 获得当前路径
	 * @return string
	 */
	abstract protected function pwd();
	
	/**
	 * 级联创建文件夹
	 * @param string $dir
	 * @param string $permissions
	 * @return boolean
	 */
	public function mkdirs($dir, $permissions = 0777) {
		$dir = explode('/', WindSecurity::escapeDir($dir));
		$dirs = '';
		$result = false;
		$count = count($dir);
		for ($i = 0; $i < $count; $i++) {
			if (strpos($dir[$i], '.') === 0) continue;
			$result = $this->mkdir($dir[$i], $permissions);
			$this->changeDir($this->rootPath . $dirs . $dir[$i]);
			$dirs .= "$dir[$i]/";
		}
		$this->changeDir($this->rootPath);
		return $result;
	}
	
		
	/**
	 * 检查文件是否存在
	 * @param string $filename
	 * @return boolean
	 */
	public function file_exists($filename) {
		$directory = substr($filename, 0, strrpos($filename, '/'));
		$filename = str_replace("$directory/", '', $filename);
		if ($directory) {
			$directory = $this->rootPath . $directory . '/';
		} else {
			$directory = $this->rootPath;
		}
		$this->changeDir($directory);
		$list = $this->fileList();
		$this->changeDir($this->rootPath);
		if (!empty($list) && in_array($filename, $list)) return true;
		return false;
	}
	
	/**
	 * 初始化根目录信息
	 */
	protected function initRootPath() {
		$this->rootPath = $this->pwd();
		if ($this->dir) {
			$this->rootPath .= trim(str_replace('\\', '/', $this->dir), '/') . '/';
		}
		$this->changeDir($this->rootPath);
	}
	
	/**
	 * 检查文件
	 * @param string $filename
	 * @return boolean
	 */
	protected function checkFile($filename) {
		return (str_replace(array('..', '.php.'), '', $filename) != $filename || preg_match('/\.php$/i', $filename));
	}
	
	/**
	 * 获得文件后缀
	 *
	 * @param	string
	 * @return	string
	 */
	protected function getExt($filename){
		if (false === strpos($filename, '.')) return 'txt';
		$x = explode('.', $filename);
		return strtolower(end($x));
	}
	
	/**
	 * 显示错误信息
	 * @param string $str
	 */
	protected function showError($str, $close = true) {
		$close && $this->close();
		exit($str);
	}
}