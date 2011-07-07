<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

Wind::import('WIND:component.upload.AbstractWindUpload');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: yishuo $>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id: WindFormUpload.php 1994 2011-06-16 04:19:05Z yishuo $ 
 * @package 
 */
class WindFormUpload extends AbstractWindUpload {
	private $errorInfo = array('type' => array(), 'size' => array(), 'upload' => array());
	
	private $allowType = array();//允许上传的类型及对应的大小，array(ext=>size);
	
	public function __construct($allowType = array()) {
		$allowType && $this->allowType = $allowType;	
	}
	
	/**
	 * 
	 * (non-PHPdoc)
	 * @see AbstractWindUpload::upload()
	 */
	public function upload($saveDir, $preFileName = '', $allowType = array()) {
		$allowType = $allowType ? (array)$allowType : $this->allowType;
		$atc_attachment = '';
		$uploaddb = $error = array();
		foreach ($_FILES as $key => $value) {
			if (!$this->hasUploadedFile($value['tmp_name'])) continue;
			$atc_attachment = $value['tmp_name'];
			$upload = $this->initCurrUpload($key, $value);
			
			if (empty($upload['ext']) || !$this->checkAllowType($upload['ext'], array_keys($allowType))) {
				$error['type'][] = $upload;
				$this->hasError = true;
				continue;
			}
			if ($upload['size'] < 1 || $upload['size'] > $allowType[$upload['ext']]) {
				$upload['maxSize'] = $allowType[$upload['ext']];
				$error['size'][] = $upload;
				$this->hasError = true;
				continue;
			}
			$fileName = $this->getFileName($upload, $preFileName);
			$source = $this->getSavePath($fileName, $saveDir);
			
			if (!$this->postUpload($atc_attachment, $source)) {
				$upload['savePath'] = $source;
				$error['upload'][] = $upload;
				$this->hasError = true;
				continue;
			}
			clearstatcache();
			$upload['size'] = ceil(filesize($source) / 1024);
			$upload['savePath'] = $source;
			$uploaddb[] = $upload;
		}
		$this->errorInfo = $error;
		return $uploaddb;/*array($uploaddb, $errorType, $errorSize, $uploadError)*/;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AbstractWindUpload::getErrorInfo()
	 */
	public function getErrorInfo($type = 'all') {
		return isset($this->errorInfo[$type]) ? $this->errorInfo[$type] : $this->errorInfo;
	}
	
	/**
	 * 设置允许上传的类型
	 * @param array $allowType
	 * @return 
	 */
	public function setAllowType($allowType) {
		$allowType && $this->allowType = $allowType;	
	}
	
	/**
	 * 执行上传操作
	 */
	private function postUpload($tmp_name, $filename) {
		if (strpos($filename, '..') !== false || strpos($filename, '.php.') !== false || preg_match("/\.php$/", $filename)) {
			exit('illegal file type!');
		}
		$this->createFolder(dirname($filename));
		if (function_exists("move_uploaded_file") && @move_uploaded_file($tmp_name, $filename)) {
			@chmod($filename, 0777);
			return true;
		} elseif (@copy($tmp_name, $filename)) {
			@chmod($filename, 0777);
			return true;
		} elseif (is_readable($tmp_name)) {
			Wind::import('WIND:component.utility.WindFile');
			WindFile::write($filename, WindFile::read($tmp_name));
			if (file_exists($filename)) {
				@chmod($filename, 0777);
				return true;
			}
		}
		return false;
	}

	/**
	 * 获得保存路径
	 * @param string $fileName
	 * @param string $saveDir
	 * @return string
	 */
	private function getSavePath($fileName, $saveDir) {
		return rtrim($saveDir, '\\/') . '/' . $fileName;
	}

	/**
	 * 获得文件名字
	 * @param array $info
	 * @param string $preFileName
	 * @return string
	 */
	private function getFileName($info, $preFileName = '') {
		$fileName = mt_rand(1, 10) . time() . substr(md5(time() . $info['id'] . mt_rand(1, 10)), 10, 15) . '.' . $info['ext'];
		return $preFileName ? $preFileName . $fileName : $fileName;
	}

	/**
	 * 判断是否有上传文件
	 * @param string $tmp_name
	 * @return boolean
	 */
	private function hasUploadedFile($tmp_name) {
		if (!$tmp_name || $tmp_name == 'none') {
			return false;
		} elseif (function_exists('is_uploaded_file') && !is_uploaded_file($tmp_name) && !is_uploaded_file(str_replace('\\\\', '\\', $tmp_name))) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 初始化上传的文件信息
	 * @param  string $key
	 * @param  string $value
	 */
	private function initCurrUpload($key, $value) {
		list($t, $i) = explode('_', $key);
		$arr = array('id' => intval($i), 'attname' => $t, 'name' => $value['name'], 'size' => intval($value['size']), 'type' => 'zip', 'ifthumb' => 0, 'fileuploadurl' => '');
		$arr['ext'] = strtolower(substr(strrchr($arr['name'], '.'), 1));
		return $arr;
	}

}