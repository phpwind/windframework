<?php
Wind::import('COM:viewer.AbstractWindTemplateCompiler');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerScript extends AbstractWindTemplateCompiler {
	protected $compile = 'true';

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		if ($this->compile === 'false') return $content;
		foreach ($this->windViewTemplate->getCompiledBlockData() as $key => $value) {
			$content = str_replace('#' . $key . '#', ($value ? $value : ' '), $content);
		}
		$this->windViewerResolver->getWindLayout()->setScript($content);
		return '';
	}

	/**
	 * 返回该标签支持的属性信息
	 */
	protected function getProperties() {
		return array('compile');
	}

}

?>