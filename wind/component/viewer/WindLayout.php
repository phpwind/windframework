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
	private $layout = '';
	
	/**
	 * 设置layout布局文件
	 * 可以为一个布局文件的逻辑名称，如：layout.mainLayout
	 * 则程序会在模板路径下面寻找layout目录下的mainLayout布局文件，后缀名和模板的后缀名保持一致
	 * 
	 * @param string $layout
	 */
	public function setLayoutFile($layout) {
		$this->layout = $layout;
	}
	
	/**
	 * 设置模板文件包含文件
	 * 可以为一个布局文件的逻辑名称，如：segments.header
	 * 则程序会在模板路径下面寻找segments目录下的header布局文件，后缀名和模板的后缀名保持一致
	 * 
	 * @param string $fileName
	 */
	private function includeFile($fileName) {
		$this->setSegments($fileName);
	}
	
	private function setSegments($segment) {
		$this->segments[] = $segment;
	}
	
	private function setContent($key = 'current') {
		$this->setSegments('key_' . $key);
	}
	
	/**
	 * 解析layout布局文件
	 */
	public function parserLayout($dirName = '', $ext = '') {
		if ($this->layout) {
			$file = L::getRealPath($dirName . '.' . $this->layout, false, $ext);
			if (!$file) throw new WindException('cant find layout file.');
			@include $file;
		}
		return $this->segments;
	}

}