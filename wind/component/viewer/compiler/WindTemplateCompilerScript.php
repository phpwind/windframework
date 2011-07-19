<?php
Wind::import('COM:viewer.AbstractWindTemplateCompiler');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerScript extends AbstractWindTemplateCompiler {

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		$content = preg_replace_callback('/{\s*\$(\w)+((\-\>\w+)*(\(.*\))*(\[.*\])*)*?[;\s]*}/i', array($this, 
			'doCompile'), $content);
		return $content;
	}

	/**
	 * 编译匹配到的结果
	 * @param string $content
	 */
	public function doCompile($content) {
		if (empty($content))
			return '';
		$_output = preg_replace(array('/^[\n\s{]+/i', '/[\n\s}\;]+$/i'), array('', ''), $content[0]);
		return '<?php echo ' . $_output . ';?>';
	}

}

?>