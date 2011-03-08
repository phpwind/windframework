<?php

L::import('WIND:core.viewer.AbstractWindTemplateCompiler');

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerEcho extends AbstractWindTemplateCompiler {

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		//TODO 支持模板变量标签{@header:a}
		$_output = $content;
		$_output = preg_replace(array('/^[\n\s{\@]+/i', '/[\n\s}\;]+$/i'), array('', ''), $_output);
		return '<?php echo ' . $_output . ';?>';
	}

}

?>