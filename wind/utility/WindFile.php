<?php
Wind::import("WIND:utility.WindString");
/**
 * 文件工具类
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package utility
 */
class WindFile {
	
	/**
	 * 以读的方式打开文件，具有较强的平台移植性
	 * 
	 * @var string 
	 */
	const READ = 'rb';
	
	/**
	 * 以读写的方式打开文件，具有较强的平台移植性
	 * 
	 * @var string 
	 */
	const READWRITE = 'rb+';
	
	/**
	 * 以写的方式打开文件，具有较强的平台移植性
	 * 
	 * @var string 
	 */
	const WRITE = 'wb';
	
	/**
	 * 以读写的方式打开文件，具有较强的平台移植性
	 * 
	 * @var string 
	 */
	const WRITEREAD = 'wb+';
	
	/**
	 * 以追加写入方式打开文件，具有较强的平台移植性
	 * 
	 * @var string 
	 */
	const APPEND_WRITE = 'ab';
	
	/**
	 * 以追加读写入方式打开文件，具有较强的平台移植性
	 * 
	 * @var string 
	 */
	const APPEND_WRITEREAD = 'ab+';

	/**
	 * 保存文件
	 * 
	 * @param string $fileName          保存的文件名
	 * @param mixed $data               保存的数据
	 * @param boolean $isBuildReturn    是否组装保存的数据是return $params的格式，如果没有则以变量声明的方式保存,默认为true则以return的方式保存
	 * @param string $method            打开文件方式，默认为rb+的形式
	 * @param boolean $ifLock           是否对文件加锁，默认为true即加锁
	 */
	public static function savePhpData($fileName, $data, $isBuildReturn = true, $method = self::READWRITE, $ifLock = true) {
		$temp = "<?php\r\n ";
		if (!$isBuildReturn && is_array($data)) {
			foreach ($data as $key => $value) {
				if (!preg_match('/^\w+$/', $key)) continue;
				$temp .= "\$" . $key . " = " . WindString::varToString($value) . ";\r\n";
			}
			$temp .= "\r\n?>";
		} else {
			($isBuildReturn) && $temp .= " return ";
			$temp .= WindString::varToString($data) . ";\r\n?>";
		}
		return self::write($fileName, $temp, $method, $ifLock);
	}

	/**
	 * 写文件
	 *
	 * @param string $fileName 文件绝对路径
	 * @param string $data 数据
	 * @param string $method 读写模式,默认模式为rb+
	 * @param bool $ifLock 是否锁文件，默认为true即加锁
	 * @param bool $ifCheckPath 是否检查文件名中的“..”，默认为true即检查
	 * @param bool $ifChmod 是否将文件属性改为可读写,默认为true
	 * @return int 返回写入的字节数
	 */
	public static function write($fileName, $data, $method = self::READWRITE, $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
		touch($fileName);
		if (!$handle = fopen($fileName, $method)) return false;
		$ifLock && flock($handle, LOCK_EX);
		$writeCheck = fwrite($handle, $data);
		$method == self::READWRITE && ftruncate($handle, strlen($data));
		fclose($handle);
		$ifChmod && chmod($fileName, 0777);
		return $writeCheck;
	}

	/**
	 * 读取文件
	 *
	 * @param string $fileName 文件绝对路径
	 * @param string $method 读取模式默认模式为rb
	 * @return string 从文件中读取的数据
	 */
	public static function read($fileName, $method = self::READ) {
		$data = '';
		$len = filesize($fileName);
		if (false !== ($handle = fopen($fileName, $method)) && 0 < $len) {
			flock($handle, LOCK_SH);
			$data = fread($handle, $len);
			fclose($handle);
		}
		return $data;
	}

