<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-12-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindFile{
/**
	 * 保存文件
	 * @param string $fileName          保存的文件名
	 * @param mixed $data               保存的数据
	 * @param boolean $isBuildReturn    是否组装保存的数据是return $params的格式，如果没有则以变量声明的方式保存
	 * @param string $method            打开文件方式
	 * @param boolean $ifLock           是否对文件加锁
	 */
	public static function saveData($fileName, $data, $isBuildReturn = true, $method = 'rb+', $ifLock = true) {
		L::import("COM:utility.WindString");
		$temp = "<?php\r\n ";
		if (!$isBuildReturn && is_array($data)) {
			foreach ( $data as $key => $value ) {
				if (!preg_match ( '/^\w+$/', $key ))
					continue;
				$temp .= "\$" . $key . " = " . WindString::varExport($value) . ";\r\n";
			}
			$temp .= "\r\n?>";
		} else {
			($isBuildReturn) && $temp .= " return ";
			$temp .= WindString::varExport($data) . ";\r\n?>";
		}
		return self::writeover($fileName, $temp, $method, $ifLock);
	}
	/**
	 * 写文件
	 *
	 * @param string $fileName 文件绝对路径
	 * @param string $data 数据
	 * @param string $method 读写模式
	 * @param bool $ifLock 是否锁文件
	 * @param bool $ifCheckPath 是否检查文件名中的“..”
	 * @param bool $ifChmod 是否将文件属性改为可读写
	 * @return int 返回写入的字节数
	 */
	public static function writeover($fileName, $data, $method = 'rb+', $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
		$tmpname = strtolower($fileName);
		$tmparray = array(':\/\/', "\0");
		$tmparray[] = '..';
		if (str_replace($tmparray, '', $tmpname) != $tmpname) return false;
		touch($fileName);
		if (!$handle = @fopen($fileName, $method)) return false;
		$ifLock && flock($handle, LOCK_EX);
		$writeCheck = fwrite($handle, $data);
		$method == 'rb+' && ftruncate($handle, strlen($data));
		fclose($handle);
		$ifChmod && @chmod($fileName, 0777);
		return $writeCheck;
	}
	
	/**
	 * 按目录删除文件
	 * @param string $path 目录
	 * @param boolean $ifexpiled 是否过期
	 * @return boolean
	 */
	public static function clearByPath($path, $ifexpiled = true) {
		if (false === ($handle = self::openDir($path))) {
			return false;
		}
		while (false !== ($file = self::readDir($handle))) {
			if ('.' === $file[0] || '..' === $file[0]) continue;
			$fullPath = $path . DIRECTORY_SEPARATOR . $file;
			if (self::isDir($fullPath)) {
				$this->clearByPath($fullPath, $ifexpiled);
			} else if (($ifexpiled && ($mtime =  self::getFileModifyTime($fullPath)) && $mtime < time()) || !$ifexpiled) {
				self::deleteFile($fullPath);
			}
		}
		self::closeDir($handle);
		false === $ifexpiled && self::deleteDir($path);
		return true;
	}
	
	/**
	 * 读取文件内容
	 * @param string $filename
	 * @return string
	 */
	public static function readover($filename){
		return file_get_contents($filename);
	}
	
	/**
	 * 获取文件修改时间
	 * @param int $filename
	 * @return number
	 */
	public static function getFileModifyTime($filename){
		return filemtime($filename);
	}
	
	/**
	 * 设置文件修改时间
	 * @param string $filename
	 * @param int $mtime
	 * @return boolean
	 */
	public static function setFileModifyTime($filename,$mtime){
		return touch($filename,$mtime);
	}
	
	/**
	 * 设置文件权限
	 * @param string $filename
	 * @param int $right
	 * @return boolean
	 */
	public static function setFileRight($filename,$right = 0777){
		return chmod($filename,$right);
	}
	
	/**
	 * 删除文件
	 * @param string $filename
	 * @return boolean
	 */
	public static function deleteFile($filename){
		return unlink($filename);
	}
	
	/**
	 * 判断是否是一个文件
	 * @param string $filename
	 * @return boolean
	 */
	public static function isFile($filename){
		return is_file($filename);
	}
	
	/**
	 * 判断是否是一个目录
	 * @param string $dir
	 * @return boolean
	 */
	public static function isDir($dir){
		return is_dir($dir);
	}
	
	/**
	 * 创建一个目录
	 * @param string $dir 目录名称
	 * @param int $mode 目录权限
	 * @param boolean $recursive 是否递归创建
	 * @param resource $context 目录的上下文信息
	 * @return boolean
	 */
	public static function createDir($dir,$mode = null,$recursive = null,$context =null ){
		return mkdir($dir,$mode,$recursive,$context);
	}
	
	/**
	 * 删除一个目录
	 * @param string $dir 
	 * @return boolean
	 */
	public static function deleteDir($dir){
		return rmdir($dir);
	}
	
	/**
	 * 打开一个目录句柄
	 * @param string $dir 目录名称
	 * @param resource $context 目录的上下文信息
	 * @return resource 返回一个目录句柄
	 */
	public static function openDir($dir,$context = null){
		return is_resource($context) ? opendir($dir,$context) : opendir($dir);
	}
	
	/**
	 * 从目录中读取文件和文件夹
	 * @param resource $dirHandler
	 * @return string
	 */
	public static function readdir($dirHandler){
		return readdir($dirHandler);
	}
	
	/**
	 * 关闭当前目录
	 * @param resource $dirHandler
	 */
	public static function closeDir($dirHandler){
		return closedir($dirHandler);
	}
	
	/**
	 * 重新指向一个目录
	 * @param resource $dirHandler
	 * @return boolean
	 */
	public static function rewindDir($dirHandler){
		return rewind($dirHandler);
	}
	
	/**
	 * 判断一个文件是否可写
	 * @param string $filename
	 * @return boolean
	 */
	public static function isWrite($filename){
		return is_writable($filename);
	}
	
	/**
	 * 判断一个文件是否可读
	 * @param string $filename
	 * @return boolean
	 */
	public static function isRead($filename){
		return is_readable($filename);
	}
	
	/**
	 * 判断一个文件是否可执行
	 * @param string $filename
	 * @return boolean
	 */
	public static function isExecutable($filename){
		return is_executable($filename);
	}
	
	/**
	 * 判断文件是否存在
	 * @param string $file
	 * @return boolean
	 */
	public static function fileExists($file){
		return file_exists($file);
	}
	
	
}