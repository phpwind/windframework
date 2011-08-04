<?php
Wind::import('WIND:component.upload.AbstractWindUpload');
/**
 * @author xiaoxiao <xiaoxia.xuxx@aliyun.com>  2011-7-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 * @package
 */
class WindFormUpload extends AbstractWindUpload {
	
	public function __construct($allowType = array()) {
		$this->setAllowType($allowType);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see AbstractWindUpload::postUpload()
	 */
	protected function postUpload($tmp_name, $filename) {
		if (strpos($filename, '..') !== false || strpos($filename, '.php.') !== false || preg_match('/\.php$/', $filename)) {
			exit('illegal file type!');
		}
		$this->createFolder(dirname($filename));
		if (function_exists("move_uploaded_file") && @move_uploaded_file($tmp_name, $filename)) {
			@unlink($tmp_name);
			@chmod($filename, 0777);
			return true;
		} elseif (@copy($tmp_name, $filename)) {
			@unlink($tmp_name);
			@chmod($filename, 0777);
			return true;
		} elseif (is_readable($tmp_name)) {
			Wind::import('WIND:component.utility.WindFile');
			WindFile::write($filename, WindFile::read($tmp_name));
			@unlink($tmp_name);
			if (file_exists($filename)) {
				@chmod($filename, 0777);
				return true;
			}
		}
		return false;
	}

}