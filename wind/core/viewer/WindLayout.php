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

	private $layoutFile = '';

	/**
	 * @param string $layoutFile
	 */
	public function __construct($layoutFile = '') {
		$this->setLayoutFile($layoutFile);
	}

	/**
	 * 解析layout布局文件
	 * @param WindViewerResolver $windViewerResolver
	 */
	public function parse($windViewerResolver) {
		if (!$windViewerResolver) return '';
		$this->viewer = $windViewerResolver;
		if (!$this->layoutFile) throw new WindException('layout file is required.');
		$layoutFile = $windViewerResolver->compile($this->layoutFile);
		ob_start();
		if (!include $layoutFile) throw new WindException('Incorrect layout file ' . $layoutFile);
		return ob_get_clean();
	}

	private function setSegments($segment = '') {
		$this->getContent($segment);
	}

	private function getContent($template = '') {
		if (!isset($this->viewer)) return;
		if (!$template) $template = $this->viewer->getWindView()->getTemplateName();
		if ($template) $this->viewer->displayWindFetch($template);
	}

	/**
	 * @return the $layoutFile
	 */
	protected function getLayoutFile() {
		return $this->layoutFile;
	}

	/**
	 * @param field_type $layoutFile
	 */
	protected function setLayoutFile($layoutFile) {
		$this->layoutFile = $layoutFile;
	}

}