	/**
	 * 按目录删除文件
	 * 
	 * @param string  $dir 目录
	 * @param boolean $ifexpiled 是否过期 默认为false
	 * @deprecated
	 * @return boolean
	 */
	public static function clearDir($dir, $ifexpiled = false) {
		//TODO 删除掉是否过期相关处理，不要将外部业务需求，耦合进工具库方法
		if (!$handle = @opendir($dir)) return false;
		while (false !== ($file = readdir($handle))) {
			if ('.' === $file[0] || '..' === $file[0]) continue;
			$fullPath = $dir . DIRECTORY_SEPARATOR . $file;
			if (is_dir($fullPath)) {
				self::clearDir($fullPath, $ifexpiled);
			} else if (($ifexpiled && ($mtime = filemtime($fullPath)) && $mtime < time()) || !$ifexpiled) {
				self::delFile($fullPath);
			}
		}
		closedir($handle);
		false === $ifexpiled && rmdir($dir);
		return true;
	}

	/**
	 * 批量删除指定目录下的文件
	 * 
	 * @param string $path 目录文件路径
	 * @param boolean $delDir 是否同样删除目录,默认为false不删除
	 * @param int $level 文件的级别，默认为0
	 * @return boolean
	 */
	public static function delFiles($path, $delDir = false, $level = 0) {
		$path = rtrim($path, DIRECTORY_SEPARATOR);
		if (!$handler = opendir($path)) return false;
		while (false !== ($filename = readdir($handler))) {
			if ("." != $filename && ".." != $filename) {
				if (is_dir($path . DIRECTORY_SEPARATOR . $filename)) {
					if (substr($filename, 0, 1) != '.') {
						self::delFiles($path . DIRECTORY_SEPARATOR . $filename, $delDir, $level + 1);
					}
				} else {
					self::delFile($path . DIRECTORY_SEPARATOR . $filename);
				}
			}
		}
		closedir($handler);
		true == $delDir && $level > 0 && rmdir($path);
		return true;
	}

	/**
	 * 取得目录的迭代
	 * 
	 * @param string $dir 目录名
	 * @return DirectoryIterator
	 */
	public static function getDirectoryIterator($dir) {
		return new DirectoryIterator($dir);
	}

	/**
	 * 取得文件信息
	 * 
	 * @param string $fileName 文件名字
	 * @return array 文件信息
	 */
	public static function getFileInfo($fileName) {
		if (false === is_file($fileName)) {
			return array();
		}
		$fileInfo['name'] = substr(strrchr($fileName, DIRECTORY_SEPARATOR), 1);
		$fileInfo['path'] = $fileName;
		$fileInfo['size'] = filesize($fileName);
		$fileInfo['ctime'] = filectime($fileName);
		$fileInfo['atime'] = fileatime($fileName);
		$fileInfo['mtime'] = filemtime($fileName);
		$fileInfo['readable'] = is_readable($fileName);
		$fileInfo['writable'] = is_writable($fileName);
		$fileInfo['executable'] = is_executable($fileName);
		$fileInfo['right'] = fileperms($fileName);
		$fileInfo['group'] = filegroup($fileName);
		$fileInfo['owner'] = fileowner($fileName);
		$fileInfo['mime'] = self::getMimeType($fileName);
		return $fileInfo;
	}

	/**
	 * 取得目录信息
	 * 
	 * @param string $dir 需要获取的目录
	 * @return array
	 */
	public static function getDirectoryInfo($dir) {
		if (false !== is_dir($dir)) {
			return array();
		}
		return stat($dir);
	}

	/**
	 * 删除文件
	 * 
	 * @param string $filename 文件名称
	 * @return boolean
	 */
	public static function delFile($filename) {
		return @unlink($filename);
	}

	/**
	 * 取得文件后缀
	 * 
	 * @param string $filename 文件名称
	 * @return string
	 */
	public static function getFileSuffix($filename) {
		$filename = explode('.', $filename);
		return array_pop($filename);
	}

	/**
	 * 取得真实的目录
	 * 
	 * @param string $path 路径名
	 * @return string
	 */
	public static function appendSlashesToDir($path) {
		return rtrim($path, '\\/') . DIRECTORY_SEPARATOR;
	}

	/**
	 * 创建目录
	 *
	 * @param string $path 目录路径
	 * @param int $permissions 权限
	 * @return boolean
	 */
	public static function mkdir($path, $permissions = 0777) {
		if (!is_dir($path) && dirname($path) !== $path) {
			self::mkdir(dirname($path), $permissions);
			@mkdir($path, $permissions);
		}
		return true;
	}
}