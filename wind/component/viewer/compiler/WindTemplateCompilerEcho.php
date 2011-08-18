<?php
Wind::import('COM:viewer.AbstractWindTemplateCompiler');
Wind::import('COM:utility.WindHtmlHelper');
/**
 * 变量输出解析 
 * 变量名称|变量格式（html，text）
 * {$var|html}   //不执行编译
 * {@$var->a|text}  //执行编译
 * {@templateName:var|html}   
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
		$_output = preg_replace(array('/^[\n\s{\@]+/i', '/[\n\s}\;]+$/i'), array('', ''), $content);
		list($_output, $type) = $this->doCompile($_output);
		if ($this->windViewerResolver->getWindView()->htmlspecialchars === true && $type == 'text')
			return '<?php echo WindHtmlHelper::encode(' . $_output . ');?>';
		else
			return '<?php echo ' . $_output . ';?>';
	}

	/**
	 * 处理变量
	 * @param string $var
	 * @return array
	 */
	private function doCompile($var) {
		$vars = explode('|', $var, 2);
		$type = isset($vars[1]) ? strtolower($vars[1]) : 'text';
		!in_array($type, $this->getType()) && $type = 'text';
		return array($this->compileVarShare($vars[0]), $type);
	}

	/**
	 * @param string $input
	 * @return string
	 */
	private function compileVarShare($input) {
		if (strpos($input, '$') !== false || strpos($input, '::') !== false || strpos($input, ':') === false)
			return $input;
		list($templateName, $var) = explode(':', $input);
		return 'Wind::getApp()->getResponse()->getData(\'' . $templateName . '\', \'' . $var . '\')';
	}

	/**
	 * 获得变量后的|之后的值
	 * @return array
	 */
	private function getType() {
		return array('html', 'text');
	}
}
?>