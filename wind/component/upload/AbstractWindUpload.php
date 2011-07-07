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
	
	protected $hasError = false;

	/**
	 * 上传文件
	 * @param string $saveDir
	 * @param string $fileName
	 * @param array  $allowType   格式为  array(ext=>size) size单位为b
	 * @return array|string
	 */
	public abstract function upload($saveDir, $fileName = '', $allowType = array());
	
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
	public abstract function getErrorInfo($errorType = '');
	
	/**
	 * 檢查文件是否允許上傳
	 * @param string $ext
	 * @param array $allowType
	 * @return bool
	 */
	protected function checkAllowType($ext, $allowType) {
		return $allowType ? in_array($ext, (array)$allowType) : true;
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
}