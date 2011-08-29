<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-13
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
Wind::import('WIND:component.utility.Security');
Wind::import('WIND:component.utility.WindFile');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: yishuo $>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id: AbstractWindUpload.php 1994 2011-06-16 04:19:05Z yishuo $ 
 * @package 
 */
abstract class AbstractWindUpload {
	
	/**
	 * 是否有错误产生
	 * @var boolean
	 */
	protected $hasError = false;
	
	/**
	 * 错误信息
	 * @var array
	 */
	protected $errorInfo = array('type' => array(), 'size' => array(), 'upload' => array());
	
	/** 
	 * 允许的类型
	 * @var array
	 */
	protected $allowType = array();//允许上传的类型及对应的大小，array(ext=>size);

	/**
	 * 上传文件
	 * @param string $saveDir
	 * @param string $fileName
	 * @param array  $allowType   格式为  array(ext=>size) size单位为b
	 * @return array|string
	 */
	public function upload($saveDir, $preFileName = '', $allowType = array()) {
		$this->setAllowType($allowType);
		$uploaddb = array();
		foreach ($_FILES as $key => $value) {
			if (is_array($value['name'])) {
				$temp = $this->multiUpload($key, $saveDir, $preFileName);
				$uploaddb[$key] = isset($uploaddb[$key]) ? array_merge((array)$uploaddb[$key], $temp) : $temp;
			} else {
				$uploaddb[$key][] = $this->simpleUpload($key, $saveDir, $preFileName);
			}
		}
		return 1 == count($uploaddb) ? array_shift($uploaddb) : $uploaddb;
	}
	
	/**
	 * 单个控件
	 * 一个表单中只有一个上传文件的控件
	 */
	private function simpleUpload($key, $saveDir, $preFileName = '') {
		return $this->doUp($key, $_FILES[$key], $saveDir, $preFileName);
	}
	
	/**
	 * 多个控件
	 * 一个表单中拥有多个上传文件的控件
	 */
	private function multiUpload($key, $saveDir, $preFileName = '') {
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
	 * @param string $tmp_name
	 * @param string $filename
	 * @return bool
	 */
	abstract protected function postUpload($tmp_name, $filename);
	
	/**
	 * 返回是否含有错误
	 * @return boolean
	 */
	public function hasError() {
		return $this->hasError;
	}
	
	/**
	 * 返回错误信息
	 * @param string $errorType
	 * @return string|mixed
	 */
	public function getErrorInfo($errorType = '') {
		return isset($this->errorInfo[$errorType]) ? $this->errorInfo[$errorType] : $this->errorInfo;
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
	 * 檢查文件是否允許上傳
	 * @param string $ext
	 * @return bool
	 */
	protected function checkAllowType($ext) {
		$allowType = array_keys((array)$this->allowType);
		return $allowType ? in_array($ext, $allowType) : true;
	}
	
	/**
	 * 检查上传文件的大小
	 * @param string $type
	 * @param string $uploadSize
	 * @return bool
	 */
	protected function checkAllowSize($type, $uploadSize) {
		if ($uploadSize < 0) return false;
		if (!$this->allowType || !$this->allowType[$type]) return true;
		return $uploadSize < $this->allowType[$type];
	}
	

	/**
	 * 获得文件名字
	 * @param array $attInfo
	 * @param string $preFileName
	 * @return string
	 */
	protected function getFileName($attInfo, $preFileName = '') {
		$fileName = mt_rand(1, 10) . time() . substr(md5(time() . $attInfo['attname'] . mt_rand(1, 10)), 10, 15) . '.' . $attInfo['ext'];
		return $preFileName ? $preFileName . $fileName : $fileName;
	}

	/**
	 * 获得保存路径
	 * @param string $fileName
	 * @param string $saveDir
	 * @return string
	 */
	protected function getSavePath($fileName, $saveDir) {
		return $saveDir ? rtrim($saveDir, '\\/') . '/' . $fileName : $fileName;
	}
	
	/**
	 * 判断是否有上传文件
	 * @param string $tmp_name
	 * @return boolean
	 */
	protected function isUploadFile($tmp_name) {
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
	 * @param string $preFileName 前缀
	 */
	protected function initUploadInfo($key, $value, $preFileName, $saveDir) {
		$arr = array('attname' => $key, 'name' => $value['name'], 'size' => $value['size'], 'type' => $value['type'], 'ifthumb' => 0, 'fileuploadurl' => '');
		$arr['ext'] = strtolower(substr(strrchr($arr['name'], '.'), 1));
		$arr['filename'] = $this->getFileName($arr, $preFileName);
		$arr['fileuploadurl'] = $this->getSavePath($arr['filename'], $saveDir);
		return $arr;
	}
	
	
	/**
	 * 判断是否使图片，如果使图片则返回
	 * @param string $ext;
	 * @return boolean
	 */
	protected function isImage($ext) {
		return in_array($ext, array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'swf'));
	}
	

	/**
	 * 创建文件夹
	 * @param string $path
	 * @return boolean
	 */
	protected function createFolder($path) {
		if (!is_dir($path)) {
			$this->createFolder(dirname($path));
			@mkdir($path);
			@chmod($path, 0777);
			@fclose(@fopen($path . '/index.html', 'w'));
			@chmod($path . '/index.html', 0777);
		}
		return true;
	}
	
	/**
	 * 执行上传操作
	 * 
	 * @param array $value
	 * @return array
	 */
	protected function doUp($key, $value, $saveDir, $preFileName) {
		if (!$this->isUploadFile($value['tmp_name'])) return array();
		$upload = $this->initUploadInfo($key, $value, $preFileName, $saveDir);
		
		if (empty($upload['ext']) || !$this->checkAllowType($upload['ext'])) {
			$this->errorInfo['type'][$key][] = $upload;
			$this->hasError = true;
			return array();
		}
		if (!$this->checkAllowSize($upload['ext'], $upload['size'])) {
			$upload['maxSize'] = $this->allowType[$upload['ext']];
			$this->errorInfo['size'][$key][] = $upload;
			$this->hasError = true;
			return array();
		}
		if (!($uploadSize = $this->postUpload($value['tmp_name'], $upload['fileuploadurl']))) {
			$this->errorInfo['upload'][$key][] = $upload;
			$this->hasError = true;
			return array();
		}
		$upload['size'] = intval($uploadSize);
		return $upload;
	}
}