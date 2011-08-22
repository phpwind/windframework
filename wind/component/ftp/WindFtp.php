<?php
Wind::import('COM:ftp.AbstractWindFtp');
/**
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * author xiaoxiao <x_824sina.com>
 * version 2011-7-29  xiaoxiao
 */
class WindFtp extends AbstractWindFtp {
	
	/**
	 * 被动模式是否开启
	 * var boolean
	 */
	private $isPasv = true;
	
	public function __construct($config = array()) {
		$this->connection($config);
	}

	private function connection($config = array()) {
		$this->initConfig($config);
		if (false === ($this->conn = ftp_connect($this->server, $this->port, $this->timeout))) {
			$this->showError('The ftp ' . $this->server . ' cann\'t connection!');
		}
		if (false == ftp_login($this->conn, $this->user, $this->pwd)) {
			$this->showError('Login error: ' . $this->user);
		}
		if ($this->isPasv) {
			ftp_pasv($this->conn, true);
		}
		$this->initRootPath();
		return true;
	}
	
	/**
	 * 获得链接
	 * return object
	 */
	private function getFtp() {
		if (is_resource($this->conn)) return $this->conn;
		$this->connection();
		return $this->conn;
	}

	/**
	 * (non-PHPdoc)
	 * see AbstractWindFtp::rename()
	 */
	public function rename($oldName, $newName) {
		return ftp_rename($this->getFtp(), $oldName, $newName);
	}

	/*
	 * (non-PHPdoc)
	 * see AbstractWindFtp::delete()
	 */
	public function delete($filename) {
		return ftp_delete($this->getFtp(), $filename);
	}

	/**
	 * (non-PHPdoc)
	 * see AbstractWindFtp::upload()
	 */
	public function upload($sourceFile, $desFile, $mode) {
		$mode = $this->getMode($sourceFile, $mode);
		if (!in_array(($savedir = dirname($desFile)), array('.', '/'))) {
			$this->mkdirs($savedir);
		}
		$desFile = $this->rootPath . WindSecurity::escapeDir($desFile);
		$result = ftp_put($this->getFtp(), $desFile, $sourceFile, $mode);
		if (false === $result) return false;
		$this->chmod($desFile, 0644);
		return $this->size($desFile);
	}

	/**
	 * 从服务器上将文件$filename读取到本地的localname文件中
	 * (non-PHPdoc)
	 * see AbstractWindFtp::download()
	 */
	public function download($filename, $localname = '', $mode = 'auto') {
		$mode = $this->getMode($filename, $mode);
		return ftp_get($this->getFtp(), $localname, $filename, $mode);
	}

	/*
	 * (non-PHPdoc)
	 * see AbstractWindFtp::fileList()
	 */
	public function fileList($dir = '') {
		return ftp_nlist($this->getFtp(), $dir);
	}

	/*
	 * (non-PHPdoc)
	 * see AbstractWindFtp::close()
	 */
	public function close() {
		is_resource($this->conn) && ftp_close($this->conn);
		$this->conn = null;
		return true;
	}
    
	/*
	 * (non-PHPdoc)
	 * see AbstractWindFtp::initConfig()
	 */
	public function initConfig($config) {
		if (!$config || !is_array($config)) return false;
		parent::initConfig($config);
		isset($config['ispasv']) && $this->isPasv = $config['ispasv'];
	}
	
	/*
	 * (non-PHPdoc)
	 * see AbstractWindFtp::mkdir()
	 */
	public function mkdir($dir, $permissions = 0777) {
		$result = ftp_mkdir($this->getFtp(), $dir);
		if (!$result) return false;
		return $this->chmod($result, $permissions);
	}
	
	/**
	 * 赋权限
	 * param string $file
	 * param int $permissions
	 * return boolean
	 */
	private function chmod($file, $permissions = 0777) {
		return ftp_chmod($this->getFtp(), $permissions, $file);
	}
	
	/*
	 * (non-PHPdoc)
	 * see AbstractWindFtp::pwd()
	 */
	protected function pwd() {
		return ftp_pwd($this->getFtp()) . '/';
	}

	/*
	 * (non-PHPdoc)
	 * see AbstractWindFtp::changeDir()
	 */
	public function changeDir($dir) {
		return ftp_chdir($this->getFtp(), $dir);
	}

	/*
	 * (non-PHPdoc)
	 * see AbstractWindFtp::size()
	 */
	public function size($file) {
		return ftp_size($this->getFtp(), $file);
	}
	
	/**
	 * 获得后缀和模式
	 * param string $filename
	 * param string $mode
	 * return string
	 */
	private function getMode($filename, $mode) {
		$ext = $this->getExt($filename);
		$mode = strtolower($mode);
		if ($mode == 'auto') {
			$ext = $this->getExt($filename);
			$mode = $this->getModeMap($ext);
		}
		return (strtolower($mode) == 'ascii') ? FTP_ASCII : FTP_BINARY;
	}

	/**
	 * 获得文件操作模式
	 * param	string
	 * return	string
	 */
	private function getModeMap($ext){
		$exts = array('txt', 'text', 'php', 'phps', 'php4', 'js', 'css',
				'htm', 'html', 'phtml', 'shtml', 'log', 'xml');
		return (in_array($ext, $exts)) ? 'ascii' : 'binary';
	}
}