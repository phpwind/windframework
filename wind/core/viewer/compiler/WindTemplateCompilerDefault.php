<?php

L::import('WIND:core.viewer.compiler.AbstractWindTemplateCompiler');

/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerDefault extends AbstractWindTemplateCompiler {

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile() {
		$_output = $this->tagContent;
		$_output = preg_replace(array('/{\s*/i', '/\s*}/i'), array(' ', ''), $_output);
		return $_output;
	}

}

?>