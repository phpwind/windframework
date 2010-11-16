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
class WLayout {
	/* layout 逻辑名称 */
	private $layout = '';
	
	/* 模板文件路径信息 */
	private $tpl = '';
	
	private $dirName = '';
	private $ext = '';
	private $tplName = '';
	
	/**
	 * 布局信息
	 * array('fileName' => 'filePath')
	 * @var $_segments
	 */
	private $segments = array();
	
	/**
	 * 设置layout布局文件
	 * 
	 * @param string $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}
	
	/**
	 * 设置模板的路径信息
	 * @param string $tpl
	 */
	public function setTpl($tpl) {
		$pathInfo = @pathinfo($tpl);
		$this->dirName = $pathInfo['dirname'];
		$this->ext = $pathInfo['extension'];
		$this->tplName = substr($pathInfo['basename'], 0, strrpos($pathInfo['basename'], '.'));
		$this->tpl = $tpl;
	}
	
	/**
	 * 通过解析配置文件，获得布局信息
	 * @param string $config
	 */
	public function parser() {
		if ($this->layout)
			$this->_parserLayoutFile();
	}
	
	/**
	 * 设置页面片段
	 * 
	 * @param array|string $segment
	 */
	public function setSegments($segment) {
		if (is_array($segment))
			$this->segments += $segment;
		else
			$this->segments[] = $segment;
	}
	
	public function getSegments() {
		foreach ($this->segments as $key => $value) {
			$file = $this->dirName . W::getSeparator() . $value . '.' . $this->ext;
			if (file_exists($file))
				$this->segments[$value] = $file;
		}
		return $this->segments;
	}
	
	/**
	 * 解析layout布局文件
	 */
	private function _parserLayoutFile() {
		$file = $this->dirName . W::getSeparator() . $this->layout . '.' . $this->ext;
		if (file_exists($file))
			include $file;
	}
}