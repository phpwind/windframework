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

	private $tplName = '';

	/**
	 * @param string $layoutFile
	 */
	public function __construct($layoutFile = '') {
		$this->setLayoutFile($layoutFile);
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
	 * 解析layout布局文件
	 */
	public function parserLayout($dirName = '', $ext = '', $content = '') {
		if ($this->layoutFile) {
			$this->tplName = $content;
			$dirName && $dirName = $dirName . '.';
			$ext === '' && $ext = 'htm';
			$file = L::getRealPath($dirName . $this->layoutFile);
			$file = $file . '.' . $ext;
			if (!@include $file) throw new WindException('the layout file ' . $file . ' is not exists.');
		}
		return $this->segments;
	}

	/**
	 * 设置切片文件
	 * 
	 * @param string $segment
	 */
	private function setSegments($segment) {
		if ($segment) $this->segments[] = $segment;
	}

}