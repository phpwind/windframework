<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
/**
 * page标签解析
 * 
 * 职责：编译模板page标签
 * 支持参数类型：<code>
 * <page tpl='' current='' total='' per='' url='read.php?tid=$tid&page=' />
 * </code>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package viewer
 * @subpackage compiler
 */
class WindTemplateCompilerPage extends AbstractWindTemplateCompiler {
	protected $tpl = '';
	protected $url = '';
	protected $total = '0';
	protected $page = '1';
	protected $count = '0';
	protected $per = '0';

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		return $this->getPageCode();
	
	}

	private function getPageCode() {
		empty($this->total) && $this->total = '0';
		empty($this->page) && $this->page = '1';
		empty($this->count) && $this->count = '0';
		empty($this->per) && $this->per = '0';
		empty($this->url) && $this->url = '';
		$strPageCode = '<?php ' . '$_tplPageCount=(int)' . $this->count . ';' . '$_tplPagePer=(int)' . $this->per . ';' . '$_tplPageTotal=(int)' . $this->total . ';' . '$_tplPageCurrent=(int)' . $this->page . ';' . '$_tplPageUrl=(string)"' . addslashes($this->url) . '";' . 'if ($_tplPageCount > 0 && $_tplPagePer > 0) {' . '$_tplTmpPageTotal = ceil($_tplPageCount / $_tplPagePer);' . 'if ($_tplPageTotal > $_tplTmpPageTotal) $_tplPageTotal = $_tplTmpPageTotal;' . '}' . '$_tplPageCurrent > $_tplPageTotal && $_tplPageCurrent = $_tplPageTotal;' . 'if ($_tplPageTotal > 1) {' . "?>\r\n" . $this->getTplContent() . "\r\n<?php } ?>";
		return $strPageCode;
	}

	private function getTplContent() {
		if ($this->tpl) {
			list($compileFile, $content) = $this->windViewerResolver->compile($this->tpl, '', true);
		} else {
			$content = $this->getDefaultHtml();
		}
		return $this->parsePageTags($content);
	}

	private function parsePageTags($content) {
		$arrPageTags = array('$total', '$page', '$url', '$count');
		$arrPageVars = array('$_tplPageTotal', '$_tplPageCurrent', '$_tplPageUrl', '$_tplPageCount');
		return str_ireplace($arrPageTags, $arrPageVars, $content);
	}

	private function getDefaultHtml() {
		return '';
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('tpl', 'total', 'page', 'per', 'count', 'url');
	}
}

?>