<?php
Wind::import("WIND:ftp.AbstractWindFtp");
/**
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * author xiaoxiao <x_824sina.com>
 * version 2011-7-29  xiaoxiao
 */
@set_time_limit(1000);
class WindSocketFtp extends AbstractWindFtp {
	private $tmpConnection;
	
	public function __construct($config = array()) {
		$this->getConnection($config);
	}
	
	/**
	 * 获得ftp链接
	 * @param array $config
	 */
	private function getConnection($config) {
		$this->initConfig($config);
		$errno = 0;
		$errstr = '';
		$this->conn = fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
		if (!$this->conn || !$this->checkcmd()) {
			$this->showError('ftp_connect_failed');
		}
		stream_set_timeout($this->conn, $this->timeout);
		
		if (!$this->sendcmd('USER', $this->user)) {
			$this->showError('ftp_user_failed');
		}
		if (!$this->sendcmd('PASS', $this->pwd)) {
			$this->showError('ftp_pass_failed');
		}
		
		$this->initRootPath();
		return true;
	}

	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::pwd()
	 */
	protected function pwd() {
		$this->sendcmd('PWD', '', false);
		if (!($path = $this->checkcmd(true)) || !preg_match("/^[0-9]{3} \"(.+?)\"/", $path, $matchs)) {
			return '/';
		}
		return $matchs[1] . ((substr($matchs[1], -1) == '/') ? '' : '/');
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::upload()
	 */
	public function upload($localfile, $remotefile, $mode) {
		if ($this->checkFile($localfile)) {
			$this->showError("Error：illegal file type！（{$localfile}）");
		}
		if ($this->checkFile($remotefile)) {
			$this->showError("Error：illegal file type！（{$remotefile}）");
		}
		if (!in_array(($savedir = dirname($remotefile)), array('.', '/'))) {
			$this->mkdirs($savedir);
		}
		$remotefile = $this->rootPath . WindSecurity::escapeDir($remotefile);
		if (!($fp = fopen($localfile, 'rb'))) {
			$this->showError("Error：Cannot read file \"$localfile\"");
		}
		// 'I' == BINARY mode
		// 'A' == ASCII mode
		$mode != 'I' && $mode = 'A';
		$this->delete($remotefile);
		if (!$this->sendcmd('TYPE', $mode)) {
			$this->showError('Error：TYPE command failed');
		}
		$this->openTmpConnection();
		$this->sendcmd('STOR', $remotefile);
		while (!feof($fp)) {
			fwrite($this->tmpConnection, fread($fp, 4096));
		}
		fclose($fp);
		$this->closeTmpConnection();

		if (!$this->checkcmd()) {
			$this->showError('Error：PUT command failed');
		} else {
			$this->sendcmd('SITE CHMOD', base_convert(0644, 10, 8) . " $remotefile");
		}
		return $this->size($remotefile);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::download()
	 */
	public function download($localfile, $remotefile = '', $mode = 'I') {
		if ($this->checkFile($localfile)) {
			$this->showError("Error：illegal file type！（{$localfile}）");
		}
		if ($this->checkFile($remotefile)) {
			$this->showError("Error：illegal file type！（{$remotefile}）");
		}
		$mode != 'I' && $mode = 'A';
		if (!$this->sendcmd('TYPE', $mode)) {
			$this->showError('Error：TYPE command failed');
		}
		$this->openTmpConnection();
		if (!$this->sendcmd('RETR', $remotefile)) {
			$this->closeTmpConnection();
			return false;
		}
		if (!($fp = fopen($localfile, 'wb'))) {
			$this->showError("Error：Cannot read file \"$localfile\"");
		}
		while (!feof($this->tmpConnection)) {
			fwrite($fp, fread($this->tmpConnection, 4096));
		}
		fclose($fp);
		$this->closeTmpConnection();

		if (!$this->checkcmd()) $this->showError('Error：GET command failed');
		return true;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::size()
	 */
	public function size($file) {
		$this->sendcmd('SIZE', $file, false);
		if (!($size_port = $this->checkcmd(true))) {
			$this->showError('Error：Check SIZE command failed');
		}
		return preg_replace("/^[0-9]{3} ([0-9]+)\r\n/", "\\1", $size_port);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::delete()
	 */
	public function delete($file) {
		return $this->sendcmd('DELE', $this->rootPath . WindSecurity::escapeDir($file));
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::rename()
	 */
	public function rename($oldname, $newname) {
		if (!in_array(($savedir = dirname($newname)), array('.', '/'))) {
			$this->mkdirs($savedir);
		}
		$oldname = $this->rootPath . WindSecurity::escapeDir($oldname);
		$this->sendcmd('RNFR', $oldname);
		return $this->sendcmd('RNTO', $newname);
	}

	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::mkdir()
	 */
	public function mkdir($dir) {
		$base777 = base_convert(0777, 10, 8);
		$result = $this->sendcmd('MKD', $dir);
		return $this->sendcmd('SITE CHMOD', "$base777 $dir");
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::changeDir()
	 */
	public function changeDir($dir) {
		$dir = (($dir[0] != '/') ? '/' : '') . $dir;
		if ($dir !== '/' && substr($dir, -1) == '/') {
			$dir = substr($dir, 0, -1);
		}
		if (!$this->sendcmd('CWD', $dir)) {
			$this->showError('ftp_cwd_failed');
		}
		return true;
	}

	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::fileList()
	 */
	public function fileList($dir = '') {
		$this->openTmpConnection();
		$this->sendcmd('NLST', $dir);
		$list = array();
		while (!feof($this->tmpConnection)) {
			$list[] = preg_replace('/[\r\n]/', '', fgets($this->tmpConnection, 512));
		}
		$this->closeTmpConnection();
		if (!$this->checkcmd(true)) $this->showError('Error：LIST command failed');
		return $list;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindFtp::close()
	 */
	public function close() {
		if (!$this->conn) return false;
		if (!$this->sendcmd('QUIT') || !fclose($this->conn)) $this->showError('Error：QUIT command failed', false);
		return true;
	}

	/**
	 * 发送命令
	 * @param string $cmd
	 * @param string $args
	 * @param boolean $check
	 * @return boolean
	 */
	private function sendcmd($cmd, $args = '', $check = true) {
		!empty($args) && $cmd .= " $args";
		fputs($this->conn, "$cmd\r\n");
		if ($check === true && !$this->checkcmd()) return false;
		return true;
	}
	
	/**
	 * 检查命令状态
	 * @param boolean $return
	 * @return boolean
	 */
	private function checkcmd($return = false) {
		$resp = $rcmd = '';
		$i = 0;
		do {
			$rcmd = fgets($this->conn, 512);
			$resp .= $rcmd;
		} while (++$i < 20 && !preg_match('/^\d{3}\s/is', $rcmd));

		if (!preg_match('/^[123]/', $rcmd)) return false;
		return $return ? $resp : true;
	}

	/**
	 * 链接临时句柄
	 * @return boolean
	 */
	private function openTmpConnection() {
		$this->sendcmd('PASV', '', false);
		if (!($ip_port = $this->checkcmd(true))) {
			$this->showError('Error：Check PASV command failed');
		}
		if (!preg_match('/[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]+,[0-9]+/', $ip_port, $temp)) {
			$this->showError("Error：Illegal ip-port format($ip_port)");
		}
		$temp = explode(',', $temp[0]);
		$server_ip = "$temp[0].$temp[1].$temp[2].$temp[3]";
		$server_port = $temp[4] * 256 + $temp[5];
		if (!$this->tmpConnection = fsockopen($server_ip, $server_port, $errno, $errstr, $this->timeout)) {
			$this->showError("Error：Cannot open data connection to $server_ip:$server_port<br />Error：$errstr ($errno)");
		}
		stream_set_timeout($this->tmpConnection, $this->timeout);
		return true;
	}
	
	/**
	 * 关闭临时链接对象
	 * @return boolean
	 */
	private function closeTmpConnection() {
		return fclose($this->tmpConnection);
	}
}
?>