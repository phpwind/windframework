<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
/**
 * page标签解析
 * 
 * 职责：编译模板page标签
 * 支持参数类型：<code>
 * 模板名称,当前页,总条数,每页显示多少条,url
 * <page tpl='' current='' count='' per='' url='read.php?tid=$tid&page=' />
 * </code>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package viewer
 * @subpackage compiler
 */
class WindTemplateCompilerPage extends AbstractWindTemplateCompiler {
	/**
	 * 分页模板
	 *
	 * @var string
	 */
	protected $tpl = '';
	/**
	 * 分页跳转url
	 *
	 * @var string
	 */
	protected $url = '';
	/**
	 * 字符型数字,总共有多少页
	 *
	 * @var string
	 */
	protected $total = '0';
	/**
	 * 字符型数字,当前page
	 *
	 * @var string
	 */
	protected $page = '1';
	/**
	 * 字符型数字,总条数
	 *
	 * @var string
	 */
	protected $count = '0';
	/**
	 * 字符型数字,每页显示的条数
	 *
	 * @var string
	 */
	protected $per = '0';

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		empty($this->total) && $this->total = '0';
		empty($this->page) && $this->page = '1';
		empty($this->count) && $this->count = '0';
		empty($this->per) && $this->per = '0';
		empty($this->url) && $this->url = '';
		$_return = array();
		$_return[] = '<?php $__tplPageCount=(int)' . $this->count . ';';
		$_return[] = '$__tplPagePer=(int)' . $this->per . ';';
		$_return[] = '$__tplPageTotal=(int)' . $this->total . ';';
		$_return[] = '$__tplPageCurrent=(int)' . $this->page . ';';
		$_return[] = '$__tplPageUrl="' . $this->url . '";';
		$_return[] = 'if($__tplPageCount > 0 && $__tplPagePer > 0){';
		$_return[] = '$__tplPageTotal = ceil($__tplPageCount / $__tplPagePer);}';
		$_return[] = '$__tplPageCurrent > $__tplPageTotal && $__tplPageCurrent = $__tplPageTotal;';
		$_return[] = 'if ($__tplPageTotal > 1) {?>';
		$_return[] = $this->getTplContent();
		$_return[] = '<?php } ?>';
		return implode("\r\n", $_return);
	}

	/**
	 * 获得page页模板内容
	 *
	 * @return string|mixed
	 */
	private function getTplContent() {
		if (!$this->tpl) return '';
		list(, $content) = $this->windViewerResolver->compile($this->tpl, '', true);
		$arrPageTags = array('$total', '$page', '$url', '$count');
		$arrPageVars = array(
			'$__tplPageTotal', 
			'$__tplPageCurrent', 
			'$__tplPageUrl', 
			'$__tplPageCount');
		return str_ireplace($arrPageTags, $arrPageVars, $content);
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('tpl', 'total', 'page', 'per', 'count', 'url');
	}
}

?>