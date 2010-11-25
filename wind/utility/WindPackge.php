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
class WindPack{
	
	private $packList = array();
	/**
	 * 去除指定文件的注释及空白
	 * @param string $filename 文件名
	 */
	public function  stripWhiteSpace($filename){
		return php_strip_whitespace($filename);
	}
	/**
	 * 去除注释
	 * @param string $content 要去除的内容
	 * @param string $replace 要替换的文本
	 * @return string
	 */
	public function stripComment($content,$replace = ''){
		return preg_replace('/(?:\/\*.*\*\/)*|(?:\/\/[^\r\n]*[\r\n])*/Us',$replace,$content);
	}
	
	/**
	 * 去除换行
	 * @param string $content 要去除的内容
	 * @param string $replace 要替换的文本
	 * @return string
	 */
	public function stripNR($content,$replace = "\n"){
		return preg_replace("/[\n\r]+/",$replace,$content);
	}
	
	/**
	 * 去除空格符
	 * @param string $content 要去除的内容
	 * @param string $replace 要替换的文本
	 * @return string
	 */
	public function stripSpace($content,$replace = ' '){
		return preg_replace("/[ ]+/",$replace,$content);
	}

	/**
	 * 去除php标识
	 * @param string $content
	 * @param string $replace
	 * @return string
	 */
	public function stripPhpIdentify($content,$replace = ''){
		return preg_replace("/(?:<\?(?:php)*)|(\?>)/i",$replace,$content);
	}
	
	public function getPackList(){
		return $this->packList;
	}
	
	public function formatPackList($comment = false,$pack = array(),$samekey = ''){
		$list = array();
		$sep = $comment ? "\r\n*" : "\r\n";
		$format = '';
		$pack = $pack && is_array($pack) ? $pack : $this->getPackList();
		foreach($pack as $key=>$value){
			if(is_array($value)){
				$format .= $this->formatPackList($comment,$value,$key);
			}else{
				$key = $samekey ? $samekey : $key;
				$format .= $key.'=>'.$value.$sep;
			}
		}
		return $format;
		
	}
	/**
	 *从文件读取内容
	 * @param string $filename 文件名
	 * @return string
	 */
	public function readContentFromFile($filename){
		if($this->isFile($filename)){
			$fp = fopen($filename, "r");
			while(!feof($fp)){
				$line = fgets($fp);
				if(in_array(strlen($line),array(2,3)) && in_array(ord($line),array(9,10,13)) )
					continue;
				$content .= $line;
			}
			fclose($fp);
			return $content;
		}
		return false;
	}
	
	/**
	 * 将内容打包的文件
	 * @param string $filename 文件内容
	 * @param string $content  要打包的指定文件的内容
	 * @return string
	 */
	public function writeContentToFile($filename,$content){
		$fp = fopen($filename, "w");
		fwrite($fp,$content);
		fclose($fp);
		return true;
	}
	/**
	 * 根据文件后缀得取对应的mime内容
	 * @param string $content 要打包的内容内容
	 * @param string $suffix 文件后缀类型
	 * @return string
	 */
	public function getContentBySuffix($content,$suffix){
		switch($suffix){
			case 'php' : $content = '<?php'.$content.'?>';
			default: ;
		}
		return $content;
	}
	
	public function getCommentBySuffix($content,$suffix,$other= ''){
		switch($suffix){
			case 'php' : $content = "\r\n/**$other\r\n*".$content."\r\n*/\r\n";
			default: ;
		}
		return $content;
	}
	
	public function getPackComment($content,$suffix){
		return $this->getCommentBySuffix($this->formatPackList(true),$suffix,'Your pack list').$content;
	}
	
	/**
	 * 从各个目录中取得对应的每个文件的内容 
	 * @param mixed $dir 目录名
	 * @param array $ndir 不须要打包的文件夹
	 * @param array $suffix 不须要打包的文件类型
	 * @return array
	 */
	public function readContentFromDir($dir,$ndir = array('.','..','.svn'),$suffix = array()){
		static $content = array();
		$dir = is_array($dir) ? $dir : array($dir);
		foreach($dir as $_dir){
			if($this->isDir($_dir)){
				$handle = dir($_dir);
				while(false != ($tmp = $handle->read())){
					$name = $this->realDir($_dir).$tmp;
					if($this->isDir($name) && !in_array($tmp,$ndir)){
						$this->readContentFromDir($name,$ndir,$suffix);
					}
					if($this->isFile($name) && !in_array($this->getFileSuffix($name),$suffix)){
						$content[] = $this->readContentFromFile($name);
						$this->setPackList($this->getFileName($name),$name);
					}
				}
				$handle->close();
			}
		}
		return $content;
	}
	
	public function setPackList($key,$value){
		if(isset($this->packList[$key])){
			if(is_array($this->packList[$key])){
				array_push($this->packList[$key],$value);
			}else{
				$tmp_name = $this->packList[$key];
				$this->packList[$key] = array($tmp_name,$value);
				
			}
		}else{
			$this->packList[$key] = $value;
		}
	}
	
	/**
	 * 取得真实的目录
	 * @param string $path 路径名
	 * @return string
	 */
	public function realDir($path){
		if(($pos = strrpos($path,DIRECTORY_SEPARATOR)) === strlen($path) - 1){
			return $path;
		}
		return $path.DIRECTORY_SEPARATOR;
	}
	
	/**
	 * 判断是否是一个文件
	 * @param string $filename 文件名
	 * @return boolean
	 */
	public function isFile($filename){
		return is_file($filename);
	}
	
	/**
	 * 判断是否是一个目录
	 * @param string $dir 目录名
	 * @return boolean
	 */
	public function isDir($dir){
		return is_dir($dir);
	}
	
	/**
	 * 将指定目录下的所有文件内容打包的一个文件
	 * @param mixed $dir 要打包的目录
	 * @param sgring $dst 文件名
	 * @param array $ndir 不须要打包的目录
	 * @param array $suffix 不永许打包的文件类型
	 * @return string
	 */
	public function pack($dir,$dst,$ndir = array('.','..','.svn'),$suffix = array()){
		if(empty($dst)){
			return false;
		}
		$suffix = is_array($suffix) ? $suffix : array($suffix);
		if(!($content = $this->readContentFromDir($dir,$ndir,$suffix))){
			return false;
		}
		$fileSuffix = $this->getFileSuffix($dst);
		$content = implode("\n\r",$content);
		$content = $this->stripComment($content);
		$content = $this->stripPhpIdentify($content);
		$content = $this->stripNR($content);
		$content = $this->stripSpace($content);
		$content = $this->getPackComment($content,$fileSuffix);
		$content = $this->getContentBySuffix($content,$fileSuffix);
		$this->writeContentToFile($dst,$content);
		//echo $this->formatPackList();
		return true;
		
	}
	
	public function getFileSuffix($filename){
		return substr($filename,strrpos($filename,'.')+1);
	}
	
	public function getFileName($path,$ifsuffix = false){
		$filename = substr($path, strrpos($path,DIRECTORY_SEPARATOR)+1);
		return  $ifsuffix ? $filename : substr($filename,0,strrpos($filename,'.'));
	}
	
}
