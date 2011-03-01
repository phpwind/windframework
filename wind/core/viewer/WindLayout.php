<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 布局对象，
 * 通过加载一个布局对象，或者布局配置文件，或者设置布局变量来实现页面布局
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindLayout {

	/**
	 * 布局信息
	 * @var $_segments
	 */
	private $segments = array();

	private $layoutFile = '';

	private $dir = '';

	private $suffix = '';

	private $tplName = '';

	/**
	 * @param string $layoutFile
	 */
	public function __construct($layoutFile = '', $dir = '', $suffix = '') {
		$this->setLayoutFile($layoutFile);
		$this->setDir($dir);
		$this->setSuffix($suffix);
	}

	/**
	 * 解析layout布局文件
	 */
	public function parserLayout($tplName) {
		$this->tplName = $tplName;
		if ($this->getLayoutFile() === '') {
			$this->setSegments();
		} else {
			$_filePath = $this->getLayoutFile();
			if (false !== strpos($_filePath, D_S)) {
				include $_filePath;
			} elseif ($this->getDir()) {
				$_filePath = $this->getDir() . '.' . $_filePath;
				$file = L::getRealPath($this->getDir() . '.' . $this->getLayoutFile(), $this->getSuffix());
				if (!include $file) {
					throw new WindException('the layout file ' . $file . ' is not exists.');
				}
			}
		}
		return $this->segments;
	}

	/**
	 * 设置切片文件
	 * 
	 * @param string $segment
	 */
	private function setSegments($segment = '') {
		if ($segment === '')
			$this->segments[] = $this->tplName;
		else
			$this->segments[] = $segment;
	}

	/**
	 * @return the $layoutFile
	 */
	protected function getLayoutFile() {
		return $this->layoutFile;
	}

	/**
	 * 设置layout布局文件
	 * 可以为一个布局文件的逻辑名称，如：layout.mainLayout
	 * 则程序会在模板路径下面寻找layout目录下的mainLayout布局文件，后缀名和模板的后缀名保持一致
	 * 
	 * @param string $layout
	 */
	public function setLayoutFile($layoutFile) {
		$this->layoutFile = $layoutFile;
	}

	/**
	 * @return the $dir
	 */
	public function getDir() {
		return $this->dir;
	}

	/**
	 * @return the $suffix
	 */
	public function getSuffix() {
		return $this->suffix;
	}

	/**
	 * @param field_type $dir
	 */
	public function setDir($dir) {
		$this->dir = $dir;
	}

	/**
	 * @param field_type $suffix
	 */
	public function setSuffix($suffix) {
		$this->suffix = $suffix;
	}

}