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
		return preg_replace("/(?:\n|\r)*/",' ',$content);
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
	
	public function readContentFromDir($dir){
		if($this->isDir($dir)){
			
		}
	}
	
	public function isFile($filename){
		return is_file($filename);
	}
	
	public function isDir($dir){
		return is_dir($filename);
	}
	
	public function packge($dir = ''){
		$dir = is_string($dir) ? array($dir) : $dir;
		foreach($dir as $key=>$dir){
			
		}
	}
	
	public function packgeByDir($dir){
		
	}
	
	public function packgeByTime($dir){
		
	}
	
	
}

$pack = new WPackge();
//echo php_strip_whitespace(__FILE__);
echo '<br/>';
echo $pack->stripSpace($pack->stripComment($pack->stripPhpIdentify($pack->readContentFromFile(__FILE__))));
