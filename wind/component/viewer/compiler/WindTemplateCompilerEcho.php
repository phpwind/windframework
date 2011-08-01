<?php
Wind::import('COM:viewer.AbstractWindTemplateCompiler');
Wind::import('COM:utility.WindHtmlHelper');
/**
 * 变量输出解析
 * {$var|false}
 * {@$var->a|false}
 * {@templateName:var}
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
		$_output = $content;
		$_output = preg_replace(array('/^[\n\s{\@]+/i', '/[\n\s}\;]+$/i'), array('', ''), $_output);
		$_output = $this->compileVarShare($_output);
		if ($this->windViewerResolver->getWindView()->getHtmlspecialchars() === true)
			return '<?php echo WindHtmlHelper::encode(' . $_output . ');?>';
		else
			return '<?php echo ' . $_output . ';?>';
	}

	/**
	 * @param string $input
	 * @return string
	 */
	private function compileVarShare($input) {
		$input = trim($input);
		if (strpos($input, '$') !== false || strpos($input, '::') !== false || strpos($input, ':') === false) return $input;
		list($templateName, $var) = explode(':', $input);
		$input = '$this->getResponse()->getData(\'' . $templateName . '\', \'' . $var . '\')';
		return $input;
	}

}

?>