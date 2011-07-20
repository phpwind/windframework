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
		$allowType && $this->allowType = (array)$allowType;
		$atc_attachment = '';
		$uploaddb = $error = array();
		var_dump($_FILES);
		foreach ($_FILES as $key => $value) {
			if (is_array($value['name'])) {
				$temp = $this->multiUpload($key, $saveDir, $preFileName);
				$uploaddb[$key] = isset($uploaddb[$key]) ? array_merge((array)$uploaddb[$key], $temp) : $temp;
			} else {
				$uploaddb[$key][] = $this->simpleUpload($key, $saveDir, $preFileName);
			}
		}
		$this->errorInfo = $error;
		return $uploaddb;/*array($uploaddb, $errorType, $errorSize, $uploadError)*/;
	}
	
	/**
	 * 单个控件
	 * 一个表单中只有一个上传文件的控件
	 */
	public function simpleUpload($key, $saveDir, $preFileName = '') {
		return $this->doUp($key, $_FILES[$key], $saveDir, $preFileName);
	}
	
	/**
	 * 多个空间
	 * 一个表单中拥有多个上传文件的控件
	 */
	public function multiUpload($key, $saveDir, $preFileName = '') {
		$uploaddb = array();
		$files = $_FILES[$key];
		$num = count($files['name']);
		for($i = 0; $i < $num; $i ++) {
			$one = array();
			$one['name'] = $files['name'][$i];
			$one['tmp_name'] = $files['tmp_name'][$i];
			$one['error'] = $files['error'][$i];
			$one['size'] = $files['size'][$i];
			$one['type'] = $files['type'][$i];
			if (!($upload = $this->doUp($key, $one, $saveDir, $preFileName))) continue;
			$uploaddb[] = $upload;
		}
		return $uploaddb;
	}
	

	/**
	 * 执行上传操作
	 * 
	 * @param array $value
	 * @return array
	 */
	private function doUp($key, $value, $saveDir, $preFileName) {
		$atc_attachment = '';
		if (!$this->hasUploadedFile($value['tmp_name'])) return array();
		$atc_attachment = $value['tmp_name'];
		$upload = $this->initCurrUpload($key, $value);
		
		if (empty($upload['ext']) || !$this->checkAllowType($upload['ext'], array_keys($this->allowType))) {
			$this->errorInfo['type'][$key][] = $upload;
			$this->hasError = true;
			return array();
		}
		if ($upload['size'] < 1 || ($this->allowType && $upload['size'] > $this->allowType[$upload['ext']])) {
			$upload['maxSize'] = $this->allowType[$upload['ext']];
			$this->errorInfo['size'][$key][] = $upload;
			$this->hasError = true;
			return array();
		}
		$fileName = $this->getFileName($upload, $preFileName);
		$source = $this->getSavePath($fileName, $saveDir);
		
		if (!$this->postUpload($atc_attachment, $source)) {
			$upload['savePath'] = $source;
			$this->errorInfo['upload'][$key][] = $upload;
			$this->hasError = true;
			return array();
		}
		$upload['size'] = ceil(filesize($source) / 1024);
		$upload['savePath'] = $source;
		return $upload;
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
		$fileName = mt_rand(1, 10) . time() . substr(md5(time() . $info['attname'] . mt_rand(1, 10)), 10, 15) . '.' . $info['ext'];
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
		$arr = array('attname' => $key, 'name' => $value['name'], 'size' => intval($value['size']), 'type' => 'zip', 'ifthumb' => 0, 'fileuploadurl' => '');
		$arr['ext'] = strtolower(substr(strrchr($arr['name'], '.'), 1));
		return $arr;
	}
	
	/**
	 * 获得上传文件的的数组的可能key值
	 * @return array
	 */
	private function getUploadFileField() {
		return array('name', 'tmp_name', 'error', 'size', 'type');
	}

}