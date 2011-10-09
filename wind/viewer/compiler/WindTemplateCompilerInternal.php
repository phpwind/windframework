<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package viewer
 * @subpackage compiler
 */
class WindTemplateCompilerInternal extends AbstractWindTemplateCompiler {

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		return $content;
	}

}

?>