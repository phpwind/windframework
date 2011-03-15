<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.utility.WindFile');
/**
 * 程序打包工具
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindPack {

	/**
	 * @var string 使用正则打包
	 */
	const STRIP_SELF = 'stripWhiteSpaceBySelf';

	/**
	 * @var string 利用php自身的函数打包
	 */
	const STRIP_PHP = 'stripWhiteSpaceByPhp';

	/**
	 * @var string 通过token方式打包
	 */
	const STRIP_TOKEN = 'stripWhiteSpaceByToken';

	private $packList = array();

	private $contentInjectionPosition;

	private $contentInjectionCallBack = '';

	/**
	 * 将指定文件类型且指定文件夹下的所指定文件打包成一个易阅读的文件,
	 * @param mixed $dir 要打包的目录
	 * @param string $dst 文件名
	 * @param string $packMethod 打包方式
	 * @param boolean $compress 是否压缩
	 * @param string $absolutePath 文件路径
	 * @param array $ndir 不须要打包的目录
	 * @param array $suffix 不永许打包的文件类型
	 * @return string
	 */
	public function packFromDir($dir, $dst, $packMethod = WindPack::STRIP_PHP, $compress = true, $absolutePath = '', $ndir = array('.','..','.svn'), $suffix = array(), $nfile = array()) {
		if (empty($dst) || empty($dir)) {
			return false;
		}
		$suffix = is_array($suffix) ? $suffix : array($suffix);
		if (!($content = $this->readContentFromDir($packMethod, $dir, $absolutePath, $ndir, $suffix, $nfile))) {
			return false;
		}
		$fileSuffix = WindFile::getFileSuffix($dst);
		$replace = $compress ? ' ' : "\n";
		$content = implode($replace, $content);
		$content = $this->callBack($content, $replace);
		$content = $this->stripNR($content, $replace);
		$content = $this->stripPhpIdentify($content, '');
		$content = $this->stripImport($content, '');
		$content = $this->getContentBySuffix($content, $fileSuffix, $replace);
		WindFile::write($dst, $content);
		return true;
	}

	/**
	 * @param mixed $fileList
	 * @param string $dst
	 * @param method $packMethod
	 * @param boolean $compress
	 * @param string $absolutePath
	 * @return string|string
	 */
	public function packFromFileList($fileList, $dst, $packMethod = WindPack::STRIP_PHP, $compress = true, $absolutePath = '') {
		if (empty($dst) || empty($fileList)) {
			return false;
		}
		$content = array();
		$this->readContentFromFileList($fileList, $packMethod, $absolutePath, $content);
		$fileSuffix = WindFile::getFileSuffix($dst);
		$replace = $compress ? ' ' : "\n";
		$content = implode($replace, $content);
		$content = $this->callBack($content, $replace);
		$content = $this->stripNR($content, $replace);
		$content = $this->stripPhpIdentify($content, '');
		//$content = $this->stripImport($content, '');
		$content = $this->getContentBySuffix($content, $fileSuffix, $replace);
		WindFile::write($dst, $content);
		return true;
	}

	/**
	 * 通过php自身方式去除指定文件的注释及空白
	 * @param string $filename 文件名
	 */
	public function stripWhiteSpaceByPhp($filename) {
		return php_strip_whitespace($filename);
	}

	/**
	 * 通过正则方式去除指定文件的注释及空白
	 * @param string $filename
	 * @param boolean $compress
	 * @return string
	 */
	public function stripWhiteSpaceBySelf($filename, $compress = true) {
		$content = $this->getContentFromFile($filename);
		$content = $this->stripComment($content, '');
		return $this->stripSpace($content, ' ');
	}

	/**
	 * 通过token方式去除指定文件的注释及空白
	 * @param string $filename
	 * @return string
	 */
	public function stripWhiteSpaceByToken($filename) {
		$content = $this->getContentFromFile($filename);
		$compressContent = '';
		$lastToken = 0;
		foreach (token_get_all($content) as $key => $token) {
			if (is_array($token)) {
				if (in_array($token[0], array(T_COMMENT, T_WHITESPACE, T_DOC_COMMENT))) {
					continue;
				}
				$compressContent .= ' ' . $token[1];
			} else {
				$compressContent .= $token;
			}
			$lastToken = $token[0];
		}
		return $compressContent;
	}

	/**
	 * 从各个目录中取得对应的每个文件的内容 
	 * @param string $packMethod 打包方式
	 * @param mixed $dir 目录名
	 * @param string $absolutePath 绝对路径名
	 * @param array $ndir 不须要打包的文件夹
	 * @param array $suffix 不须要打包的文件类型
	 * @param array $nfile 不须要打包的文件
	 * @return array
	 */
	public function readContentFromDir($packMethod = WindPack::STRIP_PHP, $dir = array(), $absolutePath = '', $ndir = array('.','..','.svn'), $suffix = array(), $nfile = array()) {
		static $content = array();
		if (empty($dir) || false === $this->isValidatePackMethod($packMethod)) {
			return false;
		}
		$dir = is_array($dir) ? $dir : array($dir);
		foreach ($dir as $_dir) {
			$_dir = is_dir($absolutePath) ? WindFile::appendSlashesToDir($absolutePath) . $_dir : $_dir;
			if (is_dir($_dir)) {
				$handle = dir($_dir);
				while (false != ($tmp = $handle->read())) {
					$name = WindFile::appendSlashesToDir($_dir) . $tmp;
					if (is_dir($name) && !in_array($tmp, $ndir)) {
						$this->readContentFromDir($packMethod, $name, $absolutePath, $ndir, $suffix, $nfile);
					}
					if (is_file($name) && !in_array(WindFile::getFileSuffix($name), $suffix) && !in_array($file = basename($name), $nfile)) {
						$content[] = $this->$packMethod($name);
						$this->setPackList($file, $name);
					}
				}
				$handle->close();
			}
		}
		return $content;
	}

	/**
	 * 从文件列表中取得对应的每个文件的内容 
	 * @param mixed $fileList
	 * @param method $packMethod
	 * @param string $absolutePath
	 * @return array:
	 */
	public function readContentFromFileList($fileList, $packMethod = WindPack::STRIP_PHP, $absolutePath = '', &$content = array()) {
		if (empty($fileList) || false === $this->isValidatePackMethod($packMethod)) {
			return array();
		}
		$fileList = is_array($fileList) ? $fileList : array($fileList);
		foreach ($fileList as $key => $value) {
			if (is_array($value) && isset($value[1])) {
				$parents = class_parents($value[1]);
				$_fileList = $this->buildFileList($parents, $fileList);
				$this->readContentFromFileList($_fileList, $packMethod, $absolutePath, $content);
				$implements = class_implements($value[1]);
				$_fileList = $this->buildFileList($implements, $fileList);
				$this->readContentFromFileList($_fileList, $packMethod, $absolutePath, $content);
				if (key_exists($key, $this->getPackList())) continue;
				$file = is_dir($absolutePath) ? WindFile::appendSlashesToDir($absolutePath) . $key : $key;
				if (is_file($file)) {
					$content[] = $this->$packMethod($file);
					$this->setPackList($key, $value);
				}
			}
		}
	}

	/**
	 * 去除注释
	 * @param string $content 要去除的内容
	 * @param mixed $replace 要替换的文本
	 * @return string
	 */
	public function stripComment($content, $replace = '') {
		return preg_replace('/(?:\/\*.*\*\/)*|(?:\/\/[^\r\n]*[\r\n])*/Us', $replace, $content);
	}

	/**
	 * 去除换行
	 * @param string $content 要去除的内容
	 * @param mixed $replace 要替换的文本
	 * @return string
	 */
	public function stripNR($content, $replace = array('\n','\r\n','\r')) {
		return preg_replace('/[\n\r]+/', $replace, $content);
	}

	/**
	 * 去除空格符
	 * @param string $content 要去除的内容
	 * @param mixed $replace 要替换的文本
	 * @return string
	 */
	public function stripSpace($content, $replace = ' ') {
		return preg_replace('/[ ]+/', $replace, $content);
	}

	/**
	 * 去除php标识
	 * @param string $content
	 * @param mixed $replace
	 * @return string
	 */
	public function stripPhpIdentify($content, $replace = '') {
		return preg_replace('/(?:<\?(?:php)*)|(\?>)/i', $replace, $content);
	}

	/**
	 * 根据指定规则替换指定内容中相应的内容
	 * @param string $content
	 * @param string $rule
	 * @param $mixed $replace
	 * @return string
	 */
	public function stripStrByRule($content, $rule, $replace = '') {
		return preg_replace("/$rule/", $replace, $content);
	}

	/**
	 * 去除多余的文件导入信息
	 * @param string $content
	 * @param mixed $replace
	 * @return string
	 */
	public function stripImport($content, $replace = '') {
		$str = preg_match_all('/L[\t ]*::[\t ]*import[\t ]*\([\t ]*[\'\"]([^$][\w\.:]+)[\"\'][\t ]*\)[\t ]*/', $content, $matchs);
		if ($matchs[1]) {
			foreach ($matchs[1] as $key => $value) {
				$name = substr($value, strrpos($value, '.') + 1);
				if (preg_match("/(abstract[\t ]*|class|interface)[\t ]+$name/i", $content)) {
					$strip = str_replace(array('(', ')'), array('\(', '\)'), addslashes($matchs[0][$key])) . '[\t ]*;';
					$content = $this->stripStrByRule($content, $strip, $replace);
				}
			}
		}
		return $content;
	}

	/**
	 * 取得被打包的文件列表
	 * @return array:
	 */
	public function getPackList() {
		return $this->packList;
	}

	/**
	 *从文件读取内容
	 * @param string $filename 文件名
	 * @return string
	 */
	public function getContentFromFile($filename) {
		if (is_file($filename)) {
			$content = '';
			$fp = fopen($filename, "r");
			while (!feof($fp)) {
				$line = fgets($fp);
				if (in_array(strlen($line), array(2, 3)) && in_array(ord($line), array(9, 10, 13))) continue;
				$content .= $line;
			}
			fclose($fp);
			return $content;
		}
		return false;
	}

	/**
	 * 根据文件后缀得取对应的mime内容
	 * @param string $content 要打包的内容内容
	 * @param string $suffix 文件后缀类型
	 * @param string $replace
	 * @return string
	 */
	public function getContentBySuffix($content, $suffix, $replace = ' ') {
		switch ($suffix) {
			case 'php':
				$content = '<?php' . $replace . $content . '?>';
				break;
			default:
				$content = '<?php' . $replace . $content . '?>';
				break;
		}
		return $content;
	}

	private function buildFileList($list, $fileList) {
		$_temp = array();
		foreach ($list as $fileName) {
			foreach ($fileList as $key => $value) {
				if ($value[1] == $fileName) {
					$_temp[$key] = $value;
					break;
				}
			}
		}
		return $_temp;
	}

	/**
	 * @param $contentInjectionCallBack the $contentInjectionCallBack to set
	 * @param string $position 调用位置(before|after)
	 * @author Qiong Wu
	 */
	public function setContentInjectionCallBack($contentInjectionCallBack, $position = 'before') {
		if (!in_array($position, array('before', 'after'))) $position = 'before';
		$this->contentInjectionPosition = $position;
		$this->contentInjectionCallBack = $contentInjectionCallBack;
	}

	/**
	 * 回调函数调用
	 * @param string $content
	 * @param string $replace
	 * @return string
	 */
	public function callBack($content, $replace = '') {
		if ($this->contentInjectionCallBack !== '') {
			$_content = call_user_func_array($this->contentInjectionCallBack, array($this->getPackList()));
			if ($this->contentInjectionPosition == 'before') {
				$content = $replace . $_content . $content;
			} elseif ($this->contentInjectionPosition == 'after') {
				$content .= $replace . $_content . $replace;
			}
		}
		return $content;
	}

	private function isValidatePackMethod($packMethod) {
		return method_exists($this, $packMethod) && in_array($packMethod, array(WindPack::STRIP_PHP, 
			WindPack::STRIP_SELF, WindPack::STRIP_TOKEN));
	}

	/**
	 * 添加被打包的文件到列表
	 * @param  string $key
	 * @param  string $value
	 */
	private function setPackList($key, $value) {
		if (isset($this->packList[$key])) {
			if (is_array($this->packList[$key])) {
				array_push($this->packList[$key], $value);
			} else {
				$tmp_name = $this->packList[$key];
				$this->packList[$key] = array($tmp_name, $value);
			}
		} else {
			$this->packList[$key] = $value;
		}
	}

}
