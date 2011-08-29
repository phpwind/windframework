<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerCss extends AbstractWindTemplateCompiler {

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		foreach ($this->windViewTemplate->getCompiledBlockData() as $key => $value) {
			$content = str_replace('#' . $key . '#', ($value ? $value : ' '), $content);
		}
		$this->windViewerResolver->getWindLayout()->setcss($content);
		return '';
	}
}

?>