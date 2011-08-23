<?php
Wind::import('COM:viewer.AbstractWindTemplateCompiler');
/**
 * <template source='' suffix='' load='false' />
 * source: 模板文件源地址
 * suffix: 模板文件后缀
 * load: 是否将编译内容加载到本模板中
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerTemplate extends AbstractWindTemplateCompiler {
	
	protected $source = '';
	
	protected $suffix = '';
	
	protected $load = 'true';
	
	protected $getVar = true;

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		if (!isset($this->source))
			return $content;
			/*preg_match('/^{?\$(\w+)}?$/Ui', trim($this->source), $result);
		if (!empty($result)) { 
			$_tpl = $this->windViewerResolver->getWindView()->templateName;
			$this->source = Wind::getApp()->getResponse()->getData($_tpl, $result[1]);
		}*/
		//TODO 暂时不支持 load 参数 默认全部以load模式加载子模板
		if ($this->load === 'false') {
			list($compileFile) = $this->windViewerResolver->compile($this->source, $this->suffix);
			$content = '<?php include(\'' . addslashes($compileFile) . '\'); ?>';
		} else {
			list(, $content) = $this->windViewerResolver->compile($this->source, $this->suffix, 
				true);
		}
		return $content;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('source', 'suffix', 'load');
	}

}

?>