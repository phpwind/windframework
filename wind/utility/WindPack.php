<?php
Wind::import('WIND:utility.WindFile');
/**
 * 程序打包工具
 *
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package utility
 */
class WindPack {

	/**
	 * 使用正则打包
	 * 
	 * @var string 
	 */
	const STRIP_SELF = 'stripWhiteSpaceBySelf';

	/**
	 * 利用php自身的函数打包
	 * 
	 * @var string 
	 */
	const STRIP_PHP = 'stripWhiteSpaceByPhp';

	/**
	 * 通过token方式打包
	 * 
	 * @var string 
	 */
	const STRIP_TOKEN = 'stripWhiteSpaceByToken';

	private $packList = array();

	private $contentInjectionPosition;

	private $contentInjectionCallBack = '';

	/**
	 * 将指定文件类型且指定文件夹下的所指定文件打包成一个易阅读的文件,
	 * 
	 * @param mixed $dir 要打包的目录
	 * @param string $dst 文件名
	 * @param string $packMethod 打包方式,默认为stripWhiteSpaceByPhp
	 * @param boolean $compress 是否压缩，默认为true
	 * @param string $absolutePath 文件路径,默认为空
	 * @param array $ndir 不须要打包的目录，默认包括'.', '..', '.svn'三个
	 * @param array $suffix 不允许打包的文件类型，默认为空数组
	 * @param array $nfile 不允许打包的文件，默认为空数组
	 * @return boolean
	 */
	public function packFromDir($dir, $dst, $packMethod = WindPack::STRIP_PHP, $compress = true, $absolutePath = '', $ndir = array('.','..','.svn'), $suffix = array(), $nfile = array()) {
		if (empty($dst) || empty($dir)) return false;
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
	 * 将给出的文件列表进行打包
	 * 
	 * @param mixed $fileList 文件列表
	 * @param string $dst  打包文件的存放位置
	 * @param method $packMethod 打包的方式，默认为stripWhiteSpaceByPhp
	 * @param boolean $compress 打包是否采用压缩的方式，默认为true
	 * @param string $absolutePath 文件路径,默认为空
	 * @return boolean
	 */
	public function packFromFileList($fileList, $dst, $packMethod = WindPack::STRIP_PHP, $compress = true, $absolutePath = '') {
		if (empty($dst) || empty($fileList)) return false;
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
	 * 
	 * @param string $filename 文件名
	 * @return string
	 */
	public function stripWhiteSpaceByPhp($filename) {
		return php_strip_whitespace($filename);
	}

	/**
	 * 通过正则方式去除指定文件的注释及空白
	 * 
	 * @param string $filename 文件名字
	 * @param boolean $compress 是否采用压缩，默认为true
	 * @return string
	 */
	public function stripWhiteSpaceBySelf($filename, $compress = true) {
		$content = $this->getContentFromFile($filename);
		$content = $this->stripComment($content, '');
		return $this->stripSpace($content, ' ');
	}

	/**
	 * 通过token方式去除指定文件的注释及空白
	 * 
	 * @param string $filename 文件名称
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
	 * 
	 * @param string $packMethod 打包方式，默认为stripWhiteSpaceByPhp
	 * @param mixed $dir 目录名，默认为空数组
	 * @param string $absolutePath 绝对路径名，默认为空
	 * @param array $ndir 不须要打包的目录，默认包括'.', '..', '.svn'三个
	 * @param array $suffix 不允许打包的文件类型，默认为空数组
	 * @param array $nfile 不允许打包的文件，默认为空数组
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
	 * 
	 * @param mixed $fileList 文件列表
	 * @param method $packMethod  打包方式，默认为stripWhiteSpaceByPhp
	 * @param string $absolutePath  绝对路径名，默认为空
	 * @param array $content 保存文件内容，默认为空数组
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
	 * 
	 * @param string $content 要去除的内容
	 * @param mixed $replace 要替换的文本
	 * @return string
	 */
	public function stripComment($content, $replace = '') {
		return preg_replace('/(?:\/\*.*\*\/)*|(?:\/\/[^\r\n]*[\r\n])*/Us', $replace, $content);
	}

	/**
	 * 去除换行
	 * 
	 * @param string $content 要去除的内容
	 * @param mixed $replace 要替换的文本
	 * @return string
	 */
	public function stripNR($content, $replace = array('\n','\r\n','\r')) {
		return preg_replace('/[\n\r]+/', $replace, $content);
	}

	/**
	 * 去除空格符
	 * 
	 * @param string $content 要去除的内容
	 * @param mixed $replace 要替换的文本,默认为空 
	 * @return string
	 */
	public function stripSpace($content, $replace = ' ') {
		return preg_replace('/[ ]+/', $replace, $content);
	}

	/**
	 * 去除php标识
	 * 
	 * @param string $content 需要处理的内容
	 * @param mixed $replace 将php标识替换为该值，默认为空
	 * @return string
	 */
	public function stripPhpIdentify($content, $replace = '') {
		return preg_replace('/(?:<\?(?:php)*)|(\?>)/i', $replace, $content);
	}

	/**
	 * 根据指定规则替换指定内容中相应的内容
	 * 
	 * @param string $content 需要处理的内容
	 * @param string $rule    需要匹配的正则
	 * @param $mixed $replace 用来替换将匹配出来的结果，默认为空
	 * @return string
	 */
	public function stripStrByRule($content, $rule, $replace = '') {
		return preg_replace("/$rule/", $replace, $content);
	}

	/**
	 * 去除多余的文件导入信息
	 * 
	 * @param string $content 需要处理的内容
	 * @param mixed $replace 用来替换将匹配出来的结果，默认为空
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
	 * 
	 * @return array
	 */
	public function getPackList() {
		return $this->packList;
	}

	/**
	 * 从文件读取内容
	 *
	 * @param string $filename 文件名
	 * @return string 如果给出的文件不是一个有效文件则返回false
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
	 * 
	 * @param string $content 要打包的内容内容
	 * @param string $suffix 文件后缀类型
	 * @param string $replace 替换的字串，默认为空
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

	/**
	 * 构造文件列表
	 *
	 * @param array $list     需要处理的文件列表
	 * @param array $fileList 文件列表
	 * @return array 保存$list中存在于$fileList中的文件列表
	 */
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
	 * 设置回调
	 * 
	 * @author Qiong Wu
	 * @param array $contentInjectionCallBack 回调函数
	 * @param string $position 调用位置(before|after)默认为before
	 * @return void
	 */
	public function setContentInjectionCallBack($contentInjectionCallBack, $position = 'before') {
		if (!in_array($position, array('before', 'after'))) $position = 'before';
		$this->contentInjectionPosition = $position;
		$this->contentInjectionCallBack = $contentInjectionCallBack;
	}

	/**
	 * 回调函数调用
	 * 
	 * @param string $content 被回调的内容
	 * @param string $replace 替换内容，默认为空
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

	/**
	 * 检查打包方法的有效性
	 *
	 * @param string $packMethod 被检查的方法
	 * @return boolean
	 */
	private function isValidatePackMethod($packMethod) {
		return method_exists($this, $packMethod) && in_array($packMethod, array(WindPack::STRIP_PHP, 
			WindPack::STRIP_SELF, WindPack::STRIP_TOKEN));
	}

	/**
	 * 添加被打包的文件到列表
	 * 
	 * @param  string $key   保存的key值
	 * @param  string $value 需要被保存的值
	 * @return void
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