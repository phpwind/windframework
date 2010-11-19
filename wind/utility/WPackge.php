<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 程序打包工具
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WPackge{ 
	
	/*
	
	*/
	public function  stripWhiteSpace($filename){
		php_strip_whitespace($filename);
	}
	//  asdfafaf afafa
	public function stripComment($content){
		return preg_replace("/(?:\/\*.*\*\/)*|(?:\/\/[^\r\n\'\"]*[\r\n])*/Us",'',$content);
	}
	
	public function stripNR($content){
		return preg_replace("/[\n|\r]{2,}/","\n",$content);
	}
	
	public function stripSpace($content){
		return preg_replace("/[\s| |\t]{2,}/",' ',$content);
	}
	public function stripWriteSpace($content){
		
	}
	
	public function stripPhpIdentify($content){
		return preg_replace("/(?:<\?(?:php)*)|(\?>)/i",'',$content);
	}
	
	public function readContentFromFile($filename){
		if($this->isFile($filename)){
			$fp = fopen($filename, "r");
			$content = fread($fp, filesize ($filename));
			fclose($fp);
			return $content;
		}
		return false;
	}
	
	public function readContentFromDir($dir,$ndir = array('.','..','.svn')){
		static $content = array();
		if($this->isDir($dir)){
			$handle = dir($dir);
			while(false != ($tmp = $handle->read())){
				$name = $this->realDir($dir).$tmp;
				if($this->isDir($name) && !in_array($tmp,$ndir)){
					$this->readContentFromDir($name);
				}
				if($this->isFile($name)){
					$content[$dir] = $this->readContentFromFile($name);
				}
			}
			$handle->close();
		}
		return $content;
	}
	
	public function realDir($path){
		if(($pos = strrpos($path,DIRECTORY_SEPARATOR)) === strlen($path) - 1){
			return $path;
		}
		return $path.DIRECTORY_SEPARATOR;
	}
	
	public function isFile($filename){
		return is_file($filename);
	}
	
	public function isDir($dir){
		return is_dir($dir);
	}
	
	public function packge($dir,$dst = '',$ndir = array('.','..','.svn')){
		if(!($content = $this->readContentFromDir($dir,$ndir))){
			return false;
		}
		$content = implode("\n\r",$content);
		$content = $this->stripComment($content);
		$content = $this->stripPhpIdentify($content);
		$content = $this->stripNR($content);
		$contnet = $this->stripSpace($content);
		echo $content;
		return true;
		
	}
	
	public function packgeByDir($dir){
		
	}
	
	public function packgeByTime($dir){
	}
	
	
}
$dir =  substr(__FILE__,0,strrpos(__FILE__,DIRECTORY_SEPARATOR));

$pack = new WPackge();
//echo php_strip_whitespace(__FILE__);
$content = $pack->packge($dir);

