<?php

Wind::import('WIND:upload.AbstractWindUpload');
/**
 * 
 * the last known user to change this file in the repository  <LastChangedBy: xiaoxiao >
 * @author xiaoxiao <x_824@sina.com>
 * @version 2011-7-29  xiaoxiao
 */
class WindFtpUpload extends AbstractWindUpload {
	private $config = array();
	
	private $ftp = null;
	
	public function __construct($config) {
		$this->setConfig($config);
	}

	/**
	 * (non-PHPdoc)
	 * @see AbstractWindUpload::postUpload()
	 */
	protected function postUpload($tmp_name, $filename) {
		$ftp = $this->getFtpConnection();
		if (!($size = $ftp->upload($tmp_name, $filename))) return false;
		@unlink($tmp_name);
		return $size;
	}
	
	/**
	 * 设置配置文件
	 * @param array $config
	 * @return bool
	 */
	public function setConfig($config) {
		if (!is_array($config)) return false;
		$this->config = $config;
		return true;
	}
    
	/**
	 * 获得ftp链接对象
	 * @return AbstractWindFtp
	 */
	private function getFtpConnection() {
		if (is_object($this->ftp)) return $this->ftp;
		if (function_exists('ftp_connect')) {
			Wind::import("WIND:ftp.WindFtp");
			$this->ftp = new WindFtp($this->config);
			return $this->ftp;
		}
		Wind::import("WIND:ftp.WindSocketFtp");
		$this->ftp = new WindSocketFtp($this->config);
		return $this->ftp;
	}
}