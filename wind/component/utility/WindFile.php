<?php
Wind::import("COM:utility.WindSecurity");
Wind::import("COM:utility.WindString");
/**
 * 文件工具类
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindFile {
	
	/**
	 * @var string 以读的方式打开文件，具有较强的平台移植性
	 */
	const READ = 'rb';
	
	/**
	 * @var string 以读写的方式打开文件，具有较强的平台移植性
	 */
	const READWRITE = 'rb+';
	
	/**
	 * @var string 以写的方式打开文件，具有较强的平台移植性
	 */
	const WRITE = 'wb';
	
	/**
	 * @var string 以读写的方式打开文件，具有较强的平台移植性
	 */
	const WRITEREAD = 'wb+';
	
	/**
	 * @var string 以追加写入方式打开文件，具有较强的平台移植性
	 */
	const APPEND_WRITE = 'ab';
	
	/**
	 * @var string 以追加读写入方式打开文件，具有较强的平台移植性
	 */
	const APPEND_WRITEREAD = 'ab+';

	/**
	 * 保存文件
	 * @param string $fileName          保存的文件名
	 * @param mixed $data               保存的数据
	 * @param boolean $isBuildReturn    是否组装保存的数据是return $params的格式，如果没有则以变量声明的方式保存
	 * @param string $method            打开文件方式
	 * @param boolean $ifLock           是否对文件加锁
	 */
	public static function savePhpData($fileName, $data, $isBuildReturn = true, $method = 'rb+', $ifLock = true) {
		$temp = "<?php\r\n ";
		if (!$isBuildReturn && is_array($data)) {
			foreach ($data as $key => $value) {
				if (!preg_match('/^\w+$/', $key))
					continue;
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
	 * @param string $method 读写模式
	 * @param bool $ifLock 是否锁文件
	 * @param bool $ifCheckPath 是否检查文件名中的“..”
	 * @param bool $ifChmod 是否将文件属性改为可读写
	 * @return int 返回写入的字节数
	 */
	public static function write($fileName, $data, $method = self::READWRITE, $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
		$fileName = WindSecurity::escapePath($fileName);
		touch($fileName);
		if (!$handle = fopen($fileName, $method))
			return false;
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
	 * @param string $method 读取模式
	 * @return string
	 */
	public static function read($fileName, $method = self::READ) {
		$fileName = WindSecurity::escapePath($fileName);
		$data = '';
		if (false !== ($handle = fopen($fileName, $method))) {
			flock($handle, LOCK_SH);
			$data = fread($handle, filesize($fileName));
			fclose($handle);
		}
		return $data;
	}

	/**
	 * 按目录删除文件
	 * @param string  $dir 目录
	 * @param boolean $ifexpiled 是否过期
	 * @deprecated
	 * @return boolean
	 */
	public static function clearDir($dir, $ifexpiled = false) {
		//TODO 删除掉是否过期相关处理，不要将外部业务需求，耦合进工具库方法
		if (!$handle = @opendir($dir))
			return false;
		while (false !== ($file = readdir($handle))) {
			if ('.' === $file[0] || '..' === $file[0])
				continue;
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
	 * 批量删除文件
	 * @param string $path
	 * @param string $delDir
	 * @param int $level
	 * @return string
	 */
	public static function delFiles($path, $delDir = false, $level = 0) {
		$path = rtrim($path, DIRECTORY_SEPARATOR);
		if (!$handler = opendir($path)) {
			return false;
		}
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
	 * 取得文件的mime类型
	 * @param string $fileName 文件名
	 * @return string
	 */
	public static function getMimeType($fileName) {
		//TODO WindMimeTypes.php 被删掉了，有bug 
		$suffix = self::getFileSuffix($fileName);
		$mimes = require WIND_PATH . '/component/utility/WindMimeTypes.php';
		if (isset($mimes[$suffix])) {
			return is_array($mimes[$suffix]) ? current($mimes[$suffix]) : $mimes[$suffix];
		} else {
			throw new WindException('Sorry, can not find the corresponding mime type of the file');
		}
		return false;
	}

	/**
	 * 取得目录的迭代
	 * @param string $dir 目录名
	 * @return DirectoryIterator
	 */
	public static function getDirectoryIterator($dir) {
		return new DirectoryIterator($dir);
	}

	/**
	 * 取得文件信息
	 * @param unknown_type $fileName
	 * @return string|number
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
	 * @param unknown_type $dir
	 * @return string|multitype:
	 */
	public static function getDirectoryInfo($dir) {
		if (false !== is_dir($dir)) {
			return array();
		}
		return stat($dir);
	}

	/**
	 * 删除文件
	 * @param string $filename
	 * @return boolean
	 */
	public static function delFile($filename) {
		return @unlink($filename);
	}

	/**
	 * 取得文件后缀
	 * @param string $filename
	 * @return string
	 */
	public static function getFileSuffix($filename) {
		$filename = explode($filename, '.');
		return $filename[count($filename) - 1];
	}

	/**
	 * 取得真实的目录
	 * @param string $path 路径名
	 * @return string
	 */
	public static function appendSlashesToDir($path) {
		return rtrim($path, '\\/') . DIRECTORY_SEPARATOR;
	}

}